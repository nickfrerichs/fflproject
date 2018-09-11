<?php

class Common_waiverwire_model extends CI_Model{

    // Methods in this class are used by:
    // - myteam/Waiverwire_model
    // - admin/Transactions_model
    // - Cli_model
    // I genrally don't access these methods directly from controllers,
    // but from other models.

    function __construct(){
        parent::__construct();

    }

    function get_ww_clear_time($leagueid)
    {
        return $this->db->select('waiver_wire_clear_time')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row()->waiver_wire_clear_time;
    }

    function get_ww_priority_data_array($leagueid, $year, $weektype, $week=0)
    {
        $data = array();
        $standings = $this->db->select('team.team_name, owner.first_name, owner.last_name, team.id as team_id')
            ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
            ->select('(sum(win=1)+(sum(tie=1)/2))/count(schedule_result.id) as winpct')
            ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
            ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
            ->select('count(schedule_result.id) as total_games')
            ->from('schedule')
            ->join('schedule_result','schedule_result.schedule_id = schedule.id')
            ->join('team','team.id = schedule_result.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->join('nfl_week_type','nfl_week_type.id = schedule.nfl_week_type_id')
            ->where('schedule.year',$year)
            ->where('nfl_week_type.text_id',$weektype)
            ->where('schedule.league_id',$leagueid)
            ->group_by('team.id')
            ->order_by('winpct','asc')
            ->order_by('points','asc')
            ->order_by('opp_points','desc')
            ->get()->result();


        $draft_order = $this->db->select('team.team_name, owner.first_name, owner.last_name, team.id as team_id')
                ->from('draft_order')
                ->join('team','team.id = draft_order.team_id')
                ->join('owner','owner.id = team.owner_id')
                ->where('draft_order.league_id',$leagueid)
                ->where('draft_order.year',$year)
                ->order_by('draft_order.overall_pick','asc')
                ->get()->result();

        $data['priority'] = array();
        if(count($standings) == 0)
        {
            $data['type'] = 'draft_order';
            $added = array();
            foreach($draft_order as $key => $d)
            {
                if (in_array($d->team_id,$added))
                    continue;
                $data['priority'][] = $d;
                $added[] = $d->team_id;
            }
            $data['priority'] = array_reverse($data['priority']);
        }
        else
        {
            $data['type'] = 'standings';
            foreach($standings as $key => $s)
                $data['priority'][$key] = $s;
        }

        if ($week > 0)
        {

            // get each users most recent "collision" win and move them to the end
            $priority_used = $this->db->select('team_id')->from('waiver_wire_log')->where('league_id',$leagueid)
                ->where('year',$year)->where('transaction_week',$week)->where('priority_used',1)
                ->order_by('transaction_date','asc')->get()->result();

            foreach($priority_used as $p)
            {
                foreach($data['priority'] as $priority => $team)
                {
                    if($p->team_id == $team->team_id)
                    {
                        unset($data['priority'][$priority]);
                        $data['priority'][]=$team;
                        $data['priority'] = array_values($data['priority']);
                    }

                }
            }
        }
        return $data;

    }

    function get_roster_max($leagueid)
    {
        return $this->db->select('roster_max')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row()->roster_max;
    }

    function admin_ok_to_process_transaction($id, &$ret)
    {

        $log = $this->db->select('league_id, pickup_player_id, team_id, drop_player_id')->from('waiver_wire_log')->where('id',$id)
            ->get()->row();
        $leagueid = $log->league_id;
        $team_id = $log->team_id;
        $pickup_id = $log->pickup_player_id;
        $drop_id = $log->drop_player_id;

        if ($pickup_id == 0 && $drop_id > 0)
            return True;

        // Check roster limit
        $roster_num = $this->db->from('roster')->where('team_id',$team_id)->count_all_results();
        $roster_max = $this->get_roster_max($leagueid);
        if ($roster_max != -1)
        {
            if (($drop_id == 0 && ($roster_max <= $roster_num)) || $roster_num > $roster_max)
            {
                $ret = "This will put the team over roster limit of ".$roster_max." players.  They'll have ".$roster_num;
                return False;
            }
        }



        $week_year = $this->common_noauth_model->get_current_week_year($leagueid);

        // Check position limit, this is a tad complicated
        $pos_year = $this->common_noauth_model->league_position_year($leagueid,$week_year->year);
        $positions = $this->db->select('nfl_position_id_list, max_roster, text_id')->from('position')->where('league_id',$leagueid)
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
                    ->where('roster.team_id',$team_id)
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
            $ret = "The team can only have " .$pos_limit." players on it's roster at the ".$pos_limit_text." position.";
            return False;
        }

        // Check drop player is owned by this team
        $num = $this->db->from('roster')->where('league_id',$leagueid)
            ->where('player_id',$drop_id)->where('team_id',$team_id)->count_all_results();
        if($drop_id != 0 && $num==0)
        {
            $ret = "The team no longer owns the player they are wanting to drop.";
            return False;
        }

        // Check if pick up player is already on a team.
        $num = $this->db->from('roster')->where('player_id',$pickup_id)->where('league_id',$leagueid)->count_all_results();

        if ($num > 0)
        {
            $ret = "This player is already on another team.";
            return False;
        }

        // All checks passed, return True;
        return True;

    }

    function drop_player($player_id, $teamid, $year, $week, $weektype)
    {
        $this->common_noauth_model->drop_player($player_id, $teamid, $year, $week, $weektype);
    }

