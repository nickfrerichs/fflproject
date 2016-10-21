<?php

class Teams_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->leagueid = $this->session->userdata('league_id');
    }

    function get_league_teams_data()
    {

        return $this->db->select('team.id as team_id, team.owner_id, team.long_name, team.logo')
            ->select('division.name as division_name')
            ->select('owner.first_name, owner.last_name, owner.phone_number')
            ->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
            ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
            ->select('count(schedule_result.id) as total_games')
            ->select('((sum(win=1) + (sum(tie=1)/2))/count(schedule_result.id)) as winpct')
            ->from('team')
            ->join('owner','team.owner_id = owner.id')
            ->join('team_division','team_division.team_id = team.id and team_division.year = '.$this->current_year,'left')
            ->join('division','team_division.division_id = division.id','left')
            ->join('schedule_result','schedule_result.team_id = team.id and schedule_result.year='.$this->current_year,'left')
            ->join('schedule','schedule.id = schedule_result.schedule_id and schedule.year='.$this->current_year,'left')
            ->where('team.league_id',$this->leagueid)
            ->where('team.active',true)
            ->group_by('team.id')
            ->order_by('team_name','asc')
            ->get()->result();
    }


    function get_bench_data($teamid)
    {
        $query = $this->db->query('select player.id as player_id, player.first_name, player.last_name, player.nfl_position_id, player.short_name, '.
            'nfl_position.text_id as pos_text, IFNULL(nfl_team.club_id,"FA") as club_id, IFNULL(sum(fantasy_statistic.points),0) as points '.
            'from `roster` join `player` on `roster`.`player_id` = `player`.`id` '.
            'join nfl_position on nfl_position.id = player.nfl_position_id '.
            'left join nfl_team on nfl_team.id = player.nfl_team_id '.
            'left join fantasy_statistic on fantasy_statistic.player_id = roster.player_id and fantasy_statistic.year = '.$this->current_year.
            ' and fantasy_statistic.league_id = roster.league_id where '.
            '`roster`.`player_id` not in (SELECT `player_id` FROM `starter` where `week` = '.$this->current_week.
            ' and `year` = '.$this->current_year.' and team_id = '.$teamid.') and `roster`.`league_id` = '.$this->leagueid.
            ' and roster.team_id = '.$teamid.' '.
            ' group by roster.player_id order by nfl_position.display_order asc');

        return $query->result();
    }

    function get_bench_quickstats_data($teamid, $week = "", $year="")
    {
        if ($week == "")
            $week = $this->current_week;
        if ($year == "")
            $year = $this->current_year;

        $query = $this->db->query('select player.id as player_id, player.first_name, player.last_name, player.nfl_position_id, player.short_name, '.
            'nfl_position.text_id as pos_text, IFNULL(nfl_team.club_id,"FA") as club_id, IFNULL(sum(fantasy_statistic.points),0) as points '.
            'from `roster` join `player` on `roster`.`player_id` = `player`.`id` '.
            'join nfl_position on nfl_position.id = player.nfl_position_id '.
            'join nfl_team on nfl_team.id = player.nfl_team_id '.
            'left join fantasy_statistic on fantasy_statistic.player_id = roster.player_id and fantasy_statistic.year = '.$year.
            ' and fantasy_statistic.league_id = roster.league_id and fantasy_statistic.week ='.$week.' where '.
            '`roster`.`player_id` not in (SELECT `player_id` FROM `starter` where `week` = '.$week.
            ' and `year` = '.$year.' and team_id = '.$teamid.') and `roster`.`league_id` = '.$this->leagueid.
            ' and roster.team_id = '.$teamid.' '.
            ' group by roster.player_id order by nfl_position.display_order asc');

        return $query->result();
    }

    function get_starters_data($teamid)
    {
        return $this->db->select('starter.starting_position_id, player.id as player_id, player.first_name, player.last_name, player.short_name')
            ->select('nfl_position.text_id as pos_text, IFNULL(nfl_team.club_id,"FA") as club_id, sum(fantasy_statistic.points) as points',false)
            ->from('starter')
            ->join('player','player.id = starter.player_id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('fantasy_statistic','fantasy_statistic.player_id = starter.player_id and fantasy_statistic.year = '.$this->current_year,'left')
            ->where('starter.year',$this->current_year)->where('starter.week', $this->current_week)
            ->where('team_id',$teamid)->group_by('starter.player_id')
            ->get()->result();
    }

    function get_team_data($teamid)
    {
        return $this->db->select('team.id as team_id, team.owner_id, team.long_name, team.logo, team.team_name as team_name')
            ->select('division.name as division_name')
            ->select('owner.first_name, owner.last_name, owner.phone_number')
            ->from('team')
            ->join('owner','team.owner_id = owner.id')
            ->join('team_division','team_division.team_id = team.id and team_division.year = '.$this->current_year,'left')
            ->join('division','team_division.division_id = division.id','left')
            ->where('team.league_id',$this->leagueid)
            ->where('team.id',$teamid)
            ->order_by('team_name','asc')
            ->get()->row();
    }

}
