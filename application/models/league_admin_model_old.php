<?php

class League_admin_model extends CI_Model{
    
    protected $userid;
    protected $leagueid;
    
    // Methods for admin functions
    function __construct()
    {
        $this->userid = $this->flexi_auth->get_user_id();
        $this->leagueid = $this->session->userdata('league_id');
    }
    
    // MOVE TO USER_MODEL?
    function add_player_to_team($playerid, $teamid)
    {
        $data = array('league_id' => $this->leagueid,
            'team_id' => $teamid,
            'player_id' => $playerid);
        $this->db->insert('roster',$data);
    }
    
    function save_position($values)
    {        
        $data = array('text_id' => $values['text_id'],
            'long_text' => $values['long_text'],
            'league_id' => $this->leagueid,
            'nfl_position_id_list' => $values['league_positions'],
            'max_roster' => $values['max_roster'],
            'min_roster' => $values['min_roster'],
            'max_start' => $values['max_start'],
            'min_start' => $values['min_start']);
        
        if(isset($values['id']))
        {
            $this->db->where('id', $values['id']);
            $this->db->update('position',$data);
        }
        else
          $this->db->insert('position',$data);
        
        //$id = $this->db->insert_id();
        
        /*
        foreach(explode(',',$values['league_positions']) as $nfl_pos_id)
        {
            $data = array('nfl_position_id' => $nfl_pos_id, 'league_position_id' => $id);
            $this->db->insert('position_lookup',$data);
        }
         * 
         */
    }
    
    function position_exists($text_id)
    {
        $data = $this->db->select('id')
                ->from('position')
                ->where('text_id', $text_id)
                ->where('league_id', $this->leagueid)
                ->get();
        if ($data->num_rows != 0)
            return true;
        return false;
    }
    
    function delete_position($posid)
    {
        $this->db->where('id', $posid)
                ->where('league_id', $this->leagueid)
                ->delete('position');
    }
    
    function remove_player_from_team($playerid, $teamid)
    {
        $this->db->where('player_id', $playerid)
                ->where('team_id', $teamid)
                ->delete('roster');
    }
    
    // MOVE TO USER_MODEL?
    function player_is_available($playerid)
    {
        $data = $this->db->select('roster.id')
                ->from('roster')
                ->where('player_id', $playerid)
                ->where('league_id', $this->leagueid)
                ->get();
        if ($data->num_rows == 0)
          return true;
        return false;
    }
    

    
    // MOVE TO USER_MODEL?
    function get_league_positions_data()
    {
        $position_data = $this->db->select('position.id, position.text_id, position.long_text, position.nfl_position_id_list')
                ->select('position.max_roster, position.min_roster, position.max_start, position.min_start')
                ->from('position')
                ->where('league_id', $this->leagueid)
                ->get();
        
        return $position_data->result();
        //$nfl_position_data = $this->db->select('')
       
        //return $data->result();
    }
    
    // MOVE TO USER_MODEL?
    function get_league_position_data($posid)
    {
        $data = $this->db->select('position.id, position.text_id, position.long_text, position.nfl_position_id_list')
                ->select('position.max_roster, position.min_roster, position.max_start, position.min_start')
                ->from('position')
                ->where('league_id', $this->leagueid)
                ->where('id', $posid)
                ->get();
        return $data->row();
    }
    
    
    // MOVE TO USER_MODEL?
    function get_nfl_positions_data($include_all_pos = false)
    {
        if (!$include_all_pos)
            $pos_list = $this->get_league_nfl_position_id_array();
        $this->db->select('nfl_position.id, nfl_position.text_id, nfl_position.long_text')
                ->from('nfl_position');
        if (!$include_all_pos)
                $this->db->where_in('id', $pos_list);
        $this->db->order_by('type','asc')
                ->order_by('nfl_position.text_id', 'asc');
        $data = $this->db->get();
        return $data->result();
    }
    
    
    
