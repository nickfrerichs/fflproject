<?php

class Automation_model extends CI_Model{

    function __construct()
    {
        parent::__construct();
        $this->load->model('common/common_noauth_model');
        $this->load->model('common/common_waiverwire_model');
    }

    function approve_waiver_wire_requests($leagueid)
    {
        $week_year = $this->common_noauth_model->get_current_week_year($leagueid);
        if (!$week_year)
            return;
        $week_type = $this->common_noauth_model->get_week_type($leagueid);

        $approval_settings = $this->common_waiverwire_model->get_approval_settings($leagueid);
        $approval_type = $approval_settings->type;
        $last_check = $approval_settings->last_check;
        $admin_notified = false;
        if ($approval_type == "manual")
        {
            return;
        }

        $now = t_mysql();

        // Get all players who have outstanding approvals
        $clear_time = $this->common_waiverwire_model->get_ww_clear_time($leagueid);
        $result = $this->db->select('distinct pickup_player_id as player_id',false)
            ->from('waiver_wire_log')
            ->where('transaction_date',0)->where('approved',0)
            ->where('waiver_wire_log.year',$week_year->year)
            ->group_by('player_id')
            ->get()->result();

        // Check the pickup player for each approval.
        foreach ($result as $r)
        {
            // Check to see if waiver_wire_disable_days and is disabled today
            if ($this->common_waiverwire_model->today_is_disabled_day($leagueid))
            {
                echo "WW disabled today.\n";
                continue;
            }

            // Check to see if waiver_wire_disable_gt is enabled and if player is locked
            if ($approval_settings->waiver_wire_disable_gt)
            {
                if ($this->common_waiverwire_model->is_player_locked($r->player_id, $week_year->year, $week_year->week, $week_type, $leagueid))
                {
                    echo "Player locked, is already active this week. ".$r->player_id."\n";
                    continue;
                }
            }

            $priority = array();

            $temp = $this->common_waiverwire_model->get_ww_priority_data_array($leagueid, $week_year->year, $week_type, $week_year->week);

            foreach($temp['priority'] as $p => $t)
            {
                $priority[$t->team_id]['data'] = $t;
                $priority[$t->team_id]['priority'] = $p;
            }

            // Check if the clear_time has passed since when this player was dropped,
            $dropped_time = $this->db->select('UNIX_TIMESTAMP(transaction_date) as transaction_date')
                ->from('waiver_wire_log')->where('league_id',$leagueid)->where('drop_player_id',$r->player_id)
                ->where('transaction_date!=',0)->where('approved',1)->order_by('transaction_date','desc')
                ->limit(1)->get()->row()->transaction_date;

            // clear time has not passed, keep waiting.
            if ($dropped_time+$clear_time >= time())
            {
                echo "Waivers have not cleared for ".$r->player_id."\n";
                continue;
            }
            

            // Teams attempting to claim this pickup_player_id
            $teams = $this->db->select('team_id, id as ww_id')->from('waiver_wire_log')->where('league_id',$leagueid)
                ->where('pickup_player_id',$r->player_id)
                ->where('transaction_date',0)->where('approved',0)->get()->result();

            $ww_winner = array('priority' => PHP_INT_MAX);
            foreach ($teams as $t)
            {
                // To complicate this further, a team could be invovled in more than one player contention and we need to decide
                // which one they want to burn their priority on

                if ($this->team_pass_on_player($t->team_id,$r->player_id,$leagueid, $week_year, $week_type))
                    continue;
                
                if ($priority[$t->team_id]['priority'] < $ww_winner['priority'])
                {
                    $ww_winner['priority'] = $priority[$t->team_id]['priority'];
                    $ww_winner['data'] = $t;
                }
            }

            $priority_used = false;
            if (count($teams) > 1)
                $priority_used = true;

            // If contention and semi automation, time to exit.
            if ($approval_type == "semiauto" && count($teams) > 1)
            {
                // An email should be sent here to the league admin, but only one?
                if (!$admin_notified && $last_check < time()-(60*60))
                {
                    echo "Sending admin notice\n";
                    $this->common_waiverwire_model->update_last_check($leagueid);
                    $this->common_waiverwire_model->send_admin_approval_notice($leagueid);
                    $admin_notified = true;
                }
                continue;
            }


            // Approve the WW for this team, after checking that they still have the drop player,
            // If not, reject the transaction, email them either way.

            if ($this->common_waiverwire_model->admin_ok_to_process_transaction($ww_winner['data']->ww_id, $msg))
            {

                $log = $this->db->select('pickup_player_id, drop_player_id, team_id, id')->from('waiver_wire_log')
                    ->where('id',$ww_winner['data']->ww_id)->get()->row();
                // Process the transaction for the winner
                $this->common_waiverwire_model->drop_player($log->drop_player_id, $log->team_id, $week_year->year, $week_year->week, $week_type);
                $this->common_waiverwire_model->pickup_player($log->pickup_player_id, $log->team_id, $leagueid);

                // Update the log
                $data = array('transaction_date' => $now, 'approved' => 1, 'transaction_week' => $week_year->week, 'priority_used' => $priority_used);
                $this->db->where('id',$ww_winner['data']->ww_id);
                $this->db->update('waiver_wire_log',$data);

                $this->common_waiverwire_model->send_email_notice($ww_winner['data']->ww_id,'approved');

                // Deny all other open approvals waiting for this player.
                $rows = $this->db->select('id')->from('waiver_wire_log')->where('pickup_player_id',$log->pickup_player_id)
                    ->where('league_id',$leagueid)->where('team_id != ',$log->team_id)
                    ->where('transaction_date',0)->where('approved',0)->get()->result();

                foreach($rows as $row)
                {
                    $data = array('transaction_date'=>$now, 'approved' => 0, 'transaction_week' => $week_year->week);
                    $this->db->where('id',$row->id);
                    $this->db->update('waiver_wire_log',$data);
                    $this->common_waiverwire_model->send_email_notice($row->id,'priority');
                }
            }
            else
            {
                // Something is up, cancel the winners request and email them.
                $this->common_waiverwire_model->cancel_request($ww_winner['data']->ww_id, $ww_winner['data']->team_id);
                $this->common_waiverwire_model->send_email_notice($ww_winner['data']->ww_id,"rejected");

                // maybe call this function recursively to move on to next winner??
            }
        }
    }

