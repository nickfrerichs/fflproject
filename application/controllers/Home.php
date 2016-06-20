<?php

class Home extends MY_Basic_Controller{

    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('Flexi_auth');
        $this->load->helper('form');
    }

    public function index()
    {
        $data = array();
        if ($this->input->get('redirect'))
        {
            $data['redirect'] = $this->input->get('redirect');
        }
        // If 'Login' form has been submited, attempt to log the user in.

        if ($this->flexi_auth->ip_login_attempts_exceeded())
	    {
            $this->config->load('fflproject');
            $data['use_recaptcha'] = $this->config->item('use_recaptcha');
            if ($data['use_recaptcha'])
                $data['captcha'] = $this->flexi_auth->recaptcha(FALSE);
            else
                $data['captcha'] = $this->flexi_auth->math_captcha();
        }

        if (!$this->flexi_auth->is_logged_in())
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
