<?php

class Site_model extends MY_Model
{

    function get_leagues()
    {
        $data=array();
        $leagues = $this->db->select('league.id, league.mask_id, league.league_name, league_settings.join_password')->from('league')
            ->join('league_settings','league_settings.league_id = league.id')->get()->result();
        foreach ($leagues as $l)
        {
            $data[$l->id] = array();
            $data[$l->id]['league'] = $l;
            $data[$l->id]['admins'] = $this->db->select('username, league_admin_id as id')->from('league_admin')
                ->join('user_accounts','user_accounts.id = league_admin.league_admin_id')
                ->where('league_admin.league_id',$l->id)->get()->result();
            $data[$l->id]['active_teams'] = $this->db->select('id, team_name')->from('team')->where('active',1)->where('league_id',$l->id)->get()->result();
            $data[$l->id]['teams'] = $this->db->select('id, team_name')->from('team')->where('league_id',$l->id)->get()->result();
        }
        return $data;
    }

    function create_league($name)
    {
        $this->load->model('admin/end_season_model');
        $year = $this->end_season_model->get_real_year();
        // Insert into league table
        $data = array('league_name' => $name,
                      'mask_id' => strtoupper(substr(md5(uniqid()),0,5)),
                      'season_year' => $year);
        $this->db->insert('league',$data);
        $id = $this->db->insert_id();

        // Insert into league_settings table
        $data = array('league_id' => $id, 'offseason' => 1);
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

    function get_site_settings()
    {
        return $this->db->select('name, debug_user, debug_admin, debug_year, debug_week, debug_week_type_id, db_version')->from('site_settings')->get()->row();
    }

    function get_week_types_array()
    {
        $data = array();
        $rows = $this->db->select('id, text_id')->from('nfl_week_type')->get()->result();
        foreach($rows as $row)
        {
            $data[$row->id] = $row->text_id;
        }
        return $data;

    }

    function set_joinpassword($id, $value)
    {
        $data = array('join_password' => $value);
        $this->db->where('league_id',$id);
        $this->db->update('league_settings',$data);
    }

    function set_sitename($name)
    {
        $data = array('name' => $name);
        $this->db->update('site_settings',$data);

    }

    function get_league_admins_array($id)
    {
        $data = array();
        $admins = $this->db->select('owner.first_name, owner.last_name, user_accounts.id as user_id')->from('league_admin')
            ->join('user_accounts','user_accounts.id = league_admin.league_admin_id')
            ->join('owner','owner.user_accounts_id = user_accounts.id')
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
        $owners = $this->db->select('owner.first_name, owner.last_name, user_accounts.id as user_id')->from('team')
            ->join('owner','team.owner_id = owner.id')
            ->join('user_accounts','user_accounts.id = owner.user_accounts_id')
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

    function toggle_league_admin($user_id, $leagueid)
    {
        $num = $this->db->from('league_admin')->where('league_id',$leagueid)->where('league_admin_id',$user_id)->get()->num_rows();
        if ($num > 0)
        {
            $this->db->where('league_id',$leagueid)->where('league_admin_id',$user_id);
            $this->db->delete('league_admin');
            return 0;
        }
        else
        {
            $data = array('league_id' => $leagueid, 'league_admin_id' => $user_id);
            $this->db->insert('league_admin', $data);
            return 1;
        }
    }

    function toggle_site_setting($col)
    {
        $val = !$this->db->select($col)->from('site_settings')->get()->row()->{$col};
        $this->db->update('site_settings',array($col => $val));
        return $val;
    }

    function has_leagues()
    {
        if ($this->db->from('league')->count_all_results() > 0)
            return True;
        return False;

    }

    function get_nfl_schedule_status()
    {
        $row = $this->db->select('year,gt')->from('nfl_schedule')->order_by('start_time','desc')->limit(1)->get()->row();
        if ($row)
            return $row;
        return False;
    }

    function set_debug_week($value)
    {
        $data = array("debug_week" => $value);
        $this->db->update('site_settings',$data);
    }

    function set_debug_year($value)
    {
        $data = array("debug_year" => $value);
        $this->db->update('site_settings',$data);
    }

    function set_debug_weektype($value)
    {
        $data = array("debug_week_type_id" => $value);
        $this->db->update('site_settings',$data);
    }

}
