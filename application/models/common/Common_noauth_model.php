<?php
class Common_noauth_model extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    function league_name_from_mask_id($maskid)
    {
        return $this->db->select('league_name')->from('league')->where('mask_id',$maskid)->get()->row()->league_name;
    }

    function get_site_name()
    {
        return $this->db->select('name')->from('site_settings')->get()->row()->name;
    }

    function join_code_required($maskid)
    {
        $row = $this->db->select('join_password')->from('league')
            ->join('league_settings','league_settings.league_id = league.id')
            ->where('league.mask_id',$maskid)->get()->row();

        if (count($row) > 0 && $row->join_password != "")
            return true;
        return false;
    }

    function valid_mask($maskid)
    {
        if ($this->db->from('league')->where('mask_id',$maskid)->count_all_results() > 0)
            return True;
        return False;
    }

    function league_has_room($maskid)
    {
        $row = $this->db->select('league_settings.max_teams, league_settings.league_id')->from('league_settings')
            ->join('league','league.id = league_settings.league_id')->where('mask_id',$maskid)->get()->row();
        $active_teams = $this->db->from('team')->where('league_id',$row->league_id)->where('active',1)->count_all_results();
        // If max teams is zero and active teams is zero, must be a new league, need to let someone join.

        if ($row->max_teams == 0 && $active_teams == 0)
        {
            return True;
        }
        if ($active_teams < $row->max_teams)
            return TRUE;
        return FALSE;

    }

    function set_session_variables($expire=False)
    {
        if ($this->session->userdata('expire_basic_vars') < time() || $expire)
        {
            // Need some variables from consolidated config file.
            $this->session->set_userdata('site_name',$this->get_site_name());
            $this->session->set_userdata('expire_basic_vars', time()+$this->session->userdata('session_refresh_time'));
            $this->config->load('fflproject');
            $this->session->set_userdata('basic_debug',$this->config->item('basic_debug'));
        }
    }
}
?>
