<?php

class History_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
    }

    function get_title_games_array($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;

        // return $this->db->select('title_def.text as title_text, team.team_name')
        //     ->select('schedule_result.team_id')
        //     ->from('schedule')
        //     ->join('schedule_result','schedule_result.schedule_id = schedule.id and schedule_result.team_score > schedule_result.opp_score')
        //     ->join('title_def','title_def.id = schedule.title_def_id')
        //     ->join('team','team.id = schedule_result.team_id')
        //     ->where('schedule.year',$year)
        //     ->where('schedule_result.team_score>','schedule_result.opp_score')
        //     ->order_by('title_def.display_order','asc')
        //     ->get()->result();

        $result = $this->db->select('schedule.id as schedule_id, title.team_id as team_id, title_def.id as title_def_id, title.id as title_id')
            ->select('h.team_name as h_team_name, a.team_name as a_team_name, team_score as h_team_score, opp_score as a_team_score')
            ->select('schedule_result.team_id as h_team_id, schedule_result.opp_id as a_team_id')
            ->select('schedule.week, schedule.year')
            ->select('title_def.text as title_text')
            ->from('schedule')
            ->join('title_def','schedule.title_def_id = title_def.id')
            ->join('title','title.schedule_id = schedule.id','left')
            ->join('schedule_result','schedule_result.schedule_id = schedule.id and schedule_result.team_id > schedule_result.opp_id','left')
            ->join('team as h','h.id = schedule_result.team_id','left')
            ->join('team as a','a.id = schedule_result.opp_id','left')
            ->where('schedule.league_id',$this->leagueid)
            ->where('schedule.year',$year)
            ->get()->result();

        $title_games = array();
        foreach($result as $row)
        {  
            if ($row->team_id == $row->h_team_id)
            {
                $title_games[$row->title_id]['team_name'] = $row->h_team_name;
                $title_games[$row->title_id]['team_score'] = $row->h_team_score;
                $title_games[$row->title_id]['opp_name'] = $row->a_team_name;
                $title_games[$row->title_id]['opp_score'] = $row->a_team_score;

            }
            else
            {
                $title_games[$row->title_id]['team_name'] = $row->a_team_name;
                $title_games[$row->title_id]['team_score'] = $row->a_team_score;
                $title_games[$row->title_id]['opp_name'] = $row->h_team_name;
                $title_games[$row->title_id]['opp_score'] = $row->h_team_score;
            }
            $title_games[$row->title_id]['data'] = $row;
        }

        return $title_games;

    }

    function get_other_assigned_titles($year)
    {

        return $this->db->select('title.id as title_id, title_def.text, team.team_name')
            ->from('title')
            ->join('title_def','title.title_def_id = title_def.id','left')
            ->join('team','team.id = title.team_id')
            ->where('title.schedule_id',0)
            ->where('title_def.league_id',$this->leagueid)
            ->where('title.year',$year)
            ->get()->result();
    }


    function get_team_record($year=0,$limit=10)
    {
        $this->db->select('team.team_name')
            ->select('owner.first_name, owner.last_name')
            ->select('IFNULL(sum(schedule_result.team_score),0) as points, IFNULL(sum(schedule_result.opp_score),0) as opp_points')
            ->select('IFNULL(sum(schedule_result.team_score),0) / count(schedule_result.id) as avg_points')
            ->select('IFNULL(sum(schedule_result.opp_score),0) / count(schedule_result.id) as avg_opp_points')
            ->select('(sum(schedule_result.team_score) / count(schedule_result.id)) - (sum(schedule_result.opp_score) / count(schedule_result.id)) as avg_diff')
            ->select('IFNULL(sum(win=1),0) as wins, IFNULL(sum(loss=1),0) as losses, IFNULL(sum(tie=1),0) as ties')
            ->select('count(schedule_result.id) as total_games')
            ->select('(IFNULL(sum(win=1),0)/count(schedule_result.id)) as win_pct')
            ->from('team')
            ->join('owner','team.owner_id = owner.id')
            ->join('schedule_result','team.id = schedule_result.team_id')
            ->join('schedule','schedule.id = schedule_result.schedule_id')
            ->where('team.league_id',$this->leagueid);
        if ($year > 0)
            $this->db->where('schedule_result.year',$year);
        $this->db->group_by('team.id')
            ->order_by('win_pct','desc');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }
    

}
