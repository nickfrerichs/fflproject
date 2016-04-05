<?php

class Schedule extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/schedule_model');
    }

    function index()
    {
        $data['weeks'] = $this->schedule_model->get_season_schedule_array();
        $this->user_view('user/season/schedule.php',$data);
    }
}
?>
