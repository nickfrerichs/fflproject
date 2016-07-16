<?php

class Transactions extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/leaguesettings_model');
        $this->load->model('admin/transactions_model');
        $this->bc["League Admin"] = "";
        $this->bc["Waiver Wire & Trades"] = "";
    }

    function index()
    {
    	$data = array();
    	$data['settings'] = $this->leaguesettings_model->get_league_settings_data();
        $data['approvals'] = $this->transactions_model->get_pending_ww_approvals();
    	$this->admin_view('admin/transactions/transactions',$data);
    }

    function ajax_save_setting()
    {
        $response = array('success' => False);
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        if ($type == 'wwcleartime')
            $value = $value*60*60;
        $this->leaguesettings_model->change_setting(0,$type,$value);

        $response['success'] = True;
        echo json_encode($response);
    }

    function ww_approve()
    {
        $id = $this->input->post('id');
        $result = $this->transactions_model->approve_ww($id);

        echo json_encode($result);
    }

    function ww_reject()
    {
        $result = array();
        $id = $this->input->post('id');
        $this->transactions_model->reject_ww($id);
        echo json_encode($result);
    }

    function set_ww_approval_setting()
    {
        $response = array('success' => false);
        $value = $this->input->post('value');
        $response['value'] = $value;
        $this->transactions_model->set_ww_approval_setting($value);
        $response['success'] = True;
        echo json_encode($response);
    }

    function test()
    {
        $this->transactions_model->set_ww_approval_setting("auto");
    }

}
