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
        $this->admin_view('admin/site/manage_leagues', $data);
    }

    public function create_league()
    {
        $data = array();
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

        // print out the tbody
        ?>
            <?php foreach($data['owners'] as $id => $o): ?>

                <tr>
                    <td class="text-left"><?=$o->first_name.' '.$o->last_name?></td>
                    <td>
                        <div class="switch small">
                            <input  class="switch-input toggle-control" data-item="<?=$id?>" data-item2="<?=$leagueid?>" data-url="<?=site_url('admin/site/ajax_toggle_league_admin')?>"
                                id="admin-<?=$id?>" type="checkbox" name="adminSwitch" <?php if(array_key_exists($id, $data['admins'])){echo "checked";}?>>
                            <label class="switch-paddle" for="admin-<?=$id?>">
                            </label>

                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php
    }

    public function ajax_toggle_league_admin()
    {
        $response = array("success" => false);
        $userid = $this->input->post('item');
        $leagueid = $this->input->post('item2');
        $action = $this->input->post('action');

        $response['currentValue'] = $this->site_model->toggle_league_admin($userid, $leagueid);

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
        if($type == "sitename")
        {
            $this->site_model->set_sitename($value);
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
        $field = $this->input->post('item');

        $response['currentValue'] = $this->site_model->toggle_site_setting($field);

        $this->load->model('security_model');
        $this->security_model->set_session_variables();

        echo json_encode($response);
    }

}

?>
