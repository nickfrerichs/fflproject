<?php

class Teams extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/teams_model');
        $this->load->model('myteam/myteam_settings_model');
        $this->bc['League'] = "";
        $this->bc['Teams'] = "";
    }


    public function index()
    {

        $data = array();
        $data['teams'] = $this->teams_model->get_league_teams_data();
        $data['logos'] = array();
        foreach($data['teams'] as $t)
        {
            if ($t->logo)
                $data['logos'][$t->team_id] = $this->myteam_settings_model->get_logo_url($t->team_id,'thumb');
            else
                $data['logos'][$t->team_id] = $this->myteam_settings_model->get_default_logo_url('thumb');
        }
        $this->user_view('user/league/teams',$data);
    }


    public function view($team_id)
    {
        $this->load->model('myteam/myteam_roster_model');
        $this->load->model('myteam/schedule_model');
        $data = array();
        $data['bench'] = $this->teams_model->get_bench_data($team_id);
        $data['starters'] = $this->myteam_roster_model->get_starting_lineup_array($team_id);
        $data['schedule'] = $this->schedule_model->get_team_schedule($team_id);
        $data['team_id'] = $team_id;
        $data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
        $data['byeweeks'] = $this->common_model->get_byeweeks_array();
        $data['record'] = $this->myteam_roster_model->get_team_record_data($team_id);

        $data['team'] = $this->teams_model->get_team_data($team_id);
        if($data['team']->logo)
            $data['logo'] = $this->myteam_settings_model->get_logo_url($data['team']->team_id,'med');
        else
            $data['logo'] = $this->myteam_settings_model->get_default_logo_url('med');
        $this->bc['Teams'] = site_url('league/teams');
        $this->bc[$data['team']->team_name] = "";
        $this->user_view('user/league/teams/view_team',$data);
    }
}
