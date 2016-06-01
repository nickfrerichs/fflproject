<?php

class Draft extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/draft_model');
        $this->load->model('admin/security_model');
        $this->bc[$this->league_name] = "";
        $this->bc['Draft'] = "";
    }

    function index()
    {
        $data = array();
        $data['num_rounds'] = $this->draft_model->get_num_rounds();
        $this->admin_view('admin/draft/draft',$data);
    }

    function create()
    {
        $data = array();
        $data['teams'] = $this->draft_model->get_league_teams_data();
        $data['draft_exists'] = $this->draft_model->get_draft_order_count();
        $data['year'] = $this->current_year;
        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Create Order"] = "";
        $this->admin_view('admin/draft/create',$data);
    }

    function do_create()
    {
        $order = $this->input->post('order');
        $reverse = $this->input->post('reverse');
        $rounds = $this->input->post('rounds');

        print_r($order);

        $this->draft_model->create_draft_order($order, $rounds, $reverse);
    }

    function ajax_draft_table()
    {
        $data = array();
        $round = $this->input->post('round');
        $data['draft_rounds'] = $this->draft_model->get_draft_round_data($round);
        print_r($data['draft_rounds']);
        echo $round;
        $this->load->view('admin/draft/ajax_draft_table', $data);
    }

    function settings()
    {
        $data = array();
        $data['year'] = $this->current_year;
        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Settings"] = "";
        $this->admin_view('admin/draft/live',$data);
    }

    function save_draft_settings()
    {
        $draft_time = $this->input->post('date');

        $pick = $this->input->post('pick');
        //$draft_time = $this->current_year.'-'.$mon.'-'.$day.' '.$hour.':'.$min.':00';

        $this->draft_model->save_draft_options($draft_time, $pick);
        $this->draft_model->set_draft_deadlines();

    }



}
