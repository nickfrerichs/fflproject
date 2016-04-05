<?php
class Settings extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/myteam_settings_model');
        $this->load->library('flexi_auth', FALSE, 'flexi_auth_full');
    }

    function index()
    {
        $data = array();
        $data['owner_info'] = $this->myteam_settings_model->get_owner_info();
        $data['team_info'] = $this->myteam_settings_model->get_team_info();
        $data['team_uploaded_logo_url'] = site_url('images/team_logos/'.$this->teamid."_uploaded_logo.jpg");
        $data['team_thumb_logo_url'] = site_url('images/team_logos/'.$this->teamid."_thumb_logo.jpg");
    	$this->user_view('user/myteam/settings',$data);
    }

    function ajax_change_password()
    {
        $identity="none";
        $curpass = $this->input->post('curpass');
        $newpass = $this->input->post('newpass');
        $identity = $this->myteam_settings_model->get_owner_identity();
        //echo "curpass".$curpass;
        echo $this->flexi_auth_full->change_password($identity, $curpass, $newpass);

    }

    function ajax_change_item()
    {
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        if($type == "teamname")
            $result = $this->myteam_settings_model->change_team_name($value);
        if($type == 'phone')
            $result = $this->myteam_settings_model->change_owner_phone($value);

        $this->load->model('security_model');
        $this->security_model->set_session_variables();

        echo $result;

    }

    function ajax_upload_logo()
    {
        $f = $_FILES['files'];
        $path = $this->myteam_settings_model->get_logo_path();
        $this->myteam_settings_model->save_uploaded_logo($f['tmp_name'][0], $f['name'][0]);

    }

    function ajax_crop_team_logo()
    {
        $cropData = $this->input->post('cropData');
        $this->myteam_settings_model->crop_uploaded_logo($cropData);
        //print_r($cropData);
    }

    function ajax_change_current_league()
    {
        $leagueid = $this->input->post('leagueid');
        if ($this->myteam_settings_model->member_of_league($leagueid))
        {
            $this->myteam_settings_model->set_current_league($leagueid);
            $this->load->model('security_model');
            $this->security_model->set_session_variables();
        }
    }
}

?>
