<?php

class Auth extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
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

            // CAPTCHA FROM FLEXI AUTH, NEEDS TO BE UPDATED TO ION_AUTH
            // if ($this->flexi_auth->ip_login_attempts_exceeded())
            // {
            //     $this->config->load('fflproject');
            //     if ($this->config->item('use_recaptcha'))
            //         $this->form_validation->set_rules('recaptcha_response_field', 'Captcha Answer', 'required|validate_recaptcha');
            //     else
            //     {

            //         if (!$this->flexi_auth->validate_math_captcha($this->input->post('math_captcha_response_field')))
            //             redirect('');
            //     }
            // }

            if ($this->form_validation->run())
            {
                    // 1. First try to auth with ion_auth, if it doesn't work, auth with flexi_auth and save the password 
                    // in ion auth as part of the migration

                    // Check if user wants the 'Remember me' feature enabled.
                    $remember_user = ($this->input->post('remember_me') == 1);
                    // Verify login data.

                    $success = $this->ion_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), $remember_user);

                    if ($success)
                        echo "Ion Success!";
                    else
                    {
                        // Try legacy auth
                        $this->load->model('account_model');
                        $legacy_success = $this->account_model->legacy_check_password($this->input->post('login_identity'),
                                                                                      $this->input->post('login_password'));
                        if ($legacy_success)
                        {
                            $this->account_model->convert_legacy_password($this->input->post('login_identity'), $this->input->post('login_password'));
                            // Set password in ion auth, delete user from legacy user table.
                            
                            // Now login via ion auth
                            $this->ion_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), True);

                            // Delete user from legacy user table
                            echo "Legacy Success!";
                        }
                        else
                        {
                            echo "Legacy Failed!";
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
                        redirect('home');
                    }
            }
            else
            {
                    // Set validation errors.
                    $data['message'] = validation_errors('<p class="error_msg">', '</p>');
                    redirect('guest');
            }
        }
    }

    function logout()
    {
        $this->ion_auth->logout();
        // Set a message to the CI flashdata so that it is available after the page redirect.
        $this->session->sess_destroy();

        redirect(site_url());
    }
}
