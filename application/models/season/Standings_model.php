<?php

class Standings_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->leagueid = $this->session->userdata('league_id');
    }

    function get_year_standings($year = 0)
    {
        if($year == 0)
            $year = $this->current_year;

        $divs_array = array();
        $teams_array = array();

        $divs = $this->db->select('distinct(division_id) as id, division.name')
            ->from('team_division')
            ->join('division','division.id = team_division.division_id and division.year = team_division.year')
            ->where('team_division.league_id',$this->leagueid)
            ->where('division.year',$year)->get()->result();

        $teams = $this->db->select('home_team_id, away_team_id')->from('schedule')->where('year',$year)->get()->result();
        foreach($teams as $t)
        {
            if (!in_array($t->home_team_id,$teams_array))
                $teams_array[] = $t->home_team_id;
            if (!in_array($t->away_team_id,$teams_array))
                $teams_array[] = $t->away_team_id;
        }

        if (count($divs) > 0)
        {
            foreach ($divs as $d)
            {
                $divs_array[$d->id]['name'] = $d->name;
                $this->db->select('team.team_name, team.id as team_id')
                    ->select('owner.first_name, owner.last_name')
                    ->select('IFNULL(sum(schedule_result.team_score),0) as points, IFNULL(sum(schedule_result.opp_score),0) as opp_points')
                    ->select('IFNULL(sum(win=1),0) as wins, IFNULL(sum(loss=1),0) as losses, IFNULL(sum(tie=1),0) as ties')
                    ->select('count(schedule_result.id) as total_games')
                    ->select('standings_notation_def.text as notation_text, standings_notation_def.symbol as notation_symbol')
                    ->from('team')
                    ->join('schedule_result','schedule_result.team_id = team.id and schedule_result.year='.$year,'left')
                    ->join('owner','team.owner_id = owner.id')
                    ->join('team_division','team_division.team_id = team.id and team_division.year = '.$year)
                    ->join('standings_notation_team','standings_notation_team.team_id = team.id and standings_notation_team.year='.$year,'left')
                    ->join('standings_notation_def','standings_notation_def.id = standings_notation_team.standings_notation_def_id and standings_notation_team.year='.$year,'left')
                    ->where('team.active',1)
                    ->where('team.league_id',$this->leagueid)
                    ->where('team_division.division_id',$d->id);
                    if (count($teams_array) > 0)
                        $this->db->where_in('team.id',$teams_array);
                    $this->db->group_by('team.id')
                    ->order_by('wins','desc')
                    ->order_by('losses','asc')
                    ->order_by('points','desc');
                $divs_array[$d->id]['standings'] = $this->db->get()->result();
            }


        }
        else
        {
            // Old way without divisions, maybe for power rankings
            $this->db->select('team.team_name, team.id as team_id')->select('owner.first_name, owner.last_name')
                ->select('IFNULL(sum(schedule_result.team_score),0) as points, IFNULL(sum(schedule_result.opp_score),0) as opp_points')
                ->select('IFNULL(sum(win=1),0) as wins, IFNULL(sum(loss=1),0) as losses, IFNULL(sum(tie=1),0) as ties')
                ->select('count(schedule_result.id) as total_games')
                ->from('team')
                ->join('schedule_result','schedule_result.team_id = team.id and schedule_result.year='.$year,'left')
                ->join('owner','team.owner_id = owner.id')
                ->join('standings_notation_team','standings_notation_team.team_id = team.id','left')
                ->join('standings_notation_def','standings_notation_def.id = standings_notation_team.standings_notation_def_id and standings_notation_team.year='.$year,'left')
                ->where('team.active',1)
                ->where('team.league_id',$this->leagueid);
                if (count($teams_array) > 0)
                    $this->db->where_in('team.id',$teams_array);
                $this->db->group_by('team.id')
                ->order_by('wins','desc')
                ->order_by('losses','asc');
                $divs_array[0]['standings'] =  $this->db->get()->result();
        }
        return $divs_array;

    }

    function get_years()
    {
        return $this->db->select('distinct(year) as year')->from('schedule')->where('league_id',$this->leagueid)
            ->order_by("year","desc")->get()->result();
    }

    function get_notation_defs()
    {
        return $this->db->select('symbol,text')->from('standings_notation_def')->where('year',$this->current_year)->where('league_id',$this->leagueid)->get()->result();
    }

}
