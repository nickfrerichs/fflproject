<?php

class Moneylist extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/moneylist_model');
        $this->bc["League Admin"] = "";
        $this->bc["Money List"] = "";

    }

    function index()
    {
        $data = array();
        $data['teams'] = $this->moneylist_model->get_league_teams_data();
        $data['weeks'] = $this->moneylist_model->get_num_weeks();
        $data['types'] = $this->moneylist_model->get_types();
        $data['list'] = $this->moneylist_model->get_moneylist();
        $this->admin_view('admin/moneylist/moneylist',$data);
    }

    function ajax_add()
    {
        $week = $this->input->post('week');
        $teamid = $this->input->post('teamid');
        $amount = $this->input->post('amount');
        $text = $this->input->post('text');
        $typeid = $this->input->post('typeid');
        $this->moneylist_model->add_entry($teamid, $week, $amount, $typeid, $text);
    }
}
?>
