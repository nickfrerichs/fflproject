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
        $data = array('league_name' => $name,
                      'mask_id' => strtoupper(substr(md5(uniqid()),0,5)));
        $this->db->insert('league',$data);
        $id = $this->db->insert_id();

        // Insert into league_settings table
        $data = array('league_id' => $id);
        $this->db->insert('league_settings',$data);
    }

    function get_league_info($id)
    {
        return $this->db->select('league_name, league.id as id, league.mask_id')->from('league')->where('id',$id)->get()->row();
    }

    function get_league_settings($id)
    {
        return $this->db->select('join_password')->from('league_settings')->where('league_settings.league_id',$id)
            ->get()->row();
    }

    function set_joinpassword($id, $value)
    {
        $data = array('join_password' => $value);
        $this->db->where('league_id',$id);
        $this->db->update('league_settings',$data);
    }

}
