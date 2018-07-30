<?php

class Auth extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('account_model');


        // if (1==1)
        // {
        //     $sections = array(
        //         'benchmarks' => TRUE, 'memory_usage' => TRUE,
        //         'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE,
        //         'uri_string' => FALSE, 'http_headers' => TRUE, 'session_data' => TRUE
        //         );
        //         $this->output->set_profiler_sections($sections);
        //         $this->output->enable_profiler(TRUE);
        //         echo "here";
        // }
    }

    function index()
    {
        if($this->ion_auth->logged_in())
            redirect('home');
        elseif($this->input->post('login_user'))
            $this->login();
        else
            redirect('home');
    }

    function login()
    {
        if ($this->input->post('login_identity'))
        {
            $this->load->library('form_validation');

            // Set validation rules.
            $this->form_validation->set_rules('login_identity', 'Identity (Email / Login)', 'required');
            $this->form_validation->set_rules('login_password', 'Password', 'required');

            // Here's where we check if a captcha was displayed an needs to be evaluated
            if ($this->account_model->login_speedbump_needed())
            {
                $this->config->load('fflproject');
                if ($this->config->item('use_recaptcha'))
                {
                    $this->form_validation->set_rules('g-recaptcha-response', '<b>Captcha</b>', 'callback_getCaptchaResponse');
                }
                else
                {
                    if (!$this->account_model->validate_math_captcha($this->input->post('math_captcha_response_field')))
                         redirect('');
                }

            }

            if ($this->form_validation->run())
            {
                    // 1. First try to auth with ion_auth, if it doesn't work, auth with flexi_auth and save the password 
                    // in ion auth as part of the migration

                    // Check if user wants the 'Remember me' feature enabled.
                    $remember_user = ($this->input->post('remember_me') == 1);
                    // Verify login data.

                    $success = $this->ion_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), $remember_user);

                    if ($success)
                    {
                        // Successfully logged in using ion auth
                    }
                    else
                    {
                        // If failed, try this password with legacy auth hash.
                        $legacy_success = $this->account_model->legacy_check_password($this->input->post('login_identity'),
                                                                                      $this->input->post('login_password'));
                        if ($legacy_success)
                        {
                            // Set password in ion auth using the one we just got, ion auth will succeed for this user going forward.
                            $this->account_model->convert_legacy_password($this->input->post('login_identity'), $this->input->post('login_password'));
                            
                            // Now login via ion auth
                            $this->ion_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), True);
                        }
                        else
                        {
                            // Legacy auth failed too, redirect to base url and they'll be prompted again
                            redirect('');
                        }
                    }

                    $this->session->set_userdata('user_id', $this->ion_auth->get_user_id());
                    $this->load->model('security_model');

                    $this->security_model->set_session_variables();

                    // Save any public status or error messages (Whilst suppressing any admin messages) to CI's flash session data.
                    $this->session->set_flashdata('message', $this->ion_auth->errors());

                    //Reload page, if login was successful, sessions will have been created that will then further redirect verified users.
                    if ($this->input->post('redirect') != null)
                        redirect($this->input->post('redirect'));
                    else
                    {
                        redirect('');
                    }
            }
            else
            {
                    // // Set validation errors.
                    // $data['message'] = validation_errors('<p class="error_msg">', '</p>');
                    redirect('');
            }
        }
    }

    function logout()
    {
        // Ion auth logout also destroys the session
        $this->ion_auth->logout();
        redirect(site_url());
    }

	public function getCaptchaResponse($str){
        $this->load->library('recaptcha');
		$response = $this->recaptcha->verifyResponse($str);
		if ($response['success'])
		{     
			return true;
        }     
        else
        {
			$this->form_validation->set_message('getCaptchaResponse', '%s '. var_dump($response) );
			return false;
        }
    }
}
