<?php

class User extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('account_model');
    }

    function joinleague($mask_id="", $code="")
    {
        $data = array();
        $data['is_owner'] = $this->account_model->user_is_owner($this->session->userdata('user_id'));
        $data['mask_id'] = $mask_id;
        $data['code'] = $code;
        $this->user_view('user/join_league',$data);
    }

    function do_joinleague()
    {
		$mask_id = $this->input->post('mask_id');
		$code = $this->input->post('code');
		$first = $this->input->post('first');
		$last = $this->input->post('last');
		$team_name = $this->input->post('team_name');
		$user_id = $this->session->userdata('user_id');

    	$this->load->model('account_model');

    	$leagueid = $this->account_model->get_league_id($mask_id, $code);

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
    	}
    }

}

?>
