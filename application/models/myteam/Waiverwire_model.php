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

    // function get_nfl_players_old($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',$show_owned = false)
    // {
    //     $pos_list = $this->common_model->league_nfl_position_id_array();

    //     $clear_time = $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$this->leagueid)
    //         ->get()->row()->waiver_wire_clear_time;
    //     //echo $pos_list;
    //     if (count($pos_list) < 1)
    //         $pos_list = array(-1);
    //     $owned_list = $this->get_owned_players_array();

    //     $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
    //     $this->db->select('player.id, player.first_name, player.last_name, player.short_name')
    //             ->select('IFNULL(sum(fantasy_statistic.points),0) as points',false)
    //             ->select('nfl_position.short_text as position')
    //             ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
    //             ->select('UNIX_TIMESTAMP(wwlog_drop.transaction_date)+'.$clear_time.' as clear_time')
    //             ->select('IF(wwlog_request.approved=0,1,0) as requested')
    //             ->from('player')
    //             ->join('fantasy_statistic','fantasy_statistic.player_id = player.id and fantasy_statistic.year = '.
    //                     $this->current_year.' and fantasy_statistic.league_id = '.$this->leagueid,'left')
    //             ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
    //             ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
    //             ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
    //             ->join('waiver_wire_log as wwlog_drop','wwlog_drop.drop_player_id = player.id and wwlog_drop.league_id = '.
    //                     $this->leagueid.' and wwlog_drop.approved = 1 and UNIX_TIMESTAMP(transaction_date)>'.(time()-$clear_time),'left')
    //             ->join('waiver_wire_log as wwlog_request','wwlog_request.pickup_player_id = player.id and wwlog_request.team_id = '.
    //                     $this->teamid.' and wwlog_request.approved = 0 and wwlog_request.transaction_date = 0', 'left')
    //             ->where('roster.id IS NULL',null,false)
    //             ->where_in('nfl_position_id', $pos_list);
    //     if (count($owned_list) > 0 && $show_owned == false)
    //         $this->db->where_not_in('player.id',$owned_list);
    //     $this->db->where('active', true);
    //     if ($search != '')
    //         $this->db->where('(`last_name` like "%'.$search.'%" or `first_name` like "%'.$search.'%")',NULL,FALSE);
    //     if (($nfl_pos != 0) && (is_numeric($nfl_pos)))
    //         $this->db->where('nfl_position.id', $nfl_pos);
    //     $this->db->group_by('player.id')
    //             ->order_by($order_by[0],$order_by[1])
    //             ->limit($limit, $start);
    //     $data = $this->db->get();

    //     $returndata['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
    //     $returndata['result'] = $data->result();
    //     return $returndata;
    // }

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
                ->select('IFNULL(sum(fs_w.points),0) as points',false)
                ->select('nfl_position.short_text as position')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('UNIX_TIMESTAMP(wwlog_drop.transaction_date)+'.$clear_time.' as clear_time')
                ->select('IF(wwlog_request.approved=0,1,0) as requested')
                ->select('player_injury.injury, player_injury_type.text_id as injury_text_id,player_injury_type.short_text as injury_short_text')
                ->select('player_injury.id IS NOT NULL as injured',false)
                ->select('player_injury.week as injury_week')
                ->from('player')
                ->join('fantasy_statistic_week as fs_w','fs_w.player_id = player.id and fs_w.year = '.
                        $this->current_year.' and fs_w.league_id = '.$this->leagueid,'left')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
                ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
                ->join('waiver_wire_log as wwlog_drop','wwlog_drop.drop_player_id = player.id and wwlog_drop.league_id = '.
                        $this->leagueid.' and wwlog_drop.approved = 1 and UNIX_TIMESTAMP(transaction_date)>'.(time()-$clear_time),'left')
                ->join('waiver_wire_log as wwlog_request','wwlog_request.pickup_player_id = player.id and wwlog_request.team_id = '.
                        $this->teamid.' and wwlog_request.approved = 0 and wwlog_request.transaction_date = 0', 'left')
                ->join('player_injury','player_injury.player_id = player.id','left')
                ->join('player_injury_type','player_injury_type.id = player_injury.player_injury_type_id','left')
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
                ->order_by('player.last_name','asc')
                ->order_by('player.id','asc')
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
    }

    function pickup_player($player_id, $teamid = 0)
    {
        if ($teamid == 0)
            $teamid = $this->teamid;
        $this->common_waiverwire_model->pickup_player($player_id, $teamid, $this->leagueid);
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
        $settings = $this->common_waiverwire_model->get_approval_settings($this->leagueid);
        // Check waiverwire is open
        if (!$this->waiverwire_open())
            return False;

        // only dropping a player, it's ok no matter what.
        if ($pickup_id == 0 && $drop_id > 0)
            return True;


        // 1. Check roster limit
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

        // 2. Check position limit, this is a tad complicated
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

        // 3. Check drop player is owned by this team
        $num = $this->db->from('roster')->where('league_id',$this->leagueid)
            ->where('player_id',$drop_id)->where('team_id',$this->teamid)->count_all_results();
        if($drop_id != 0 && $num==0)
        {
            $ret = "You don't own the player you are trying to drop.";
            return False;
        }


        // 4. Check if pick up player is already on a team.
        $num = $this->db->from('roster')->where('player_id',$pickup_id)->where('league_id',$this->leagueid)->count_all_results();

        if ($num > 0)
        {
            $ret = "This player is already on another team.";
            return False;
        }


        // 5a. Check if waiver wire disable gt is, return status 1 if so..queue the request
        if ($this->common_waiverwire_model->is_player_locked($pickup_id))
        {
            $ret = "Request will be queued until week is complete, requested player's game has already started.";
            $status_code = 1;
            return False;
        }

        // 5b. If ww disable GT is enabled and player being dropped is lockeed AND was a starter
        $started = $this->db->from('starter')->where('league_id',$this->leagueid)->where('team_id',$this->teamid)
            ->where('week',$this->current_week)->where('year',$this->current_year)->where('player_id',$drop_id)
            ->count_all_results();
        if($started > 0)
        {
            if($this->common_waiverwire_model->is_player_locked($drop_id))
            {
                $ret = "Request will be queued until week is complete, drop player is a starter and his game has started.";
                $status_code = 1;
                return False;
            }
        }

        // 6. Check if waiver_wire_disable_day is on and disabled for current day.
        if ($settings->waiver_wire_disable_days != "")
        {
            $day_of_week = date('w',time());  //0=sunday, 6=saturday
            $days = str_split($settings->waiver_wire_disable_days);
            if (in_array($day_of_week,$days))
            {
                $ret = "Request will be queued, waiver wire is not available today.";
                $status_code = 1;
                return False;
            }
        }

        // 7. Check pick up player waivers are cleared, if they haven't return status 1, it's not a complete failure
        $clear_time = $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->waiver_wire_clear_time;
        $num = $this->db->from('waiver_wire_log')->where('league_id',$this->leagueid)->where('drop_player_id',$pickup_id)
            ->where('UNIX_TIMESTAMP(transaction_date)+'.$clear_time.'>'.time())->count_all_results();
        if($num >0)
        {
            $ret = "Request will be queued, you'll be notified once the player clears waivers.";
            $status_code = 1;
            return False;
        }

        // 8. If manual approvals, check if an approval is pending.
        if($settings->type != "manual")
        {
            $num = $this->db->from('waiver_wire_log')->where('league_id',$this->leagueid)->where('pickup_player_id',$pickup_id)
            ->where('transaction_date',0)->count_all_results();
            if ($num > 0)
            {
                $ret = "The player you are picking up has a pending request that<br> needs to be resolved by the league admin.";
                return False;
            }
        }

        // 8. All checks passed, return True;
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
    }

    function log_transaction($pickup_id, $drop_id, $approve = true)
    {
        $request_date = t_mysql();
        $trans_date = "0000-00-00 00:00:00";
        if ($approve)
            $trans_date = $request_date;

        $data = array('team_id' => $this->teamid,
                      'league_id' => $this->leagueid,
                      'pickup_player_id' => $pickup_id,
                      'drop_player_id' => $drop_id,
                      'transaction_date' => $trans_date,
                      'request_date' => $request_date,
                      'year' => $this->current_year,
                      'transaction_week' => $this->current_week,
                      'approved' => $approve);

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
        $this->db->select('d.first_name as drop_first, d.id as drop_id, d.last_name as drop_last, d.short_name as drop_short_name, dt.club_id as drop_club_id, d.photo as drop_photo')
            ->select('dp.short_text as drop_pos, p.id as pickup_id, p.first_name as pickup_first, p.last_name as pickup_last, p.short_name as pickup_short_name, p.photo as pickup_photo')
            ->select('pt.club_id as pickup_club_id, pt.team_name as pickup_club_name, dt.team_name as drop_club_name, pp.short_text as pickup_pos, pp.long_text as pickup_long_pos, dp.long_text as drop_long_pos')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.request_date) as request_date')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.transaction_date) as transaction_date')
            ->select('team.team_name')
            ->select('owner.first_name as owner_first, owner.last_name as owner_last')
            ->select('waiver_wire_log.priority_used')
            ->from('waiver_wire_log')
            ->join('player as d','d.id = waiver_wire_log.drop_player_id','left')
            ->join('player as p','p.id = waiver_wire_log.pickup_player_id','left')
            ->join('nfl_position as dp','dp.id = d.nfl_position_id','left')
            ->join('nfl_position as pp','pp.id = p.nfl_position_id','left')
            ->join('nfl_team as dt','dt.id = d.nfl_team_id','left')
            ->join('nfl_team as pt','pt.id = p.nfl_team_id','left')
            ->join('team','team.id = waiver_wire_log.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->where('waiver_wire_log.year',$year)
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
        return $this->common_waiverwire_model->get_ww_priority_data_array($this->leagueid, $this->current_year, $this->current_weektype, $this->current_week);
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
            ->join('player as dp','dp.id = drop_player_id','left')
            ->join('player as pp','pp.id = pickup_player_id','left')
            ->join('team','team.id = waiver_wire_log.team_id')
            ->join('owner','team.owner_id = owner.id')
            ->join('nfl_team as dt','dt.id = dp.nfl_team_id','left')
            ->join('nfl_team as pt','pt.id = pp.nfl_team_id','left')
            ->join('nfl_position as dpos','dpos.id = dp.nfl_position_id','left')
            ->join('nfl_position as ppos','ppos.id = pp.nfl_position_id','left')
            ->join('waiver_wire_log as wwlog_drop','wwlog_drop.drop_player_id = waiver_wire_log.pickup_player_id and wwlog_drop.league_id = '.
                    $this->leagueid.' and wwlog_drop.approved = 1 and UNIX_TIMESTAMP(wwlog_drop.transaction_date)>'.(time()-$clear_time),'left')
            ->where('waiver_wire_log.league_id',$this->leagueid)->where('waiver_wire_log.approved',0)->where('waiver_wire_log.transaction_date',0)
            ->where('waiver_wire_log.team_id',$this->teamid)
            ->where('waiver_wire_log.year',$this->current_year)
            ->order_by('request_date','desc')
            ->get()->result();

    }

    function make_preferred($ww_id)
    {
        $data = array('request_date' => t_mysql());
        $this->db->where('id',$ww_id)->where('team_id',$this->teamid)
            ->update('waiver_wire_log',$data);

    }

}
