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
        $this->load->model('season/moneylist_model');
        $data = array();
        $result = $this->news_model->get_news_data();
        $data['news'] = $result['news'];
        $data['show_moneylist'] = $this->moneylist_model->moneylist_is_active();
        $data['ajax_wait'] = true;
        //$data['waiverwire_log'] = $this->waiverwire_model->get_log_data($this->current_year,time()-(24*60*60*1));
        $this->user_view('user/league/news',$data);
    }

    public function test()
    {
        $this->load->model('security_model');
        $this->security_model->set_owner_session_variables();
    }
}
