<?php

class Scores extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('season/scores_model');
        $this->live = $this->session->userdata('live_scores');
        $this->bc[$this->current_year." Season"] = "";
        $this->bc['Weekly Scores'] = "";
    }

    function index()
    {

        $this->week();
    }

    function week($week = 0)
    {
        if ($week == 0)
            $week = min($this->current_week,$this->common_model->num_weeks_in_schedule());
        $data = array();


        //if ($this->input->post('week'))
        //    $week = $this->input->post('week');

        $data['matchups'] = $this->scores_model->get_fantasy_matchups(null,$week);
        $data['weeks'] = $this->scores_model->get_weeks();
        $data['selected_week'] = $week;
        $data['selected_year'] = $this->session->userdata['current_year'];
        $this->bc['Weekly Scores'] = site_url('season/scores');
        if ($week > 0)
            $this->bc['Week '.$week] = "";
        $this->user_view('user/season/scores',$data);
    }

    function live($view = "")
    {
        if ($view == 'standard')
            $this->live_standard();
        elseif ($view == 'compact')
            $this->compact();
        else
        {
            // Send them to a view that picks one
            $this->load->view('user/season/scores/redirect');
        }
    }

    function live_standard()
    {
        $this->load->model('myteam/myteam_settings_model');
        $data = array();
        $data['matchups'] = $this->scores_model->get_fantasy_matchups(null, $this->current_week);

        foreach($data['matchups'] as $key => $m)
        {

            if ($m['home_team']['team']->logo)
                $data['matchups'][$key]['home_team']['thumb'] = $this->myteam_settings_model->get_logo_url($m['home_team']['team']->id,'thumb');
            else
                $data['matchups'][$key]['home_team']['thumb'] = $this->myteam_settings_model->get_default_logo_url('thumb');
            if ($m['away_team']['team']->logo)
                $data['matchups'][$key]['away_team']['thumb'] = $this->myteam_settings_model->get_logo_url($m['away_team']['team']->id,'thumb');
            else
                $data['matchups'][$key]['away_team']['thumb'] = $this->myteam_settings_model->get_default_logo_url('thumb');
        }

        $data['nfl_matchups'] = $this->scores_model->get_live_nfl_matchups_data();
        $this->user_view('user/season/scores/live/standard',$data);
    }

    function compact()
    {
        $data = array();
        $data['matchups'] = $this->scores_model->get_fantasy_matchups(null, $this->current_week);
        $this->user_view('user/season/scores/live/compact',$data);
    }

}
