<?php

class Schedule_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
    }

    function get_team_schedule($team_id = 0)
    {
        if ($team_id == 0)
            $team_id = $this->teamid;
        //$this->current_year = 2014;
        return $this->db->select('home.id as home_id, away.id as away_id, schedule.week')
                ->select('home.team_name as home_name, away.team_name as away_name')
                ->select('home_result.team_score as home_score, away_result.team_score as away_score')
                ->select('home_result.win as home_win, home_result.loss as home_loss, home_result.tie as home_tie')
                ->select('away_result.win as away_win, away_result.loss as away_loss, away_result.tie as away_tie')
                ->from('schedule')
                ->join('team as home', 'home.id = schedule.home_team_id')
                ->join('team as away', 'away.id = away_team_id')
                ->join('schedule_result as home_result','home_result.schedule_id = schedule.id and home_result.team_id = schedule.home_team_id','left')
                ->join('schedule_result as away_result','away_result.schedule_id = schedule.id and away_result.team_id = schedule.away_team_id','left')
                ->where('schedule.year', $this->current_year)
                ->where('(`schedule`.`home_team_id` = '.$team_id.' or `schedule`.`away_team_id` = '.$team_id.')')
                ->order_by('schedule.week','asc')
                ->get()->result();
    }

    function get_season_schedule_array($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;

        $schedule = $this->db->select('home.id as home_id, away.id as away_id, schedule.week, schedule.game')
                ->select('home.team_name as home_name, away.team_name as away_name')
                ->select('IFNULL(home_result.team_score,"-") as home_score, IFNULL(away_result.team_score,"-") as away_score',false)
                ->select('home_result.win as home_win, home_result.loss as home_loss, home_result.tie as home_tie')
                ->select('away_result.win as away_win, away_result.loss as away_loss, away_result.tie as away_tie')
                ->select('home_owner.first_name, home_owner.last_name, away_owner.first_name, away_owner.last_name')
                ->from('schedule')
                ->join('team as home', 'home.id = schedule.home_team_id')
                ->join('team as away', 'away.id = away_team_id')
                ->join('owner as home_owner', 'home_owner.id = home.owner_id')
                ->join('owner as away_owner', 'away_owner.id = away.owner_id')
                ->join('schedule_result as home_result','home_result.schedule_id = schedule.id and home_result.team_id = schedule.home_team_id','left')
                ->join('schedule_result as away_result','away_result.schedule_id = schedule.id and away_result.team_id = schedule.away_team_id','left')
                ->where('schedule.year', $this->current_year)
                ->where('schedule.league_id',$this->leagueid)
                ->order_by('week','asc')
                ->order_by('game','asc')
                ->get()->result();
        $data = array();
        $weeks = array();
        foreach ($schedule as $s)
        {
            $weeks[$s->week][] = $s;
        }

        if(array_key_exists($this->current_week,$weeks))
            $data['current_week'] = $weeks[$this->current_week];
        if(array_key_exists($this->current_week-1,$weeks))
            $data['previous_week'] = $weeks[$this->current_week-1];
        if(array_key_exists($this->current_week+1,$weeks))
            $data['next_week'] = $weeks[$this->current_week+1];

        $data['weeks'] = $weeks;
        return $data;

    }
}
