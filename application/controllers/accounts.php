<?php

class Accounts extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('flexi_auth', FALSE);
        $this->load->model('account_model');
        if ($this->flexi_auth->is_logged_in())
        {
                redirect('auth');
        }

        // To load the CI benchmark and memory usage profiler - set 1==1.
        if ((1==0) && (!$this->input->is_ajax_request()))
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE,
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE,
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => TRUE
                );
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }
    }

    function index()
    {
        redirect(site_url(''));
    }

    function register($maskid="", $code = "")
    {
        if (!$this->account_model->admin_account_exists())
            $league_id = 0;
        else
            $league_id = $this->account_model->get_league_id($maskid, $code);

        if ($league_id >= 0)
        {
            // Redirect user away from registration page if already logged in.

            // If 'Registration' form has been submitted, attempt to register their details as a new account.
            if ($this->input->post('register'))
            {
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $email = $this->input->post('email_address');
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $team_name = $this->input->post('team_name');
                $instant_activate = TRUE;

                $profile_data = array(
    				'upro_first_name' => $this->input->post('first_name'),
    				'upro_last_name' => $this->input->post('last_name')
    			);
                # If leage_id is 0, no admin exists, make this account an admin
                if ($league_id == 0)
                    $group_id = 1;
                else
                    $group_id = 2;

                $response = $this->flexi_auth->insert_user($email, $username, $password, $profile_data, $group_id, $instant_activate);

                # If league_id is 0, this is the first user and is only an admin
                if ($response && $league_id != 0)
                {
                    $this->account_model->add_team($email, $team_name, $league_id);
                    $this->account_model->add_owner($email, $first_name, $last_name);
                }
                if ($response)
                    redirect(site_url());
            }
            $this->load->helper('form');
            $this->load->view('guest_register',array('admin_exists' => $this->account_model->admin_account_exists()));
        }
    }

    function forgot()
    {
        $data = array();
        if ($this->input->post('email_address'))
        {

            $this->flexi_auth->forgotten_password($this->input->post('email_address'));
            $data['sent'] = true;
            //$this->flexi_auth->auto_reset_forgotten_password($this->input->post('email_address'));

        }
        $this->load->helper('form');
        $this->load->view('guest_forgot',$data);
    }

    function reset_confirm($user="", $token="")
    {
        $email = "";

        if ($this->flexi_auth->forgotten_password_complete($user, $token, 'imadumbfuck', $email))
        {
            $email = $this->account_model->get_email_from_id($user);
            echo "A new password has been emailed to your email address.";
        }
        else
            redirect(site_url(''));

    }

    // Start of reset function where they set their password on a form
    function reset_password($user="", $token="")
    {
        if ($token == "")
            redirect(site_url(''));
        $data = array('user_id' => $user, 'reset_token' => $token);
        if (($this->input->post('password1')) == ($this->input->post('password2')) && $this->input->post('submit'))
        {
            $pw1 = $this->input->post('password1');
            $pw2 = $this->input->post('password2');
            $this->flexi_auth->forgotten_password_complete($user, $token, $pw1);
            $userdata = array('password' => $pw1);
            $this->session->sess_destroy();
            redirect(site_url(''));
        }
        $this->load->view('reset_password', $data);
    }

}
