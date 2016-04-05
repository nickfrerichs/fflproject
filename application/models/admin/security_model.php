<?php

class Security_model extends MY_Model
{
    function is_league_admin()
    {
        $data = $this->db->select('league_admin_id')
            ->from('league_admin')
            ->where('league_id', $this->leagueid)
            ->where('league_admin_id', $this->userid)
            ->get();
        if ($data->num_rows() > 0)
            return true;
        return false;
    }
    
    function is_position_in_league($id)
    {
        $data = $this->db->select('id')
            ->from('position')
            ->where('league_id', $this->leagueid)
            ->where('position.id', $id)
            ->get();
        if ($data->num_rows() > 0)
            return true;
        return false;
    }
    
    function is_team_in_league($id)
    {
        $data = $this->db->select('league_id')
        ->from('team')
        ->where('id', $id)
        ->where('league_id', $this->leagueid)
        ->get();
    if ($data->num_rows() > 0)
        return true;
    return false;
    }
}