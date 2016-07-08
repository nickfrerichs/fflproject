<?php

class Rules extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/rules_model');
        $this->bc['League'] = "";
        $this->bc['Rules'] = "";
    }


    public function index()
    {
        $data['content'] = $this->rules_model->get_rules_content();
        $this->user_view('user/league/rules', $data);
    }

    public function scoring()
    {
        $data = array();
        $data['defs'] = $this->rules_model->get_scoring_defs_data();
        $this->bc['Rules'] = site_url('league/rules');
        $this->bc['Scoring Definitions'] = "";
        $this->user_view('user/league/rules/scoring',$data);
    }

    public function Positions()
    {
        $data = array();
        $this->bc['Rules'] = site_url('league/rules');
        $this->bc['League Positions'] = "";
        $data['pos_lookup'] = $this->rules_model->get_nfl_pos_lookup_array();
        $data['league_pos'] = $this->rules_model->get_league_positions_data();
        $this->user_view('user/league/rules/positions',$data);
    }
}
