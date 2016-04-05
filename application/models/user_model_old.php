<?php

class User_model extends CI_Model{
    
    protected $userid;
    protected $leagueid;
    // Methods for roster, startinglineup
    function __construct()
    {
        $this->userid = $this->flexi_auth->get_user_id();
        $this->leagueid = $this->get_active_league();

    }
       
    function nfl_players_count($filter = false, $include_inactive = false)
    {
        $this->db->select('player.id')
                ->from('player');
        if (!$include_inactive)
            $this->db->where('status', 'ACT');
        if (is_numeric($filter))
            $this->db->where('player.nfl_position_id', $filter);
        elseif($filter)
            $this->db->like('player.last_name', $filter, 'after');
        $data = $this->db->get();
        return $data->num_rows();
    }
   
    function get_nfl_players($limit = 100000, $start = 0, $show_inactive = false)
    {
        $this->db->select('player.id, player.first_name, player.last_name')
                ->select('nfl_position.text_id as position')
                ->select('nfl_team.club_id')
                ->from('player')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->order_by('player.last_name','asc');
        if (!$show_inactive)
            $this->db->where('status', "ACT");
        $this->db->limit($limit, $start);
        $data = $this->db->get();
        return $data->result();
    }
    
    function get_active_league()
    {
        $data = $this->db->select('league_id')
         ->from('team')
         ->where('owner_user_id', $this->userid)
         ->get();
        return ($data->row()->league_id);
    }
    
}