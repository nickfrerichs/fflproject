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
        $data['admins'] = $this->site_model->get_league_admins_array($id);
        $this->admin_view('admin/site/edit_league',$data);
    }

    public function ajax_get_owners()
    {
        $data = array();
        $leagueid = $this->input->post('leagueid');
        $data['admins'] = $this->site_model->get_league_admins_array($leagueid);
        $data['owners'] = $this->site_model->get_league_owners_array($leagueid);
        // print out the tbody
        ?>
            <?php foreach($data['owners'] as $id => $o): ?>

                <tr>
                    <td class="text-left"><?=$o->first_name.' '.$o->last_name?></td>
                    <td>

                        <?php if(!array_key_exists($id, $data['admins'])): ?>
                            <?php $class="btn btn-default admin-button"; $action="add";?>
                        <?php else: ?>
                            <?php $class="btn btn-default admin-button active"; $action="remove";?>
                        <?php endif;?>
                        <button class="<?=$class?>" data-id="<?=$o->user_id?>" data-leagueid="<?=$leagueid?>" data-action="<?=$action?>">
                        Admin</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php
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

}

?>
