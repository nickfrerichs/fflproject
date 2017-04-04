<?php

class History_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
    }

    function get_titles($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;

        return $this->db->select('schedule_title.text as title_text, team.team_name')
            ->select('schedule_result.team_id')
            ->from('schedule')
            ->join('schedule_result','schedule_result.schedule_id = schedule.id and schedule_result.team_score > schedule_result.opp_score')
            ->join('schedule_title','schedule_title.id = schedule.schedule_title_id')
            ->join('team','team.id = schedule_result.team_id')
            ->where('schedule.year',$year)
            ->where('schedule_result.team_score>','schedule_result.opp_score')
            ->order_by('schedule_title.display_order','asc')
            ->get()->result();

    }

}
