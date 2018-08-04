<?php

class Transactions_model extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('common/common_waiverwire_model');
    }

    function get_pending_ww_approvals()
    {
        return $this->db->select('pp.first_name as p_first, pp.last_name as p_last, dp.first_name as d_first, dp.last_name as d_last')
            ->select('team.team_name, owner.first_name as o_first, owner.last_name as o_last')
            ->select('dt.club_id as d_club_id, pt.club_id as p_club_id')
            ->select('dpos.text_id as d_pos, ppos.text_id as p_pos')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.request_date) as request_date')
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
            ->where('waiver_wire_log.league_id',$this->leagueid)->where('approved',0)->where('transaction_date',0)
            ->order_by('request_date','desc')
            ->get()->result();
    }

    function reject_ww($id)
    {
        $now = t_mysql();
        $log = $this->db->select('pickup_player_id, team_id, drop_player_id')->from('waiver_wire_log')->where('id',$id)
            ->where('league_id',$this->leagueid)->get()->row();

        // Update the log
        $data = array('transaction_date' => $now, 'approved' => 0, 'transaction_week' => $this->current_week);
        $this->db->where('id',$id);
        $this->db->update('waiver_wire_log',$data);

        // Notify the owner via email.
        $this->common_waiverwire_model->send_email_notice($id,'rejected');

    }

    function approve_ww($id)
    {
        $result['success'] = False;
        $this->load->model('myteam/waiverwire_model');
        $now = t_mysql();
        $log = $this->db->select('pickup_player_id, team_id, drop_player_id')->from('waiver_wire_log')->where('id',$id)
            ->where('league_id',$this->leagueid)->get()->row();

        // confirm drop player is still available
        if ($this->ok_to_process_transaction($id, $msg))
        {
            // Deny all other open approvals waiting for this player.
            $rows = $this->db->select('id')->from('waiver_wire_log')->where('pickup_player_id',$log->pickup_player_id)
                ->where('league_id',$this->leagueid)->where('team_id != ',$log->team_id)
                ->where('transaction_date',0)->get()->result();

            foreach($rows as $row)
            {
                $data = array('transaction_date'=>$now, 'approved' => 0, 'transaction_week' => $this->current_week);
                $this->db->where('id',$row->id);
                $this->db->update('waiver_wire_log',$data);
                $this->common_waiverwire_model->send_email_notice($row->id,'rejected');
            }

            // Approve the transaction
            $this->waiverwire_model->drop_player($log->drop_player_id, $log->team_id);
            $this->waiverwire_model->pickup_player($log->pickup_player_id, $log->team_id);

            // Update the log
            $data = array('transaction_date' => $now, 'approved' => 1, 'transaction_week' => $this->current_week);
            $this->db->where('id',$id);
            $this->db->update('waiver_wire_log',$data);

            $result['success'] = True;

            // Notify the owner via email.
            $this->common_waiverwire_model->send_email_notice($id,'approved');

            return $result;
        }
        $result['msg'] = $msg;
        return $result;
    }

    function get_roster_max()
    {
        return $this->db->select('roster_max')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->roster_max;
    }

    function ok_to_process_transaction($id, &$ret)
    {
        $this->load->model('common/common_waiverwire_model');
        return $this->common_waiverwire_model->admin_ok_to_process_transaction($id, $ret);
    }

    function set_ww_approval_setting($value)
    {
        if (in_array($value, array("auto","semiauto","manual")))
        {
            $data = array('waiver_wire_approval_type' => $value);
            $this->db->where('league_id',$this->leagueid);
            $this->db->update('league_settings',$data);
        }

    }

    function toggle_wwday($wwday)
    {
        $row = $this->db->select('waiver_wire_disable_days')->from('league_settings')->where('league_id',$this->leagueid)->get()->row();
        if ($row)
        {
            $new_wwdays = '';
            $wwdays = str_split($row->waiver_wire_disable_days);
            foreach(str_split('0123456') as $i)
            {
                if (in_array($i,$wwdays))
                {
                    if ($i == $wwday)
                        continue;
                    $new_wwdays .= $i;
                }
                else
                {
                    if ($i == $wwday)
                        $new_wwdays .= $i;
                }
            }
            $this->db->where('league_id',$this->leagueid)->update('league_settings',array('waiver_wire_disable_days' => $new_wwdays));

            return in_array($wwday,str_split($new_wwdays));
        }
    }

}

?>
