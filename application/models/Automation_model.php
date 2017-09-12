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
        print "Leagueid: ".$leagueid."\n";
        $week_year = $this->common_noauth_model->get_current_week($leagueid);
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
                echo "WW disabled today.";
                continue;
            }

            // Check to see if waiver_wire_disable_gt is enabled and if player is locked
            if ($approval_settings->waiver_wire_disable_gt)
            {
                if ($this->common_waiverwire_model->is_player_locked($r->player_id, $week_year->year, $week_year->week, $week_type, $leagueid))
                {
                    print "Player locked. ".$r->player_id."\n";
                    continue;
                }
            }

            $priority = array();

            $temp = $this->common_waiverwire_model->get_ww_priority_data_array($leagueid, $week_year->year, $week_type);
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
                continue;
            }

            $teams = $this->db->select('team_id, id as ww_id')->from('waiver_wire_log')->where('league_id',$leagueid)
                ->where('pickup_player_id',$r->player_id)
                ->where('transaction_date',0)->where('approved',0)->get()->result();

            // If there is only 1 team, they will be the winner anyway.
            $ww_winner = array('priority' => PHP_INT_MAX);

            foreach ($teams as $t)
            {
                if ($priority[$t->team_id]['priority'] < $ww_winner['priority'])
                {
                    $ww_winner['priority'] = $priority[$t->team_id]['priority'];
                    $ww_winner['data'] = $t;
                }
            }

            $priority_used = false;
            if (count($teams > 1))
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
            // Here is where you would adjust the priority, if you were keeping track of it.

        }
    }
}
?>
