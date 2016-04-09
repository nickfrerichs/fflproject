<?php

class Leaguesettings extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/leaguesettings_model');
    }

    function index()
    {
        $leagueid = $this->session->userdata('league_id');
        $data = array();
        $data['settings'] = $this->leaguesettings_model->get_league_settings_data($leagueid);
        $this->admin_view('admin/leaguesettings/leaguesettings',$data);
    }

}
?>
