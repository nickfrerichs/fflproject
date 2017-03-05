<?php

class Past_seasons extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();

        $this->load->model('admin/admin_security_model');
        $this->bc["League Admin"] = "";
        $this->bc["Past Seasons"] = "";
    }

    function index()
    {
        $data = array();
        $data['years'] = $this->common_model->get_league_years();
        $this->admin_view('admin/past_seasons/past_seasons',$data);

    }

    function year($selected_year)
    {
        $data = array();
        $data['selected_year'] = $selected_year;
        $data['years'] = $this->common_model->get_league_years();
        $this->bc['Past Seasons'] = site_url("admin/past_seasons");
        $this->bc[$selected_year] = "";

        $this->admin_view('admin/past_seasons/show_past_season',$data);
    }

}
?>