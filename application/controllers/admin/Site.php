<?php

class Site extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/site_model');
        if (!$this->is_admin)
            die();
        $this->bc["Super Admin"] = "";
    }

    public function manage_leagues()
    {
        $data = array();
        $data['leagues'] = $this->site_model->get_leagues();
        $this->bc["Leagues"] = "";
        $this->admin_view('admin/site/manage_leagues', $data);
    }

    public function create_league()
    {
        $data = array();
        $data['nfl_schedule_status'] = $this->site_model->get_nfl_schedule_status();
        $this->bc["Leagues"] = site_url("admin/site/manage_leagues");
        $this->bc["New"] = "";
        $this->admin_view('admin/site/create_league',$data);
    }

    public function do_create_league()
    {
        $name = $this->input->post('name');
        $this->site_model->create_league($name);
    }

    public function manage_league($id)
    {
        $data = array();
        $data['info'] = $this->site_model->get_league_info($id);
        $data['settings'] = $this->site_model->get_league_settings($id);
        $data['admins'] = $this->site_model->get_league_admins_array($id);
        $this->bc["Leagues"] = site_url("admin/site/manage_leagues");
        $this->bc["Manage"] = "";
        $this->admin_view('admin/site/manage_league',$data);
    }

    public function settings()
    {
        $data = array();

        $data['settings'] = $this->site_model->get_site_settings();
        $data['week_types'] = $this->site_model->get_week_types_array();
        $data['current_year'] = date("Y");
        $this->bc["Settings"] = "";
        $this->admin_view('admin/site/site_settings',$data);
    }

    public function ajax_get_owners()
    {
        $data = array();
        $leagueid = $this->input->post('leagueid');
        $data['admins'] = $this->site_model->get_league_admins_array($leagueid);
        $data['owners'] = $this->site_model->get_league_owners_array($leagueid);
        //print_r($data['owners']);



        ?>
            <?php foreach($data['owners'] as $id => $o): ?>

                <tr>
                    <td class="text-left"><?=$o->first_name.' '.$o->last_name?></td>
                    <td>
                        <?=$this->load->view('components/toggle_switch',
                                                array('id' => 'admin-'.$id,
                                                      'var1' => $id,
                                                      'var2' => $leagueid,
                                                      'url' => site_url('admin/site/ajax_toggle_league_admin'),
                                                      'is_checked' => array_key_exists($id, $data['admins'])),TRUE);
                        ?>

                    </td>
                </tr>
            <?php endforeach; ?>
        <?php
    }

    public function ajax_toggle_league_admin()
    {
        $response = array("success" => false);
        $userid = $this->input->post('var1');
        $leagueid = $this->input->post('var2');
        //$action = $this->input->post('action');

        $response['currentValue'] = $this->site_model->toggle_league_admin($userid, $leagueid);
        if ($userid == $this->session->userdata('user_id'))
        {
            $this->load->model('security_model');
            $this->security_model->set_session_variables();
        }

        echo json_encode($response);

        // if ($action == "remove")
        //     $this->site_model->remove_league_admin($userid, $leagueid);
        // if ($action == "add")
        // {
        //     if ($this->site_model->ok_to_add_admin($userid, $leagueid))
        //         $this->site_model->set_league_admin($userid, $leagueid);
        //
        // }

    }

    public function ajax_change_item()
    {
        $return = array('success' => FALSE);
        //$type = $this->input->post('type');
        $value = $this->input->post('value');
        $id = $this->input->post('id');

        if ($id == '#join-password')
        {
            $league_id = $this->input->post('var1');
            $this->site_model->set_joinpassword($league_id,$value);
            $return['success'] = TRUE;
            $return['value'] = $value;
        }

        if($id == "#sitename")
        {
            $this->site_model->set_sitename($value);
            $return['success'] = True;
            $return['value'] = $value;
        }
        if($id == "#debug-week")
        {
            $this->site_model->set_debug_week($value);
            $return['success'] = True;
            $return['value'] = $value;
        }
        if($id == "#debug-year")
        {
            $this->site_model->set_debug_year($value);
            $return['success'] = True;
            $return['value'] = $value;
        }
        if($id == "#debug-weektype")
        {
            $this->site_model->set_debug_weektype($value);
            $return['success'] = True;
            $return['value'] = $value;
        }
        echo json_encode($return);
    }

    public function ajax_toggle_admin()
    {
        $userid = $this->input->post('userid');
        $leagueid = $this->input->post('leagueid');
        $action = $this->input->post('action');
        if ($action == "remove")
            $this->site_model->remove_league_admin($userid, $leagueid);
        if ($action == "add")
        {
            if ($this->site_model->ok_to_add_admin($userid, $leagueid))
                $this->site_model->set_league_admin($userid, $leagueid);

        }

    }

    public function ajax_toggle_site_setting()
    {
        $response = array("success" => false);
        $col = $this->input->post('var1');

        $response['value'] = $this->site_model->toggle_site_setting($col);

        $this->load->model('security_model');
        $this->security_model->set_session_variables();

        $response["success"] = True;

        echo json_encode($response);
    }

}

?>
