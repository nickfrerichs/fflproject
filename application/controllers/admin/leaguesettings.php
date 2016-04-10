<?php

class Leaguesettings extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/leaguesettings_model');
    }

    function index()
    {
        $data = array();
        $data['settings'] = $this->leaguesettings_model->get_league_settings_data($this->session->userdata('league_id'));
        $this->admin_view('admin/leaguesettings/leaguesettings',$data);
    }

    function ajax_toggle_item()
    {
        $return = array();
        $item = $this->input->post('item');

        $this->leaguesettings_model->toggle_setting($this->session->userdata('league_id'),$item);
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

}
?>
