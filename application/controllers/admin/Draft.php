<?php

class Draft extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/draft_model');
        $this->load->model('admin/admin_security_model');
        $this->bc["League Admin"] = "";
        $this->bc['Draft'] = "";
    }

    function index()
    {
        $data = array();
        $data['num_rounds'] = $this->draft_model->get_num_rounds();
        $data['settings'] = $this->draft_model->get_draft_settings();
        $this->admin_view('admin/draft/draft',$data);
    }

    function create()
    {
        $data = array();
        $data['teams'] = $this->draft_model->get_league_teams_data();
        $data['draft_exists'] = $this->draft_model->get_draft_order_count();
        $data['year'] = $this->current_year;
        $data['settings'] = $this->draft_model->get_draft_settings();
        if ($data['settings']->trade_draft_picks && $this->draft_model->future_year_exists())
            $data['traded_picks'] = True;
        else
            $data['traded_picks'] = False;
        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Create Order"] = "";
        $this->admin_view('admin/draft/create',$data);
    }

    function do_create()
    {
        $order = $this->input->post('order');
        $reverse = $this->input->post('reverse');
        $rounds = $this->input->post('rounds');
        $trades = $this->input->post('trades');

        $this->draft_model->create_draft_order($order, $rounds, $reverse, $trades);
    }

    function ajax_draft_table()
    {
        $data = array();
        $round = $this->input->post('round');
        $data['draft_rounds'] = $this->draft_model->get_draft_round_data($round);

        $this->load->view('admin/draft/ajax_draft_table', $data);
    }

    function settings()
    {
        $data = array();
        $data['year'] = $this->current_year;
        $data['settings'] = $this->draft_model->get_draft_settings();
        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Settings"] = "";
        $this->admin_view('admin/draft/settings',$data);
    }

    function save_draft_settings()
    {
        $response = array("success" => False);
        $id = $this->input->post('id');
        $limit = false;
        $draft_time = false;
        if ($id == "#drafttime")
        {
            $draft_time = $this->input->post('value');
            $response['value'] = $draft_time;
        }
        if ($id == "#picktime")
        {
            $limit = $this->input->post('value');
            $response['value'] = $limit;
        }

        //$draft_time = $this->current_year.'-'.$mon.'-'.$day.' '.$hour.':'.$min.':00';

        $this->draft_model->save_draft_options($draft_time, $limit);
        $this->draft_model->set_draft_deadlines();

        $response["success"] = True;

        echo json_encode($response);


    }

    function ajax_toggle_auto_start()
    {
        $response = array('success' => False);
        $response['value'] = $this->draft_model->toggle_auto_start();
        $response['success'] = True;

        echo json_encode($response);

    }

    function ajax_delete_pick()
    {
        $response = array('success' => False);
        $pick_id = $this->input->post('pick_id');

        $this->draft_model->delete_draft_pick($pick_id);
        $response['success'] = True;

        echo json_encode($response);
    }

    function future()
    {
        $data = array();

        $data['pick_years'] = $this->draft_model->get_future_pick_years_array();
        $data['default_num_rounds'] = $this->draft_model->get_default_num_rounds();

        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Future"] = "";
        $this->admin_view('admin/draft/future',$data);
    }

    function future_manage($year)
    {
        $data = array();
        $data['picks'] = $this->draft_model->get_future_picks_data($year);

        $this->bc["Draft"] = site_url('admin/draft');
        $this->bc["Future"] = site_url('admin/draft/future');
        $this->bc[$year] = "";
        $this->admin_view('admin/draft/future_manage',$data);
    }

    function ajax_create_future_picks()
    {
        $response = array();
        $year = $this->input->post('year');
        $rounds = $this->input->post('rounds');
        if (!$this->draft_model->future_year_exists($year))
            $this->draft_model->create_future_year($year, $rounds);

        echo json_encode($response);
    }

}
?>
