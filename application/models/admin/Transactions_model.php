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
        //if ($this->ok_to_process_transaction($log->team_id, $log->pickup_player_id, $log->drop_player_id, $msg))
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
        //old
        //function ok_to_process_transaction($team_id, $pickup_id, $drop_id, &$ret)

        $this->load->model('common/common_waiverwire_model');
        return $this->common_waiverwire_model->admin_ok_to_process_transaction($id, $ret);

        // THE REST OF THIS IS OLD BEFORE MOVING TO common_waiverwire_model
        // Check roster limit
        // $roster_num = $this->db->from('roster')->where('team_id',$team_id)->count_all_results();
        // $roster_max = $this->get_roster_max();
        // if ($roster_max != -1)
        // {
        //     if (($drop_id == 0 && ($roster_max <= $roster_num)) || $roster_num > $roster_max)
        //     {
        //         $ret = "This will put the team over roster limit of ".$roster_max." players.  They'll have ".$roster_num;
        //         return False;
        //     }
        // }
        //
        // // Check position limit, this is a tad complicated
        // $pos_year = $this->common_model->league_position_year();
        // $positions = $this->db->select('nfl_position_id_list, max_roster, text_id')->from('position')->where('league_id',$this->leagueid)
        //     ->where('position.year',$pos_year)->get()->result();
        // $pickup_nfl_pos = $this->db->select('nfl_position_id')->from('player')->where('id',$pickup_id)->get()->row()->nfl_position_id;
        // $temp = $this->db->select('nfl_position_id')->from('player')->where('id',$drop_id)->get()->row();
        // if (count($temp) == 1)
        //     $drop_nfl_pos = $temp->nfl_position_id;
        // else
        //     $drop_nfl_pos = 0;
        // $pos_limit = True;
        // $pos_limit_text = "";
        // foreach ($positions as $p)
        // {
        //     if ($p->max_roster == -1) // limit is 0
        //     {
        //         $pos_limit = False;
        //         break;
        //     }
        //     $p_array = explode(",",$p->nfl_position_id_list);
        //     // If the player being added has an NFL position that is part of this league position..
        //     if (in_array($pickup_nfl_pos,$p_array))
        //     {
        //
        //         $pos_count = $this->db->from('roster')->join('player','player.id = roster.player_id')
        //             ->where('roster.team_id',$team_id)
        //             ->where_in('player.nfl_position_id',explode(",",$p->nfl_position_id_list))->count_all_results();
        //
        //         // If the player being dropped is also part of this league position, subtract one from count.
        //         if (in_array($drop_nfl_pos,$p_array))
        //             $pos_count--;
        //
        //         // If position count after adding this picked up player doesn't put us over the limit, then we have
        //         // room for in at least one league position spot.  Set temp pos_limt variable to false and break out of loop
        //         if ($pos_count+1 <= $p->max_roster)
        //         {
        //             $pos_limit = False;
        //             break;
        //         }
        //         else
        //         {
        //             $pos_limit = $p->max_roster;
        //             $pos_limit_text = $p->text_id;
        //         }
        //     }
        //
        // }
        // if ($pos_limit)
        // {
        //     $ret = "The team can only have " .$pos_limit." players on it's roster at the ".$pos_limit_text." position.";
        //     return False;
        // }
        //
        // // Check drop player is owned by this team
        // $num = $this->db->from('roster')->where('league_id',$this->leagueid)
        //     ->where('player_id',$drop_id)->where('team_id',$team_id)->count_all_results();
        // if($drop_id != 0 && $num==0)
        // {
        //     $ret = "The team no longer owns the player they are wanting to drop.";
        //     return False;
        // }
        //
        //
        // // Check if pick up player is already on a team.
        // $num = $this->db->from('roster')->where('player_id',$pickup_id)->where('league_id',$this->leagueid)->count_all_results();
        //
        // if ($num > 0)
        // {
        //     $ret = "This player is already on another team.";
        //     return False;
        // }
        //
        // // All checks passed, return True;
        // return True;

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
