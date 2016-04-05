<?php

class Site_model extends MY_Model
{

    function get_leagues()
    {
        $data=array();
        $leagues = $this->db->select('id, league_name')->from('league')->get()->result();
        foreach ($leagues as $l)
        {
            $data[$l->id] = array();
            $data[$l->id]['league'] = $l;
            $data[$l->id]['admins'] = $this->db->select('uacc_username as username, league_admin_id as id')->from('league_admin')
                ->join('user_accounts','user_accounts.uacc_id = league_admin.league_admin_id')
                ->where('league_admin.league_id',$l->id)->get()->result();
        }
        return $data;
    }

    function create_league($name)
    {
        // Insert into league table
        $data = array('league_name' => $name);
        $this->db->insert('league',$data);
        $id = $this->db->insert_id();

        // Insert into league_settings table
        $data = array('league_id' => $id);
        $this->db->insert('league_settings',$data);
    }

}
