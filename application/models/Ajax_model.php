<?php

class Ajax_model extends CI_Model{
    
    protected $userid;
    protected $leagueid;
    
    // Methods for admin functions
    function __construct()
    {
        $this->userid = $this->ion_auth->get_user_id();
        $this->leagueid = $this->session->userdata('league_id');
    }
    
    function player_search($search_text)
    {
        $data = $this->db->select('player.id, CONCAT( player.first_name ," ", player.last_name ) as full_name', false)
                ->select('nfl_team.club_id')
                ->from('player')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id')
                ->where('status', "ACT")
                ->like('CONCAT(player.first_name,"%20",player.last_name)', $search_text)
                ->limit('10')
                ->get();
        return $data->result();
    }
    
    
}