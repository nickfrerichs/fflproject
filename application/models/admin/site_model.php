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

    function get_league_admins_array($id)
    {
        $data = array();
        $admins = $this->db->select('owner.first_name, owner.last_name, user_accounts.uacc_id as user_id')->from('league_admin')
            ->join('user_accounts','user_accounts.uacc_id = league_admin.league_admin_id')
            ->join('owner','owner.user_accounts_id = user_accounts.uacc_id')
            ->where('league_admin.league_id',$id)->get()->result();
        foreach ($admins as $a)
        {
            $data[$a->user_id] = $a;
        }
        return $data;
    }

    function get_league_owners_array($id)
    {
        $data = array();
        $owners = $this->db->select('owner.first_name, owner.last_name, user_accounts.uacc_id as user_id')->from('team')
            ->join('owner','team.owner_id = owner.id')
            ->join('user_accounts','user_accounts.uacc_id = owner.user_accounts_id')
            ->where('team.league_id',$id)->get()->result();

        foreach ($owners as $o)
        {
            $data[$o->user_id] = $o;
        }
        return $data;
    }

    function set_league_admin($user_id, $leagueid)
    {
        $data = array('league_id' => $leagueid, 'league_admin_id' => $user_id);
        $this->db->insert('league_admin', $data);
    }

    function ok_to_add_admin($user_id, $leagueid)
    {
        $num = $this->db->from('league_admin')->where('league_id',$leagueid)->where('league_admin_id',$user_id)->get()->num_rows();
        if ($num > 0)
            return False;
        return True;

    }

    function remove_league_admin($user_id, $leagueid)
    {
        $this->db->where('league_id',$leagueid)->where('league_admin_id',$user_id);
        $this->db->delete('league_admin');
    }

}
