<?php

class News extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/news_model');
        $this->bc["League"] = "";
        $this->bc["News"] = "";
    }


    public function index()
    {
        $this->load->model('myteam/waiverwire_model');
        $data = array();
        $data['news'] = $this->news_model->get_news_data();
        $data['waiverwire_log'] = $this->waiverwire_model->get_log_data($this->current_year,time()-(3*24*60*60*3));
        $this->user_view('user/league/news',$data);
    }
}