    // Check to see if team is involved in multiple contentions, if so decide if it should pass
    // on the current player
    function team_pass_on_player($team_id, $player_id,$leagueid, $week_year, $week_type)
    {
        // Find out what "wins" this team has in all existing contentions

        // First get an array of pickup_ids for the team, order by request date for preference
        $team_pickups = array();
        $temp = $this->db->select('pickup_player_id')->from('waiver_wire_log')->where('team_id',$team_id)->where('approved',0)
            ->where('transaction_date','0000-00-00 00:00:00')->order_by('request_date','desc')->get()->result();
        foreach($temp as $t)
            $team_pickups[] = $t->pickup_player_id;

        // Get priority array
        $priority_lookup = array();
        $temp = $this->common_waiverwire_model->get_ww_priority_data_array($leagueid, $week_year->year, $week_type);
        foreach($temp['priority'] as $priority => $t)
            $priority_lookup[$t->team_id] = $priority;

        // Get all players under contention (even those not involving this team)
        $contention_players = array();
        $temp = $this->db->select('pickup_player_id, count(pickup_player_id) as pickups')->from('waiver_wire_log')->where('league_id',$leagueid)
            ->where('approved',0)->where('transaction_date','0000-00-00 00:00:00')->group_by('pickup_player_id')->having('pickups>',1)->get()->result();
        foreach($temp as $t)
            $contention_players[] = $t->pickup_player_id;

        // Go through all of this team's pickup_ids.  Find out which ones it will win as current priority stands (can't go down in priority)
        $wins = array();
        foreach($team_pickups as $p)
        {
            $teams = $this->db->select('team_id, id as ww_id')->from('waiver_wire_log')->where('league_id',$leagueid)
            ->where('pickup_player_id',$p)->where('transaction_date',0)->where('approved',0)->get()->result();

            // get all other teams also picking up this player
            
            // check the priority array to determine if we win over the other teams
            $winner = array('priority' => PHP_INT_MAX);
            foreach($teams as $t)
            {
                if ($priority_lookup[$t->team_id] < $winner['priority'])
                {
                    $winner['priority'] = $priority_lookup[$t->team_id];
                    $winner['team_id'] = $t->team_id;
                }

            }
            if ($winner['team_id'] == $team_id)
                $wins[] = $p;
        }

        // If it's the only win, we'll take it
        if (count($wins) <= 1)
            return False;

        // If we have more than one win and it's the highest priority, also take it
        if (count($wins) > 1 && $player_id == $team_pickups[0])
            return False;
 
        // If we have more than one win, but it's not the highest priority, wait
        return True;
    }

    function p($head, $arr)
    {
        echo $head."\n================\n";
        print_r($arr);
        echo "\n==============";
    }
}
?>
