<?php

class Auth extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('Flexi_auth');
    }

    function index()
    {
        if($this->flexi_auth->is_logged_in())
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

            if ($this->flexi_auth->ip_login_attempts_exceeded())
            {
                $this->form_validation->set_rules('recaptcha_response_field', 'Captcha Answer', 'required|validate_recaptcha');
            }

            if ($this->form_validation->run())
            {
                    // Check if user wants the 'Remember me' feature enabled.
                    $remember_user = ($this->input->post('remember_me') == 1);
                    // Verify login data.

                    $this->flexi_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), $remember_user);

                    $this->session->set_userdata('user_id', $this->flexi_auth->get_user_id());
                    $this->load->model('security_model');
                    $this->security_model->set_session_variables();


                    // Save any public status or error messages (Whilst suppressing any admin messages) to CI's flash session data.
                    $this->session->set_flashdata('message', $this->flexi_auth->get_messages());

                    // Reload page, if login was successful, sessions will have been created that will then further redirect verified users.
                    if ($this->input->post('redirect'))
                        redirect($this->input->post('redirect'));
                    else
                        redirect('home');
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
        $this->flexi_auth->logout(TRUE);

        // Set a message to the CI flashdata so that it is available after the page redirect.
        $this->session->set_flashdata('message', $this->flexi_auth->get_messages());

        redirect(site_url());
    }
}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
