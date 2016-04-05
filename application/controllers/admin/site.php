<?php

class Site extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/site_model');
        if (!$this->is_admin)
            die();
    }

    public function manage_leagues()
    {
        $data = array();
        $data['leagues'] = $this->site_model->get_leagues();
        $this->admin_view('admin/site/manage_leagues', $data);
    }

    public function create_league()
    {
        $data = array();
        $this->admin_view('admin/site/create_league',$data);
    }

    public function do_create_league()
    {
        $name = $this->input->post('name');
        $this->site_model->create_league($name);
    }

    public function edit_league($id)
    {
        $data = array();
        $data['info'] = $this->site_model->get_league_info($id);
        $data['settings'] = $this->site_model->get_league_settings($id);
        $this->admin_view('admin/site/edit_league',$data);
    }

    public function ajax_change_item()
    {
        $return = array();
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $id = $this->input->post('var1');
        if($type == "joinpassword")
        {
            $this->site_model->set_joinpassword($id, $value);
            $return['success'] = True;
            $return['value'] = $value;
        }

        echo json_encode($return);
    }

}

?>
