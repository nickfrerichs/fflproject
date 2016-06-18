<?php

class Team extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('statistics/team_statistics_model');
    }

    function index()
    {
        $data = array();
        $this->user_view('user/statistics/team_statistics.php', $data);
        # By default, show top scores?
    }
}
