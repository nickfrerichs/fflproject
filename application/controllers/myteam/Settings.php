<?php
class Settings extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/myteam_settings_model');
        $this->bc['My Team'] = "";
        $this->bc['Settings'] = "";
    }

    function index()
    {
        $data = array();
        $data['owner_info'] = $this->myteam_settings_model->get_owner_info();
        $data['team_info'] = $this->myteam_settings_model->get_team_info();
        $data['team_uploaded_logo_url'] = site_url('images/team_logos/'.$this->teamid."_uploaded_logo.jpg?nocache=".time());
        $data['team_thumb_logo_url'] = site_url('images/team_logos/'.$this->teamid."_thumb_logo.jpg?nocache=".time());
    	$this->user_view('user/myteam/settings',$data);
    }

    function ajax_change_password()
    {
        $this->load->model('ion_auth_model');
        $identity="none";
        $curpass = $this->input->post('curpass');
        $newpass = $this->input->post('newpass');
        $identity = $this->myteam_settings_model->get_owner_identity();
        echo $this->ion_auth_model->change_password($identity, $curpass, $newpass);

    }

    function ajax_change_item()
    {
        $type = $this->input->post('id');
        $value = $this->input->post('value');
        $response = array("success" => false, "msg" =>'');
        if($type == "#teamname")
        {
            $response['msg'] = $this->myteam_settings_model->change_team_name($value);
            $response['value'] = $value;
            $response['success'] = true;
        }
        if($type == "#abbreviation")
        {
            $response['msg'] = $this->myteam_settings_model->change_team_abbreviation($value);
            $response['value'] = $value;
            $response['success'] = true;
        }
        if($type == '#phone')
        {
            $response['msg'] = $this->myteam_settings_model->change_owner_phone($value);
            $response['value'] = $value;
            $response['success'] = true;
        }
        if($type == '#email')
        {
            $response['msg'] = $this->myteam_settings_model->change_owner_email($value);
            $response['value'] = $value;
            $response['success'] = true;
        }
        if($type == '#last')
        {
            $response['msg'] = $this->myteam_settings_model->change_owner_lastname($value);
            $response['value'] = $value;
            $response['success'] = true;
        }
        if($type == '#first')
        {
            $response['msg'] = $this->myteam_settings_model->change_owner_firstname($value);
            $response['value'] = $value;
            $response['success'] = true;
        }

        $this->load->model('security_model');
        $this->security_model->set_session_variables();


        echo json_encode($response);

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

    function ajax_toggle_item()
    {
        $item = $this->input->post('id');
        $response = array('success' => False);
        if ($item == "#chat_balloon")
        {
            $response['value'] = $this->myteam_settings_model->toggle_chat_balloon();
            $response['success'] = True;
            $this->load->model('security_model');
            $this->security_model->set_owner_session_variables();
        }
        echo json_encode($response);
    }
}

?>
