<?php

class Schedule extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/schedule_model');
        $this->bc[$this->current_year." Season"] = "";
        $this->bc['Schedule'] = "";
    }

    function index()
    {
        $data['weeks'] = $this->schedule_model->get_season_schedule_array();
        $this->user_view('user/season/schedule.php',$data);
    }
}
?>
