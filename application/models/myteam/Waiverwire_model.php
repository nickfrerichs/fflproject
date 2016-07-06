<?php

class Waiverwire_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->current_weektype = $this->session->userdata('week_type');
        $this->leagueid = $this->session->userdata('league_id');
        $this->load->model('common/common_waiverwire_model');
    }

    function get_roster_data()
    {
    	return $this->db->select('player.first_name, player.last_name, player.short_name, player.id')
    		->select('IFNULL(nfl_team.club_id,"FA") as club_id, nfl_position.short_text as position',false)
    		->select('sum(fantasy_statistic.points)')
    		->from('roster')->join('player','player.id = roster.player_id')
    		->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
    		->join('fantasy_statistic','fantasy_statistic.player_id = player.id and fantasy_statistic.year = '.
                    $this->current_year.' and fantasy_statistic.league_id ='.$this->leagueid,'left')
    		->join('nfl_position','nfl_position.id = player.nfl_position_id')
    		->where('roster.team_id',$this->teamid)
    		->group_by('player.id')->order_by('nfl_position.display_order','asc')
    		->get()->result();
    }

    function get_nfl_players($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',$show_owned = false)
    {
        $pos_list = $this->common_model->league_nfl_position_id_array();

        $clear_time = $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->waiver_wire_clear_time;
        //echo $pos_list;
        if (count($pos_list) < 1)
            $pos_list = array(-1);
        $owned_list = $this->get_owned_players_array();

        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id, player.first_name, player.last_name, player.short_name')
                ->select('IFNULL(sum(fantasy_statistic.points),0) as points',false)
                ->select('nfl_position.short_text as position')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('UNIX_TIMESTAMP(wwlog_drop.transaction_date)+'.$clear_time.' as clear_time')
                ->select('IF(wwlog_request.approved=0,1,0) as requested')
                ->from('player')
                ->join('fantasy_statistic','fantasy_statistic.player_id = player.id and fantasy_statistic.year = '.
                        $this->current_year.' and fantasy_statistic.league_id = '.$this->leagueid,'left')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
                ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
                ->join('waiver_wire_log as wwlog_drop','wwlog_drop.drop_player_id = player.id and wwlog_drop.league_id = '.
                        $this->leagueid.' and wwlog_drop.approved = 1 and UNIX_TIMESTAMP(transaction_date)>'.(time()-$clear_time),'left')
                ->join('waiver_wire_log as wwlog_request','wwlog_request.pickup_player_id = player.id and wwlog_request.team_id = '.
                        $this->teamid.' and wwlog_request.approved = 0 and wwlog_request.transaction_date = 0', 'left')
                ->where('roster.id IS NULL',null,false)
                ->where_in('nfl_position_id', $pos_list);
        if (count($owned_list) > 0 && $show_owned == false)
            $this->db->where_not_in('player.id',$owned_list);
        $this->db->where('active', true);
        if ($search != '')
            $this->db->where('(`last_name` like "%'.$search.'%" or `first_name` like "%'.$search.'%")',NULL,FALSE);
        if (($nfl_pos != 0) && (is_numeric($nfl_pos)))
            $this->db->where('nfl_position.id', $nfl_pos);
        $this->db->group_by('player.id')
                ->order_by($order_by[0],$order_by[1])
                ->limit($limit, $start);
        $data = $this->db->get();

        $returndata['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $returndata['result'] = $data->result();
        return $returndata;
    }

    function nfl_players_count($nfl_pos = 0,$search = '')
    {
        $pos_list = $this->common_model->league_nfl_position_id_array();
        if (count($pos_list) < 1)
            $pos_list = array(-1);
        $owned_list = $this->get_owned_players_array();

        $this->db->select('player.id')
                ->from('player')
                ->join('fantasy_statistic','fantasy_statistic.player_id = player.id')
                ->where('fantasy_statistic.year',$this->current_year)
                ->where_in('nfl_position_id', $pos_list)
                ->where_not_in('player.id',$owned_list)
                ->where('active', true);
        if ($search != '')
            $this->db->where('(`last_name` like "%'.$search.'%" or `first_name` like "%'.$search.'%")',NULL,FALSE);
        if (($nfl_pos != 0) && (is_numeric($nfl_pos)))
            $this->db->where('nfl_position_id', $nfl_pos);
        $data = $this->db->group_by('player.id')->get();

        return $data->num_rows();
    }

    // function get_league_nfl_position_id_array()
    // {
    //     $data = $this->db->select('position.nfl_position_id_list')
    //             ->from('position')
    //             ->where('position.league_id', $this->leagueid)
    //             ->get();
    //     $pos_list = array();
    //
    //     foreach ($data->result() as $posrow)
    //         $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));
    //     return $pos_list;
    // }

    function get_owned_players_array()
    {
        $owned = array();
        $data = $this->db->select('player_id')->from('roster')->where('league_id',$this->leagueid)->get()->result();
        foreach($data as $row)
            $owned[] = $row->player_id;
        return $owned;
    }

    function drop_player($player_id, $teamid=0)
    {
        if ($teamid == 0)
            $teamid = $this->teamid;
        return $this->common_waiverwire_model->drop_player($player_id, $teamid, $this->current_year, $this->current_week, $this->week_type);

        // if ($player_id == 0)
        //     return;
        //
        // if ($teamid == 0)
        //     $teamid = $this->teamid;
        //
        // $gamestart = $this->common_model->player_game_start_time($player_id);
        // // Delete player from roster
        // $this->db->where('player_id',$player_id)
        //     ->where('team_id',$teamid)
        //     ->where('league_id',$this->leagueid)
        //     ->delete('roster');
        //
        // // Delete any starter rows for current team with this player
        // $this->db->where('player_id', $player_id)
        //         ->where('team_id', $teamid)
        //         ->where('league_id', $this->leagueid);
        // if ($gamestart > time()) // game is in the future, include this week
        //     $this->db->where('week >=', $this->current_week);
        // else // this week's game has started, don't drop from this weeks starting lineup
        //     $this->db->where('week >', $this->current_week);
        // $this->db->where('year', $this->current_year)
        //         ->delete('starter');
    }

    function pickup_player($player_id, $teamid = 0)
    {
        if ($teamid == 0)
            $teamid = $this->teamid;
        $this->common_waiverwire_model->pickup_player($player_id, $teamid, $this->leagueid);

        // $data['league_id'] = $this->leagueid;
        // $data['team_id'] = $teamid;
        // $data['player_id'] = $player_id;
        // $data['starting_position_id'] = 0;
        // $this->db->insert('roster',$data);
    }

    function waiverwire_open()
    {
        $deadline_open = $this->db->select('count(league_id) as count')->from('league_settings')->where('league_id',$this->leagueid)
                ->where('waiver_wire_deadline > CURRENT_TIMESTAMP()')->get()->row()->count;
        if ($deadline_open > 0)
            return True;
        return False;

    }


    function ok_to_process_transaction($pickup_id, $drop_id, &$ret="", &$status_code=False)
    {
        // Check waiverwire is open
        if (!$this->waiverwire_open())
            return False;

        // only dropping a player, it's ok no matter what.
        if ($pickup_id == 0 && $drop_id > 0)
            return True;

        // Check pick up player waivers are clear, do this first in case they want to be on the waiting list
        $clear_time = $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->waiver_wire_clear_time;
        $num = $this->db->from('waiver_wire_log')->where('league_id',$this->leagueid)->where('drop_player_id',$pickup_id)
            ->where('UNIX_TIMESTAMP(transaction_date)+'.$clear_time.'>'.time())->count_all_results();
        if($num >0)
        {
            $ret = "This player has not cleared waivers.";
            $status_code = 1;
            return False;
        }

        // Check if an approval is pending
        $num = $this->db->from('waiver_wire_log')->where('league_id',$this->leagueid)->where('pickup_player_id',$pickup_id)
            ->where('approved',0)->count_all_results();
        if($num > 0)
        {
            $ret = "The player you are picking up has a pending request that<br> needs to be resolved by the commissioner.";
            return False;
        }

        // Check roster limit
        $roster_num = $this->db->from('roster')->where('team_id',$this->teamid)->count_all_results();
        $roster_max = $this->get_roster_max();
        if ($roster_max != -1)
        {
            if (($drop_id == 0 && ($roster_max <= $roster_num)) || $roster_num > $roster_max)
            {
                $ret = "This will put your team over roster limit of ".$roster_max." players.  You'll have ".$roster_num;
                return False;
            }
        }

        // Check position limit, this is a tad complicated
        $pos_year = $this->common_model->league_position_year();
        $positions = $this->db->select('nfl_position_id_list, max_roster, text_id')->from('position')->where('league_id',$this->leagueid)
            ->where('position.year',$pos_year)->get()->result();
        $pickup_nfl_pos = $this->db->select('nfl_position_id')->from('player')->where('id',$pickup_id)->get()->row()->nfl_position_id;
        $temp = $this->db->select('nfl_position_id')->from('player')->where('id',$drop_id)->get()->row();
        if (count($temp) == 1)
            $drop_nfl_pos = $temp->nfl_position_id;
        else
            $drop_nfl_pos = 0;
        $pos_limit = True;
        $pos_limit_text = "";
        foreach ($positions as $p)
        {
            if ($p->max_roster == -1) // limit is 0
            {
                $pos_limit = False;
                break;
            }
            $p_array = explode(",",$p->nfl_position_id_list);
            // If the player being added has an NFL position that is part of this league position..
            if (in_array($pickup_nfl_pos,$p_array))
            {

                $pos_count = $this->db->from('roster')->join('player','player.id = roster.player_id')
                    ->where('roster.team_id',$this->teamid)
                    ->where_in('player.nfl_position_id',explode(",",$p->nfl_position_id_list))->count_all_results();

                // If the player being dropped is also part of this league position, subtract one from count.
                if (in_array($drop_nfl_pos,$p_array))
                    $pos_count--;

                // If position count after adding this picked up player doesn't put us over the limit, then we have
                // room for in at least one league position spot.  Set temp pos_limt variable to false and break out of loop
                if ($pos_count+1 <= $p->max_roster)
                {
                    $pos_limit = False;
                    break;
                }
                else
                {
                    $pos_limit = $p->max_roster;
                    $pos_limit_text = $p->text_id;
                }
            }

        }
        if ($pos_limit)
        {
            $ret = "You can only have " .$pos_limit." players on your roster at the ".$pos_limit_text." position.";
            return False;
        }

        // Check drop player is owned by this team
        $num = $this->db->from('roster')->where('league_id',$this->leagueid)
            ->where('player_id',$drop_id)->where('team_id',$this->teamid)->count_all_results();
        if($drop_id != 0 && $num==0)
        {
            $ret = "You don't own the player you are trying to drop.";
            return False;
        }


        // Check if pick up player is already on a team.
        $num = $this->db->from('roster')->where('player_id',$pickup_id)->where('league_id',$this->leagueid)->count_all_results();

        if ($num > 0)
        {
            $ret = "This player is already on another team.";
            return False;
        }

        // All checks passed, return True;
        return True;

    }

    // This function is used when waivers have not yet cleared.
    function request_player($pickup_id, $drop_id)
    {
        $now = t_mysql();
        $data = array('team_id' => $this->teamid,
                      'league_id' => $this->leagueid,
                      'pickup_player_id' => $pickup_id,
                      'drop_player_id' => $drop_id,
                      'transaction_date' => '0000-00-00',
                      'request_date' => $now,
                      'year' => $this->current_year,
                      'approved' => 0);
        $this->db->insert('waiver_wire_log', $data);
    }

    function cancel_request($id)
    {
        $this->common_waiverwire_model->cancel_request($id, $this->teamid);

        // $now = t_mysql();
        // $data = array('transaction_date' => $now,
        //       'approved' => 0);
        // $this->db->where('team_id',$this->teamid)->where('id',$id);
        // $this->db->update('waiver_wire_log',$data);
    }

    function log_transaction($pickup_id, $drop_id)
    {
        $now = t_mysql();
        $data = array('team_id' => $this->teamid,
                      'league_id' => $this->leagueid,
                      'pickup_player_id' => $pickup_id,
                      'drop_player_id' => $drop_id,
                      'transaction_date' => $now,
                      'request_date' => $now,
                      'year' => $this->current_year,
                      'approved' => 1);

        $this->db->insert('waiver_wire_log',$data);

        // Post to twitter, if league setting is enabled.
        $twitteron = $this->db->select('twitter_player_moves')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->twitter_player_moves;
        if ($twitteron)
        {
            $text = 'WAIVERS: '.$this->session->userdata('chat_name').' ';
            $this->load->model('league/player_statistics_model');
            $this->load->model('common/common_model');
            $pickup_player = $this->player_statistics_model->get_player_data($pickup_id);
            $drop_player = $this->player_statistics_model->get_player_data($drop_id);

            if (count($pickup_player) == 1)
                $text.= 'PICKUP: ('.$pickup_player->pos.' - '.$pickup_player->club_id.') '.$pickup_player->first_name.' '.$pickup_player->last_name.' ';
            else
                $text.= 'PICKUP: No One ';

            if (count($drop_player) == 1)
                $text.= 'DROP: ('.$drop_player->pos.' - '.$drop_player->club_id.') '.$drop_player->first_name.' '.$drop_player->last_name.' ';
            else
                $text.= 'DROP: No One ';

            $this->common_model->twitter_post($text);

        }
    }

    function get_log_data($year = 0, $limit = 100000, $start = 0, $days=0)
    {
        if ($year == 0)
            $year = $this->current_year;

        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('d.first_name as drop_first, d.last_name as drop_last, d.short_name as drop_short_name, dt.club_id as drop_club_id')
            ->select('dp.short_text as drop_pos, p.first_name as pickup_first, p.last_name as pickup_last, p.short_name as pickup_short_name')
            ->select('pt.club_id as pickup_club_id, pp.short_text as pickup_pos')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.request_date) as request_date')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.transaction_date) as transaction_date')
            ->select('team.team_name')
            ->select('owner.first_name as owner_first, owner.last_name as owner_last')
            ->from('waiver_wire_log')
            ->join('player as d','d.id = waiver_wire_log.drop_player_id','left')
            ->join('player as p','p.id = waiver_wire_log.pickup_player_id','left')
            ->join('nfl_position as dp','dp.id = d.nfl_position_id','left')
            ->join('nfl_position as pp','pp.id = p.nfl_position_id','left')
            ->join('nfl_team as dt','dt.id = d.nfl_team_id','left')
            ->join('nfl_team as pt','pt.id = p.nfl_team_id','left')
            ->join('team','team.id = waiver_wire_log.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->where('waiver_wire_log.league_id',$this->leagueid)
            ->where('waiver_wire_log.transaction_date !=','00-00-00 00:00:00')
            ->where('waiver_wire_log.approved',1);
            if ($days != 0)
                $this->db->where('waiver_wire_log.transaction_date > date_sub(now(), INTERVAL '.$days.' day)');
            $this->db->limit($limit, $start);
        $return['result'] = $this->db->order_by('transaction_date','desc')
            ->get()->result();
        $return['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        return $return;
    }

    function get_clear_time()
    {
        return time() - $this->db->select('waiver_wire_clear_time')->from('league_settings')
            ->where('league_id',$this->leagueid)->get()->row()->waiver_wire_clear_time;
    }

    function get_roster_max()
    {
        return $this->db->select('roster_max')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->roster_max;
    }

    function get_priority_data_array()
    {

        return $this->common_waiverwire_model->get_ww_priority_data_array($this->leagueid, $this->current_year, $this->current_weektype);
        // $data = array();
        // $standings = $this->db->select('team.team_name, owner.first_name, owner.last_name')
        //     ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
        //     ->select('(sum(win=1)+(sum(tie=1)/2))/count(schedule_result.id) as winpct')
        //     ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
        //     ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
        //     ->select('count(schedule_result.id) as total_games')
        //     ->from('schedule')
        //     ->join('schedule_result','schedule_result.schedule_id = schedule.id')
        //     ->join('team','team.id = schedule_result.team_id')
        //     ->join('owner','owner.id = team.owner_id')
        //     ->join('nfl_week_type','nfl_week_type.id = schedule.nfl_week_type_id')
        //     ->where('schedule.year',$this->current_year)
        //     ->where('nfl_week_type.text_id',$this->current_weektype)
        //     ->group_by('team.id')
        //     ->order_by('winpct','asc')
        //     ->order_by('points','asc')
        //     ->order_by('opp_points','desc')
        //     ->get()->result();
        //
        //
        // $draft_order = $this->db->select('team.team_name, owner.first_name, owner.last_name')
        //         ->from('draft_order')
        //         ->join('team','team.id = draft_order.team_id')
        //         ->join('owner','owner.id = team.owner_id')
        //         ->where('draft_order.round',1)
        //         ->where('draft_order.league_id',$this->leagueid)
        //         ->where('draft_order.year',$this->current_year)
        //         ->order_by('draft_order.pick','desc')
        //         ->get()->result();
        //
        // $data['priority'] = array();
        // if(count($standings) == 0)
        // {
        //     $data['type'] = 'draft_order';
        //     foreach($draft_order as $key => $d)
        //         $data['priority'][$key] = $d;
        // }
        // else
        // {
        //     $data['type'] = 'standings';
        //     foreach($standings as $key => $s)
        //         $data['priority'][$key] = $s;
        // }
        //
        // return $data;

    }

    function get_pending_requests()
    {

        $clear_time = $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->waiver_wire_clear_time;

        return $this->db->select('pp.first_name as p_first, pp.last_name as p_last, dp.first_name as d_first, dp.last_name as d_last')
            ->select('team.team_name, owner.first_name as o_first, owner.last_name as o_last')
            ->select('dt.club_id as d_club_id, pt.club_id as p_club_id')
            ->select('dpos.text_id as d_pos, ppos.text_id as p_pos')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.request_date) as request_date')
            ->select('IFNULL(UNIX_TIMESTAMP(wwlog_drop.transaction_date)+'.$clear_time.',0) as clear_time')
            ->select('waiver_wire_log.id as ww_id')
            ->from('waiver_wire_log')
            ->join('player as dp','dp.id = drop_player_id')
            ->join('player as pp','pp.id = pickup_player_id')
            ->join('team','team.id = waiver_wire_log.team_id')
            ->join('owner','team.owner_id = owner.id')
            ->join('nfl_team as dt','dt.id = dp.nfl_team_id')
            ->join('nfl_team as pt','pt.id = pp.nfl_team_id')
            ->join('nfl_position as dpos','dpos.id = dp.nfl_position_id')
            ->join('nfl_position as ppos','ppos.id = pp.nfl_position_id')
            ->join('waiver_wire_log as wwlog_drop','wwlog_drop.drop_player_id = waiver_wire_log.pickup_player_id and wwlog_drop.league_id = '.
                    $this->leagueid.' and wwlog_drop.approved = 1 and UNIX_TIMESTAMP(wwlog_drop.transaction_date)>'.(time()-$clear_time),'left')
            ->where('waiver_wire_log.league_id',$this->leagueid)->where('waiver_wire_log.approved',0)->where('waiver_wire_log.transaction_date',0)
            ->where('waiver_wire_log.team_id',$this->teamid)
            ->where('waiver_wire_log.year',$this->current_year)
            ->order_by('request_date','desc')
            ->get()->result();

    }

}
