<?php

class Home extends MY_Basic_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->helper('form');
    }

    public function index()
    {
        $data = array();
        if ($this->input->get('redirect'))
        {
            $data['redirect'] = $this->input->get('redirect');
        }

        if ($this->ion_auth->logged_in() === FALSE)
        {
            $this->load->model('account_model');
            $data['admin_exists'] = $this->account_model->admin_account_exists();
            $data['site_name'] = $this->account_model->get_site_name();
            // Display login form
            $this->basic_view('guest_view',$data);
        }
        else
        {
            redirect('league/news');
        }
    }
}
