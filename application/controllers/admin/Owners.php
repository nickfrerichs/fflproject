<?php

class Owners extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->bc["League Admin"] = "";
        $this->bc["owners"] = "";
    }

    function index()
    {
        $this->load->model('admin/owners_model');
        $this->load->model('admin/admin_security_model');

        if ($this->admin_security_model->is_league_admin())
        {
            $owners = $this->owners_model->get_league_owners_data();
            $this->admin_view('admin/owners/owners', array('owners' => $owners));
        }
    }
}
?>
