<?php

class Transactions_model extends MY_Model
{

    function get_pending_ww_approvals()
    {
        return $this->db->select('pp.first_name as p_first, pp.last_name as p_last, dp.first_name as d_first, dp.last_name as d_last')
            ->select('team.team_name, owner.first_name as o_first, owner.last_name as o_last')
            ->select('dt.club_id as d_club_id, pt.club_id as p_club_id')
            ->select('dpos.text_id as d_pos, ppos.text_id as p_pos')
            ->select('UNIX_TIMESTAMP(waiver_wire_log.request_date) as request_date')
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
            ->where('waiver_wire_log.league_id',$this->leagueid)->where('approved',0)->where('transaction_date',0)
            ->order_by('request_date','desc')
            ->get()->result();
    }

    function approve_ww($id)
    {
        $this->load->model('myteam/waiverwire_model');
        $now = t_mysql();
        $log = $this->db->select('pickup_player_id, team_id, drop_player_id')->from('waiver_wire_log')->where('id',$id)
            ->where('league_id',$this->leagueid)->get()->row();

        // confirm both players are still available

        // Deny all other open approvals waiting for this player.
        $rows = $this->db->select('id')->from('waiver_wire_log')->where('pickup_player_id',$log->pickup_player_id)
            ->where('league_id',$this->leagueid)->where('team_id != ',$log->team_id)->get()->result();

        foreach($rows as $row)
        {
            $data = array('transaction_date'=>$now, 'approved' => 0);
            $this->db->where('id',$row->id);
            $this->db->update('waiver_wire_log',$data);
        }

        // Approve the transaction
        $this->waiverwire_model->drop_player($log->drop_player_id, $log->team_id);
        $this->waiverwire_model->pickup_player($log->pickup_player_id, $log->team_id);

        // Update the log
        $data = array('transaction_date' => $now, 'approved' => 1);
        $this->db->where('id',$id);
        $this->db->update('waiver_wire_log',$data);


    }

}

?>
