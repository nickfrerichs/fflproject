<?php

class Home extends MY_Admin_Controller
{

	function __construct()
    {
        parent::__construct();
        $this->load->model('admin/site_model');

        $this->bc["Admin"] = "";
	}

    function index()
    {
    	$data = array();
    	if ($this->is_league_admin)
	    	redirect('admin/teams');
        $data['has_leagues'] = $this->site_model->has_leagues();
        $data['nfl_schedule_status'] = $this->site_model->get_nfl_schedule_status();
        $this->admin_view('admin/home',$data);
    }
}
?>
