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
        $id = $this->input->post('id');
        $value = $this->input->post('value');

        if ($id == '#wwcleartime')
            $value = $value*60*60;
        // if ($id == '#wwdays' && !$this->leaguesettings_model->wwdays_is_valid($value))
        // {
        //     $response['success'] = False;
        //     echo json_encode($response);
        //     return;
        // }

        $this->leaguesettings_model->change_setting(0,$id,$value);
        $response['value'] = $value;
        if ($id == '#wwcleartime')
            $response['value'] = $value/60/60;

        $response['success'] = True;
        echo json_encode($response);
    }

    function ajax_toggle_wwday()
    {   
        $response = array('success' => False);
        // This will be 0,1,2,3,4,5,6
        $day = $this->input->post('var1');
        if (!in_array($day,array('0','1','2','3','4','5','6')))
        {
            $response['success'] = False;
            echo json_encode($response);
            return;
        }

        $response['value'] = $this->transactions_model->toggle_wwday($day);
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
        $result = array('success' => false);
        $id = $this->input->post('id');
        $this->transactions_model->reject_ww($id);
        $result['success'] = true;
        echo json_encode($result);
    }

    function set_ww_approval_setting()
    {
        $response = array('success' => false);
        $value = $this->input->post('value');
        $this->transactions_model->set_ww_approval_setting($value);
        $response['value'] = $value;
        $response['success'] = True;
        echo json_encode($response);
    }

    function test()
    {
        $this->transactions_model->set_ww_approval_setting("auto");
    }

}
