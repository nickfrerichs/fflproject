<?php

class Schedule extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/schedule_model');
    }

    function index()
    {
    	$data['teamid'] = $this->teamid;
        $data['teamname'] = $this->team_name;
    	$data['schedule'] = $this->schedule_model->get_team_schedule();
    	$this->user_view('user/myteam/schedule',$data);
    }

}
