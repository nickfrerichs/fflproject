<?php

class Teams extends MY_Admin_Controller
{
    function __construct() 
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/teams_model');
    }
    
    function index()
    {
        if ($this->security_model->is_league_admin())
        {
            $teams = $this->teams_model->get_league_teams_data();
            $league_name = $this->teams_model->get_league_name();
            $this->load->helper('form');
            $this->admin_view('admin/teams/teams', array('teams' => $teams, 'leaguename' => $league_name));
        }
        else
        {
            echo "Not League admin";
        }
    }
    
    function show($var)
    {
        if ($this->security_model->is_team_in_league($var))
        {
            $team = $this->teams_model->get_team_data($var);
            $this->admin_view('admin/teams/show', array('team' => $team));
        }
    }
    


    
}
