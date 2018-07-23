<?php

class User extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('account_model');

        if ($this->session->userdata('debug') && !$this->input->is_ajax_request())
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

    function test()
    {
//        $this->load->model('season/standings_model');
        $this->load->model('security_model');
        $this->security_model->set_user_notifications();
    }

    function joinleague($mask_id="")
    {
        $this->load->model('common/common_noauth_model');  
        $data = array();

        if ($this->common_noauth_model->valid_mask($mask_id) && $this->common_noauth_model->league_has_room($mask_id))
        {
            $data['is_owner'] = $this->account_model->user_is_owner($this->session->userdata('user_id'));
            $data['mask_id'] = $mask_id;
            $data['join_league_id'] = $this->account_model->get_league_id($mask_id);
            $data['code_required'] = $this->common_noauth_model->join_code_required($mask_id);
            $data['league_name'] = $this->common_noauth_model->league_name_from_mask_id($mask_id);
            // Kinda awkard, need to reconstruct user_view function from MY_Controller class
            $this->load->library('ion_auth');
            $this->load->model('menu_model');
            $data['menu_items'] = $this->menu_model->get_menu_items_data();
            $data['v'] = 'user/join_league';
            $data['bc'] = array('Join League' => "");
            $data['_notifications'] = array();
            $this->load->view('template/user_init',$data);
        }
        else
        {
            redirect('/');
        }
    }

    function do_joinleague($mask_id)
    {
        $response = array("success" => false, "error" => false, "message" => false);

        //$mask_id = $this->input->post('mask_id');
        $code = '';
        if($this->input->post('join_code'))
            $code = $this->input->post('join_code');

		$team_name = $this->input->post('team_name');
		$user_id = $this->session->userdata('user_id');

    	$this->load->model('account_model');
        $this->load->model('common/common_noauth_model');
        $leagueid = $this->account_model->get_league_id($mask_id, $code);
        $has_room = $this->common_noauth_model->league_has_room($mask_id);
        if (!$has_room)
        {
            $response['error'] = "League max teams reached.";
        }
        elseif ($leagueid == -1)
        {
            $response['error'] = "League password incorrect.";
        }
        elseif(!$this->session->userdata('leagues') || !array_key_exists($leagueid,$this->session->userdata('leagues')))
        {
            // You can't join, cause this account is already an owner.
        	// Make sure the league exists
        	if ($leagueid > 0)
        	{
        		if ($this->account_model->user_is_owner($user_id) == False)
        		{
        			$this->account_model->add_owner($user_id);
        		}

    			$this->account_model->add_team($user_id, $team_name, $leagueid);
    			$this->account_model->set_active_league($user_id, $leagueid);
    			$this->load->model('security_model');
    			$this->security_model->set_session_variables();
                $response['success'] = True;
                $response['message'] = "League joined: ".$this->common_noauth_model->league_name_from_mask_id($mask_id);
        	}
        }
        else
        {
            $response['error'] = 'You are already in this league.';
        }

        echo json_encode($response);
    }

}

?>
