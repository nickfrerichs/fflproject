<?php

class News extends MY_Controller{


    function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $this->load->model('security_model');
        $this->security_model->get_current_week();
        $this->user_view('user/league/news');
    }
}
