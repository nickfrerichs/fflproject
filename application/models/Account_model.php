<?php

class Account_model extends CI_Model{

    function add_team($user_id, $team_name, $league_id)
    {
        // $user_id = $this->db->select('uacc_id')->from('user_accounts')->
        //         where('uacc_email', $email_address)->get()->row()->uacc_id;

        $owner_id = $this->db->select('owner.id as owner_id')->from('user_accounts')->join('owner','owner.user_accounts_id = user_accounts.id')
            ->where('user_accounts.id',$user_id)->get()->row()->owner_id;

        $chat_key = $this->db->select('chat_key')->from('league_settings')->where('league_id',$league_id)->get()->row()->chat_key;

        if ($this->db->from('team')->where('owner_id',$owner_id)->where('league_id',$league_id)->get()->num_rows() == 0)
        {
            $data = array('owner_id' => $owner_id, 'league_id' => $league_id, 
                          'team_name' => $team_name, 'long_name' => $team_name, 'active' => 1, 'chat_read' => $chat_key);
            $this->db->insert('team', $data);
        }

        if ($this->db->from('owner_setting')->where('owner_id',$owner_id)->where('league_id',$league_id)->get()->num_rows() == 0)  
        {
            $data = array('owner_id' => $owner_id, 'league_id' => $league_id);
            $this->db->insert('owner_setting',$data);
        }
    }

    function add_owner($user_id)
    {
        $row = $this->db->select('first_name, last_name')->from('user_accounts')->where('id',$user_id)->get()->row();
        if ($row)
        {
            $first_name = $row->first_name;
            $last_name = $row->last_name;
        
            $data = array('user_accounts_id' => $user_id, 'first_name' => $first_name,
                        'last_name' => $last_name);
            $this->db->insert('owner', $data);
            return $this->db->insert_id();
        }
        return False;
    }

    function set_active_league($user_id, $leagueid)
    {
        $data = array('active_league' => $leagueid);
        $this->db->where('user_accounts_id',$user_id);
        $this->db->update('owner',$data);
    }

    function get_email_from_id($id)
    {
        return $this->db->select('email')->from('user_accounts')->where('id',$id)->get()->row()->email;
    }

    function get_username_from_email($email_address)
    {
        $row = $this->db->select('username')->from('user_accounts')->where('email',$email_address)->get()->row();
        if (count($row) > 0)
        {
            return $row->username;
        }
        return False;
    }

    function get_league_id($maskid, $code='')
    {
        $this->db->select('league.id')->from('league')->join('league_settings','league_settings.league_id = league.id')
            ->where('league.mask_id',$maskid)->where('league_settings.join_password',$code);
        $result = $this->db->get()->row();
        if(count($result) > 0)
            return $result->id;
        return -1;
    }

    function admin_account_exists()
    {
        if ($this->db->from('user_memberships')->where('group_id',1)->get()->num_rows() > 0)
            return True;
        return False;
    }

    function user_is_owner($user_id)
    {
        $result = $this->db->select('id')->from('owner')->where('user_accounts_id',$user_id)->get()->result();
        if (count($result) > 0)
            return True;
        return False;
    }

    function ok_to_join_league($user_id,$league_id)
    {
        // Check to see if user already has a team in this league.
        $num = $this->db->from('team')->join('owner','team.owner_id = owner.id')
            ->where('team.league_id',$league_id)->where('owner.user_accounts_id',$user_id)
            ->get()->num_rows();
        if ($num > 0)
            return False;
        return True;
    }

    function identity_in_use($col,$identity)
    {
        $num = $this->db->select('id')->from('user_accounts')->where($col,$identity)->get()->num_rows();
        if ($num > 0)
            return True;
        return False;

    }

    function get_site_name()
    {
        return $this->db->select('name')->from('site_settings')->get()->row()->name;
    }

    function join_code_ok($leagueid, $code)
    {
        $actual_code = $this->db->select('join_password')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row()->join_password;

        if ($actual_code == $code)
            return True;
        return False;
    }

    // This function checks credentials against the legacy auth hash functions
    function legacy_check_password($username, $verify_password)
    {
        $legacy_info = $this->db->select('password, salt')->from('user_accounts')->where('username',$username)
            ->get()->row();
        if($legacy_info)
        {
            require_once(APPPATH.'libraries/phpass/PasswordHash.php');
            require_once(FCPATH.'config.php');

            $database_salt = $legacy_info->salt;
            $database_password = $legacy_info->password;
            $hash_token = new PasswordHash(8, FALSE);

            return $hash_token->CheckPassword($database_salt . $verify_password . $this->config->item('fflp_salt'), $database_password);
        }
        return False;
    }

    function convert_legacy_password($username, $password)
    {
        $this->load->model('ion_auth_model');

        // Once set in ion_auth, delete user from legacy auth table
        if ($this->ion_auth_model->reset_password($username, $password))
        {
            return True;
        }
    }

    function login_speedbump_needed($username="",$ip_address="")
    {
        if ($ip_address == "")
            $ip_address = $this->input->ip_address();
        // More than 10 login attempts from an IP in 5 mins
        $num = $this->db->from('user_login_attempts')->where('ip_address',$ip_address)->where('time>',(time()-600))
            ->count_all_results();

        if ($num >= 10)
            return True;

        // More than 5 login attempts for a user in 5 mins
        if($username != "")
        {
            $num = $this->db->from('user_login_attempts')->where('login',$username)->where('time>',(time()-300))
                ->count_all_results();

            if ($num >= 5)
                return True;
        }

        return False;
    }

    // Ported over from old flexi auth implementation
	public function get_math_captcha()
	{
		$min_operand_val = 1;
		$max_operand_val = 20;
		$total_operands = 2;
		$operators = array('+'=>' plus ', '-'=>' minus ');
		$equation = '';
        for ($i = 1; $total_operands >= $i; $i++)
		{
			$operand = rand($min_operand_val, $max_operand_val);
			$operator = ($i < $total_operands) ? array_rand($operators) : '';
			$equation .= $operand.$operator;
		}
		// Convert equation symbols to written symbols.
		$captcha['equation'] = str_replace(array_keys($operators), array_values($operators), $equation);
		// Convert equation string.
		eval("\$captcha['answer'] = ".$equation.";");

        $this->session->set_flashdata('fflp_math_captcha', $captcha['answer']);

        return $captcha['equation'];
    }

    // Ported over from old flexi auth implementation
    public function validate_math_captcha($answer = FALSE)
	{
		return ($answer == $this->session->flashdata('fflp_math_captcha'));
    }
    
    public function get_recaptcha($ssl=FALSE)
    {
        $this->load->helper('recaptcha_helper');
        require_once(FCPATH.'config.php');
        return recaptcha_get_html($this->config->item('fflp_recaptcha_public_key'), NULL, $ssl);
    }

	public function validate_recaptcha()
	{
        $this->load->helper('recaptcha_helper');
        require_once(FCPATH.'config.php');
		$response = recaptcha_check_answer(
			$this->config->item('fflp_recaptcha_private_key'),
			$this->input->ip_address(),
			$this->input->post('recaptcha_challenge_field'),
			$this->input->post('recaptcha_response_field')
		);
		return $response->is_valid;
	}    
}
