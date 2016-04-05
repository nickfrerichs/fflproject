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

        $divs = $this->db->select('distinct(division_id) as id, division.name')
            ->from('team_division')
            ->join('division','division.id = team_division.id and division.year = team_division.year')
            ->where('team_division.league_id',$this->leagueid)
            ->where('division.year',$year)->get()->result();

        if (count($divs) > 0)
        {
            foreach ($divs as $d)
            {
                $divs_array[$d->id]['name'] = $d->name;
                $divs_array[$d->id]['standings'] = $this->db->select('team.team_name, team.id as team_id')
                    ->select('owner.first_name, owner.last_name')
                    ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
                    ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
                    ->select('count(schedule_result.id) as total_games')
                    ->select('standings_notation_def.text as notation_text, standings_notation_def.symbol as notation_symbol')
                    ->from('team')
                    ->join('schedule_result','schedule_result.team_id = team.id')
                    ->join('schedule','schedule.id = schedule_result.schedule_id')
                    ->join('owner','team.owner_id = owner.id')
                    ->join('team_division','team_division.team_id = team.id and team_division.year = '.$year)
                    ->join('standings_notation_team','standings_notation_team.team_id = team.id','left')
                    ->join('standings_notation_def','standings_notation_def.id = standings_notation_team.standings_notation_def_id','left')
                    ->where('team.league_id',$this->leagueid)
                    ->where('schedule.year',$year)
                    ->where('team_division.division_id',$d->id)
                    ->group_by('team.id')
                    ->order_by('wins','desc')
                    ->order_by('losses','asc')
                    ->order_by('points','desc')
                    ->get()->result();
            }


        }
        else
        {
            // Old way without divisions, maybe for power rankings
            $divs_array[0]['standings'] = $this->db->select('team.team_name, team.id as team_id')->select('owner.first_name, owner.last_name')
                ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
                ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
                ->select('count(schedule_result.id) as total_games')
                ->from('team')
                ->join('schedule_result','schedule_result.team_id = team.id')
                ->join('schedule','schedule.id = schedule_result.schedule_id')
                ->join('owner','team.owner_id = owner.id')
                ->where('team.league_id',$this->leagueid)
                ->where('schedule.year',$year)
                ->group_by('team.id')
                ->order_by('wins','desc')
                ->order_by('losses','asc')
                ->get()->result();
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
