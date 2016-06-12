<?php

class Transactions extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/leaguesettings_model');
        $this->bc["League Admin"] = "";
        $this->bc["Waiver Wire & Trades"] = "";
    }

    function index()
    {
    	$data = array();
    	$data['settings'] = $this->leaguesettings_model->get_league_settings_data();
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

}
