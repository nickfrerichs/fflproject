<?php

class Owners_model extends MY_Model
{    
    function get_league_owners_data()
    {
        $data = $this->db->select('owner.id, owner.first_name, owner.last_name, owner.phone_number')
                ->select('team.id, team_name')
                ->from('owner')
                ->join('team', 'owner.id = team.owner_id','left')
                ->where('team.league_id', $this->leagueid)
                ->where('team.active',1)
                ->get();
        return $data->result();
    }
}