    // MOVE TO USER_MODEL?
    function get_nfl_positions_array()
    {
        $data = $this->db->select('nfl_position.id, nfl_position.text_id')
                ->from('nfl_position')
                ->get();
        $data_array = array();
        foreach ($data->result() as $result)
        {
            $data_array[$result->id] = $result->text_id;
        }
        return $data_array;
    }
       
    function get_league_teams_data()
    {
        $data = $this->db->select('team.id, team.owner_user_id, team.team_name, team.league_id') #team
                ->select('owner.first_name, owner.last_name') #owner
                ->select('count(roster.id) as roster_count') #roster
                ->from('team')
                ->join('owner', 'owner.user_accounts_id = team.owner_user_id')
                ->join('roster', 'roster.team_id = team.id', 'left')
                ->where('team.league_id', $this->leagueid)
                ->get();
        return $data->result();
 
    }
    
    // MOVE TO USER_MODEL?
    function get_team_roster_data($teamid)
    {
        $data = $this->db->select('roster.id, roster.player_id') #roster
                ->select('player.short_name') #player
                ->select('nfl_team.club_id') #nfl_team
                ->select('nfl_position.text_id as position') #nfl_position
                ->from('roster')
                ->join('player', 'player.id = roster.player_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->where('roster.league_id', $this->leagueid)
                ->where('roster.team_id', $teamid)
                ->get();
        return $data->result();

    }
    
    // MOVE TO USER_MODEL?
    function get_league_owners_data()
    {
        $data = $this->db->select('owner.id, owner.first_name, owner.last_name, owner.phone_number')
                ->select('team.id, team_name')
                ->from('owner')
                ->join('team', 'team.owner_user_id = owner.id')
                ->where('team.league_id', $this->leagueid)
                ->get();
        return $data->result();
    }
    
    // MOVE TO USER_MODEL?
    function get_team_data($teamid)
    {
        $data = $this->db->select('team.id, team.team_name')
                ->select('owner.first_name, owner.last_name, owner.phone_number')
                ->from('team')
                ->join('owner', 'owner.id = team.owner_user_id')
                ->where('team.league_id', $this->leagueid)
                ->where('team.id', $teamid)
                ->get();
        return $data->result();
    }
    
    // MOVE TO USER_MODEL?
    function get_team_name($teamid)
    {
        $data = $this->db->select('team_name')
                ->from('team')
                ->where('team.id', $teamid)
                ->get();
        return $data->row()->team_name;
    }
    
    // MOVE TO USER_MODEL?
    function get_league_name()
    {
        $data = $this->db->select('league_name')
                ->from('league')
                ->where('league.id', $this->leagueid)
                ->get();
        return $data->row()->league_name;
    }
    
    function admin_check($type, $id)
    {
        switch($type)
        {
            case 'team':
                $data = $this->db->select('league_id')
                    ->from('team')
                    ->where('id', $id)
                    ->where('league_id', $this->leagueid)
                    ->get();
                if ($data->num_rows() > 0)
                    return true;
                return false;
                
            case 'league':
                $data = $this->db->select('league_admin_id')
                    ->from('league_admin')
                    ->where('league_id', $this->leagueid)
                    ->where('league_admin_id', $this->userid)
                    ->get();
                if ($data->num_rows() > 0)
                    return true;
                return false;
                
            case 'position':
                $data = $this->db->select('id')
                    ->from('position')
                    ->where('league_id', $this->leagueid)
                    ->where('position.id', $id)
                    ->get();
                if ($data->num_rows() > 0)
                    return true;
                return false;
                
            case 'default':
                return false;
        }
    }
    
    function get_league_nfl_position_id_array()
    {
        $data = $this->db->select('position.nfl_position_id_list')
                ->from('position')
                ->where('position.league_id', $this->leagueid)
                ->get();
        $pos_list = array();
       
        foreach ($data->result() as $posrow)
            $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));
        return $pos_list;
    }
    
}