<?php

class Accounts extends MY_Basic_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('account_model');
        if ($this->ion_auth->logged_in())
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
        if ($data['admin_exists'])
            $data['register_url'] = site_url('accounts/ajax_register/'.$maskid);
        else
            $data['register_url'] = site_url('accounts/ajax_register_admin/');
        $data['site_name'] = $this->common_noauth_model->get_site_name();
        if($league_id > 0)
            $data['league_name'] = $this->common_noauth_model->league_name_from_mask_id($maskid);
        $data['code_required'] = $code_required;
        $data['maskid'] = $maskid;
        $this->basic_view('guest_register',$data);
    }

    function ajax_register_admin()
    {
        $data = array('success' => False, 'error' => False);
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email_address');
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $form_reqs_met = form_require(array($first_name,$last_name,$email,$username,$password));

        // Do error checking first to make sure valid data was submitted
        if ($this->account_model->admin_account_exists())
            $data['error'] = "Admin account already exists.";
        elseif (!$form_reqs_met)
            $data['error'] = "Form missing required values.";
        elseif($this->account_model->identity_in_use("username",$username))
            $data['error'] = "Username is already taken.";
        elseif($this->account_model->identity_in_use("email",$email))
            $data['error'] = "Email address is already registered.";

        // If error is still false, process the registration
        if(!$data['error'])
        {
            $additional_data = array(
                'first_name' => $first_name,
                'last_name' => $last_name
            );
            // Site admin is in both admin and user groups
            $group_ids = array(1,2);

            // Skip activation since it's the admin account
            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $group_ids, TRUE);

            $data['success'] = True;
            $data['message'] = 'Admin account created.';
        }
        echo json_encode($data);
    }

    function ajax_register($maskid="")
    {
        $data = array('success' => False, 'error' => False);
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email_address');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $team_name = $this->input->post('team_name');
        $code = $this->input->post('league_password');
        $league_id = $this->account_model->get_league_id($maskid);

        $form_reqs_met = form_require(array($first_name,$last_name,$email,$username,$password,$team_name));

        // Do error checking first to make sure valid data was submitted
        if (!$this->account_model->admin_account_exists())
            $data['error'] = "Must register admin account first.";
        elseif ($league_id < 1)
            $data['error'] = "Invalid league ID.";
        elseif (!$form_reqs_met)
            $data['error'] = "Form missing required values.";
        elseif (!$this->common_noauth_model->league_has_room($maskid))
            $data['error'] = "League team limit reached.";
        elseif ($code && $this->account_model->join_code_ok($league_id,$code) == false)
            $data['error'] = "Incorrect League Password";
        elseif($this->account_model->identity_in_use("username",$username))
            $data['error'] = "Username is already taken.";
        elseif($this->account_model->identity_in_use("email",$email))
            $data['error'] = "Email address is already registered.";

        // If error is still false, process the registration
        if(!$data['error'])
        {
            $additional_data = array(
                'first_name' => $first_name,
                'last_name' => $last_name
            );
            # If league_id is 0, no admin exists, make this account an admin
            $group_ids = array();
            $group_id[] = 2;

            //$response = $this->flexi_auth->insert_user($email, $username, $password, $profile_data, $group_id, $instant_activate);
            $user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $group_ids);

            # If league_id is 0, this is the first user and there are no leagues yet.
            if ($this->account_model->ok_to_join_league($user_id,$league_id))
            {
                // Weird, but user_id is passed to all three of these, they figure out the owner_id if needed
                $this->account_model->add_owner($user_id, $first_name, $last_name);
                $this->account_model->add_team($user_id, $team_name, $league_id);
                $this->account_model->set_active_league($user_id, $league_id);

                $data['success'] = True;
                $data['message'] = 'A confirmation email was sent to '.$email;
            }
            else
            {
                $data['error'] = 'User already has a team in this league.';
            }
        }
        echo json_encode($data);
    }

    function ajax_forgot()
    {
        $data['success'] = False;
        $email_address = $this->input->post('email');
        $data['email'] = $email_address;
        $username = $this->account_model->get_username_from_email($email_address);
        if($username)
        {
            $this->ion_auth->forgotten_password($username);
            $data['success'] = True;
            $data['sent'] = True;
        }
        else{
            $data['success'] = True;
            $data['sent'] = False;
        }
        echo json_encode($data);
    }

    // function forgot()
    // {
    //     $data = array();
    //     if ($this->input->post('email_address'))
    //     {
    //         $this->flexi_auth->forgotten_password($this->input->post('email_address'));
    //         $data['sent'] = true;
    //     }
    //     $data['site_name'] = $this->common_noauth_model->get_site_name();
    //     $this->load->helper('form');
    //     $this->basic_view('guest_forgot',$data);
    // }

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
        if (!$this->ion_auth->forgotten_password_check($token))
            redirect(site_url(''));
        $data = array('user_id' => $user, 'reset_token' => $token);

        // This executes on a post supplying the new password
        if (($this->input->post('password1')) == ($this->input->post('password2')) && $this->input->post('submit'))
        {
            $pw1 = $this->input->post('password1');
            $pw2 = $this->input->post('password2');
            $profile = $this->ion_auth->forgotten_password_check($token);
            if ($profile)
            {
                $this->ion_auth_model->reset_password($profile->username,$pw1);
                $this->session->sess_destroy();
                redirect(site_url(''));
            }
        }
        $data['site_name'] = $this->common_noauth_model->get_site_name();
        $this->basic_view('reset_password',$data);
    }

    function activate($user_id, $activate_code)
    {
        if ($this->ion_auth->activate($user_id, $activate_code))
        {
            $data['site_name'] = $this->common_noauth_model->get_site_name();
            $this->basic_view('activate_success',$data);
        }
        else
            redirect(site_url(''));
    }

}
