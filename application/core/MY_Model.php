<?php

class MY_Model extends CI_Model
{
    protected $userid;
    protected $leagueid;
    protected $teamid;
    protected $current_week;
    protected $current_year;

    function __construct()
    {
        parent::__construct();

        $this->userid = $this->session->userdata('user_id'); // Add this to session like league_id
        $this->load->model('common/common_model');
        $this->load->model('common/common_noauth_model');

        // Owner specific session variables.
        if ($this->session->userdata('is_owner'))
        {
            $this->leagueid = $this->session->userdata('league_id');
            $this->teamid = $this->session->userdata('team_id');
            $this->current_year = $this->session->userdata('current_year');
            $this->current_week = $this->session->userdata('current_week');
            $this->week_type = $this->session->userdata('week_type');
        }
    }
}
