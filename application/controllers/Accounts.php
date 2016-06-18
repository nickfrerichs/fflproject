<?php

class Accounts extends MY_Basic_Controller{

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
    }

    function index()
    {
        redirect(site_url(''));
    }

    function register($maskid="")
    {
        $data = array();
        if (!$this->account_model->admin_account_exists())
            $league_id = 0;
        else
            $league_id = $this->account_model->get_league_id($maskid);

        $code_required = $this->common_noauth_model->join_code_required($maskid);
        $valid_mask = $this->common_noauth_model->valid_mask($maskid);

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
                $code = $this->input->post('league_password');
                $instant_activate = TRUE;
                if ($league_id > 0 && !$this->common_noauth_model->league_has_room($maskid))
                {
                    $data['error'] = "League team limit reached.";
                }
                elseif ($league_id > 0 && $code && $this->account_model->join_code_ok($league_id,$code) == false)
                {
                    $data['error'] = "Incorrect League Password";
                }
                else
                {
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


                    # If league_id is 0, this is the first user and there are no leagues yet.
                    if ($response && $league_id > 0 && $this->account_model->ok_to_join_league($response,$league_id))
                    {
                        $this->account_model->add_owner($response, $first_name, $last_name);
                        $this->account_model->add_team($response, $team_name, $league_id);
                        $this->account_model->set_active_league($response, $league_id);
                    }
                    if ($response)
                        redirect(site_url());
                }
            }
        }
        else
        {
            if ($valid_mask && $code_required)
                $data['error'] = "League password is invalid.";
            else
            {
                echo "Invalid url.";
                return;
            }

            //echo "<center>You need to use a valid invite link to register.</center>";
        }
        $this->load->helper('form');
        $data['admin_exists'] = $this->account_model->admin_account_exists();
        $data['site_name'] = $this->common_noauth_model->get_site_name();
        $data['code_required'] = $code_required;
        $data['maskid'] = $maskid;
        $this->basic_view('guest_register',$data);
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
        $data['site_name'] = $this->common_noauth_model->get_site_name();
        $this->load->helper('form');
        $this->basic_view('guest_forgot',$data);
    }

    function reset_confirm($user="", $token="")
    {
        $email = "";

        if ($this->flexi_auth->forgotten_password_complete($user, $token, '', $email))
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
        $data['site_name'] = $this->common_noauth_model->get_site_name();
        $this->basic_view('reset_password',$data);
    }

}
