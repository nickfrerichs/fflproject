<?php

class Home extends CI_Controller{


    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('Flexi_auth');
        $this->load->helper('form');

        if (1==0)
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE,
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => FALSE,
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => FALSE
                );
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }
    }

    public function index()
    {
        $data = array();
        // If 'Login' form has been submited, attempt to log the user in.

        if ($this->flexi_auth->ip_login_attempts_exceeded())
	    {
            $data['captcha'] = $this->flexi_auth->recaptcha(FALSE);
        }

        if (!$this->flexi_auth->is_logged_in())
        {
            $this->load->model('account_model');
            $data['admin_exists'] = $this->account_model->admin_account_exists();
            // Display login form
            $this->load->view('guest_view', $data);
        }
        else
        {
            redirect('league/news');
        }
    }
}
