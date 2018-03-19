<?php

class Leaguesettings extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/leaguesettings_model');
        $this->bc["League Admin"] = "";
        $this->bc['Settings'] = "";
    }

    function index()
    {
        $data = array();
        $data['settings'] = $this->leaguesettings_model->get_league_settings_data($this->session->userdata('league_id'));
        $data['invite_url'] = $this->common_model->get_league_invite_url();
        $this->admin_view('admin/leaguesettings/leaguesettings',$data);
    }

    function ajax_toggle_item()
    {
        $return = array();
        $item = $this->input->post('var1');

        $return['value'] = $this->leaguesettings_model->toggle_setting($this->session->userdata('league_id'),$item);
        $return['success'] = true;

        echo json_encode($return);
    }
    function ajax_change_item()
    {
        $return = array();
        $return['success'] = false;
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $return['value'] = $value;
        $result = $this->leaguesettings_model->change_setting($this->session->userdata('league_id'),$type,$value);
        if ($result > 0)
            $return['success'] = true;
        $return['rows'] = $result;
        echo json_encode($return);
    }
    function set_wo_setting()
    {
        $response = array('success' => false);
        $value = $this->input->post('value');
        $this->leaguesettings_model->set_wo_setting($value);
        $response['success'] = true;

        $this->load->model('security_model');
        $this->security_model->set_session_variables();

        echo json_encode($response);

    }

    function clear_keepers()
    {
        $response = array('success' => false);
        $this->leaguesettings_model->clear_keepers();
        $response['success'] = true;

        echo json_encode($response);
    }

}
?>
