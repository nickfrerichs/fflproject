<?php

class Register_old extends CI_Controller{





function register_account()
{

                         $this->auth = new stdClass;
                         $this->load->library('flexi_auth');

			// Get user login details from input.
			$email = 'admin@admin.com';
			$username = 'admin';
			$password = 'fflproject';

			// Get user profile data from input.
			// You can add whatever columns you need to customise user tables.
			$profile_data = array(
				'upro_first_name' => 'admin',
				'upro_last_name' => 'admin',
				'upro_phone' => '123-456-7890'
				);


			// Set whether to instantly activate account.
			// This var will be used twice, once for registration, then to check if to log the user in after registration.
			$instant_activate = TRUE;

			// The last 2 variables on the register function are optional, these variables allow you to:
			// #1. Specify the group ID for the user to be added to (i.e. 'Moderator' / 'Public'), the default is set via the config file.
			// #2. Set whether to automatically activate the account upon registration, default is FALSE.
			// Note: An account activation email will be automatically sent if auto activate is FALSE, or if an activation time limit is set by the config file.
			$response = $this->flexi_auth->insert_user($email, $username, $password, $profile_data, 1, $instant_activate);

			if ($response)
			{
				// This is an example 'Welcome' email that could be sent to a new user upon registration.
				// Bear in mind, if registration has been set to require the user activates their account, they will already be receiving an activation email.
				// Therefore sending an additional email welcoming the user may be deemed unnecessary.
				$email_data = array('identity' => $email);
				$this->flexi_auth->send_email($email, 'Welcome', 'registration_welcome.tpl.php', $email_data);
				// Note: The 'registration_welcome.tpl.php' template file is located in the '../views/includes/email/' directory defined by the config file.

				###+++++++++++++++++###

				// Save any public status or error messages (Whilst suppressing any admin messages) to CI's flash session data.
				$this->session->set_flashdata('message', $this->flexi_auth->get_messages());

				// This is an example of how to log the user into their account immeadiately after registering.
				// This example would only be used if users do not have to authenticate their account via email upon registration.
				if ($instant_activate && $this->flexi_auth->login($email, $password))
				{
					// Redirect user to public dashboard.
					redirect('auth_public/dashboard');
				}

				// Redirect user to login page
				redirect('auth');
			}

		return FALSE;
}

}
