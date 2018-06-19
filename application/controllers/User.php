<?php

class User extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');
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

    function do_joinleague()
    {
        $response = array("success" => false);

		$mask_id = $this->input->post('mask_id');
		$code = $this->input->post('code');
		$first = $this->input->post('first');
		$last = $this->input->post('last');
		$team_name = $this->input->post('team_name');
		$user_id = $this->session->userdata('user_id');

    	$this->load->model('account_model');
        $this->load->model('common/common_noauth_model');
        $leagueid = $this->account_model->get_league_id($mask_id, $code);
        $has_room = $this->common_noauth_model->league_has_room($mask_id);
        if (!$has_room)
        {
            $response['msg'] = "League max teams reached.";
        }
        elseif ($leagueid == -1)
        {
            $response['msg'] = "League password incorrect.";
        }
        elseif(!$this->session->userdata('leagues') || !array_key_exists($leagueid,$this->session->userdata('leagues')))
        {
            // You can't join, cause this account is already an owner.
        	// Make sure the league exists
        	if ($leagueid > 0)
        	{
        		if ($this->account_model->user_is_owner($user_id) == False)
        		{
        			$this->account_model->add_owner($user_id, $first, $last);
        		}

    			$this->account_model->add_team($user_id, $team_name, $leagueid);
    			$this->account_model->set_active_league($user_id, $leagueid);
    			$this->load->model('security_model');
    			$this->security_model->set_session_variables();
                $response['success'] = True;
        	}
        }

        echo json_encode($response);
    }

}

?>
