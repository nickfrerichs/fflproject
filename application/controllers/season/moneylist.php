<?php

class Moneylist extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('season/moneylist_model');
    }


    public function index()
    {
        $data = array();
        $data['list'] = $this->moneylist_model->get_moneylist();
        $data['totals'] = $this->moneylist_model->get_totals();
        $this->user_view('user/season/moneylist',$data);
    }
}

?>
