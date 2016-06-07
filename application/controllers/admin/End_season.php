<?php

class End_season extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/end_season_model');
        $this->bc["League Admin"] = "";
        $this->bc["End Season"] = "";
    }

    function index()
    {
    	$data = array();
    	$data['real_year'] = $this->end_season_model->get_real_year();
    	$data['season_appears_finished'] = $this->end_season_model->is_finished();
        $this->admin_view('admin/end_season/end_season',$data);
    }
}
?>