    function pickup_player($player_id, $teamid = 0, $leagueid)
    {
        $this->common_noauth_model->add_player($player_id, $teamid, $leagueid = 0);
        // $data['league_id'] = $leagueid;
        // $data['team_id'] = $teamid;
        // $data['player_id'] = $player_id;
        // $data['starting_position_id'] = 0;
        // $this->db->insert('roster',$data);
        
        // Recalculate the bench
    }

    function cancel_request($id, $teamid, $week=0)
    {
        if ($week == 0)
            $week = $this->session->userdata('current_week');
        $now = t_mysql();
        $data = array('transaction_date' => $now,
              'approved' => 0,
              'transaction_week' => $week);
        $this->db->where('team_id',$teamid)->where('id',$id);
        $this->db->update('waiver_wire_log',$data);
    }

    function send_email_notice($id, $subject)
    {

        $data = $this->db->select('email as email_address')->from('waiver_wire_log')
            ->select('pp.first_name as p_first, pp.last_name as p_last')
            ->select('dp.first_name as d_first, dp.last_name as d_last')
            ->join('team','waiver_wire_log.team_id = team.id')
            ->join('owner','owner.id = team.owner_id')
            ->join('user_accounts','user_accounts.id = owner.user_accounts_id')
            ->join('player as pp','pp.id = waiver_wire_log.pickup_player_id','left')
            ->join('player as dp','dp.id = waiver_wire_log.drop_player_id','left')
            ->where('waiver_wire_log.id',$id)
            ->get()->row();

        if ($subject == "approved")
        {
            $subject = "Waiver wire request approved.";
            $body = "The following request has been processed:\n\n".
            "Added: ".$data->p_first.' '.$data->p_last."\n";
            if ($data->d_first == "" && $data->d_last == "")
                $body .= "Dropped: No one";
            else
                $body .= "Dropped: ".$data->d_first.' '.$data->d_last;
        }

        if ($subject == "priority")
        {
            $subject = "Waiver wire request rejected.";
            $body = "Your request for ".$data->p_first.' '.$data->p_last.
            ' was rejected.  A team with a higher priority requested the player.';
        }

        if ($subject == "rejected")
        {
            $subject = "Waiver wire request rejected.";
            $body = "Your request for ".$data->p_first.' '.$data->p_last.' was rejected.';
        }

        $body .= "\n\n--\nThis is an automated email.";

        $this->config->load('fflproject');
        $this->load->library('email');
        $this->email->from($this->config->item('fflp_email_reply_to'), $this->config->item('fflp_email_site_title'));
        $this->email->to($data->email_address);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();

    }

    function send_admin_approval_notice($leagueid)
    {
        $admins = $this->db->select('email as email')->from('league_admin')
            ->join('user_accounts','user_accounts.id = league_admin_id')
            ->where('league_admin.league_id',$leagueid)->get()->result();

        $league_name = $this->common_noauth_model->get_league_name($leagueid);

        $this->config->load('fflproject');
        $this->load->library('email');
        $this->email->from($this->config->item('fflp_email_reply_to'), $this->config->item('fflp_email_site_title'));
        $this->email->subject("Waiver wire request needs approval.");
        $this->email->message("Hi League Admin,\n\nA waiver wire request needs approval for league: ".$league_name.".");
        foreach($admins as $admin)
        {
            $this->email->to($admin->email);
            $this->email->send();
        }
    }

    function get_approval_settings($leagueid)
    {
        return $this->db->select('waiver_wire_approval_type as type, UNIX_TIMESTAMP(waiver_wire_approval_last_check) as last_check')
            ->select('waiver_wire_disable_gt, waiver_wire_disable_days')
            ->from('league_settings')->where('league_id',$leagueid)->get()->row();
    }

    function is_player_locked($player_id, $year=0, $week=0, $weektype="", $leagueid=0)
    {
        if ($year == 0)
            $year = $this->session->userdata('current_year');
        if ($week == 0)
            $week = $this->session->userdata('current_week');
        if ($weektype == "")
            $weektype = $this->session->userdata('week_type');
        if ($leagueid == 0)
            $leagueid = $this->session->userdata('league_id');

        $settings = $this->db->select('waiver_wire_disable_gt, waiver_wire_disable_days')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row();

        $current_time = time();
        if ($settings->waiver_wire_disable_gt)
        {
            $start_time  = $this->common_noauth_model->player_game_start_time($player_id,$year,$week,$weektype);
            //$end_time = $this->common_noauth_model->final_game_start_time($year,$week,$weektype);
            // If it's past the start time and 8 hours haven't passed since the final game started
            // if (($current_time > $start_time) && ($current_time < $end_time+(60*60*8)))
            // I no longer think I need to check the end_time... if a player is locked for the passed in week using waiver_wire_disable_gt, they
            // stay locked until we roll into the next week.

            // Player has a bye this week, not locked.
            if ($start_time == "")
                return False;

            if ($current_time > $start_time)
            {
                return True;
            }
        }

        // Checks passed, player not locked.
        return False;
        
    }

    function today_is_disabled_day($leagueid)
    {
        $settings = $this->db->select('waiver_wire_disable_gt, waiver_wire_disable_days')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row();
        if ($settings->waiver_wire_disable_days != "")
        {
            $day_of_week = date('w',time());  //0=sunday, 6=saturday
            $days = str_split($settings->waiver_wire_disable_days);
            if (in_array($day_of_week,$days))
                return True;
            
        }
        return False;
    }

    function update_last_check($leagueid)
    {
        $data = array('waiver_wire_approval_last_check' => t_mysql());
        $this->db->where('league_id',$leagueid);
        $this->db->update('league_settings',$data);
    }

}
?>
