<?php

class Divisions_model extends MY_Model{

    function get_teams_data()
    {
        $data = $this->db->select('team.id as team_id, team.team_name')
                ->select('division.id as division_id, division.name as division_name')
                ->from('team')
                ->join('team_division','team_division.team_id = team.id and team_division.year = '.$this->current_year,'left')
                ->join('division','team_division.division_id = division.id', 'left')
                ->where('team.league_id',$this->leagueid)
                ->where('team.active',1)
                ->order_by('division.id','asc')
                ->get();

        return $data->result();
    }

    function get_league_divisions()
    {
        $data = $this->db->select('division.id, division.name')
                ->from('division')->where('league_id',$this->leagueid)->where('year',$this->current_year)->get();
        return $data->result();
    }

    function add_division($name)
    {
        $data = array('name' => $name, 'league_id' => $this->leagueid, 'year' => $this->current_year);
        $this->db->insert('division', $data);
    }

    function save_team($team_id, $division_id)
    {
        $this->db->where('team_id',$team_id)->where('year',$this->current_year)->where('league_id',$this->leagueid);
        $this->db->delete('team_division');

        $data = array('team_id' => $team_id, 'division_id' => $division_id, 'year' => $this->current_year, 'league_id' => $this->leagueid);
        $this->db->insert('team_division',$data);
        //$this->db->update('team', array('division_id' => $division_id));
    }

    function delete_division($id)
    {
        $this->db->where('league_id', $this->leagueid)
                ->where('division_id', $id)->delete('team_division');

        $this->db->where('id',$id)->delete('division');
    }

}
