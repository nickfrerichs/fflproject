<?php

class News extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/news_model');

        $this->bc["League"] = "";
        $this->bc["News"] = "";
    }


    public function index()
    {
        $data = array();
        $data['news'] = $this->news_model->get_news_data();
        $this->user_view('user/league/news',$data);
    }
}
