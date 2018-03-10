<?php

class Teams extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/teams_model');
        $this->load->model('admin/rosters_model');
        $this->bc["League Admin"] = "";
        $this->bc['Teams'] = "";
    }

    function index()
    {
        if ($this->admin_security_model->is_league_admin())
        {
            $data = array();
            $teams = $this->teams_model->get_league_teams_data(false);
            $data['leaguename'] = $this->teams_model->get_league_name();
            $data['invite_url'] = $this->common_model->get_league_invite_url();
            $this->load->helper('form');

            $this->admin_view('admin/teams/teams', $data);
        }
        else
        {
            echo "Not League admin";
        }
    }

    function show($var)
    {
        if ($this->admin_security_model->is_team_in_league($var))
        {
            $team = $this->teams_model->get_team_data($var);

            $this->bc['Teams'] = site_url('admin/teams');
            $this->bc[$team->team_name] = "";
            $this->admin_view('admin/teams/show', array('team' => $team));
        }
    }

    function ajax_get_teams()
    {
        if ($this->admin_security_model->is_league_admin())
        {
            $teams = $this->teams_model->get_league_teams_data(false);
            ?>

            <?php foreach ($teams as $team): ?>
            <tr>
                <td><a href="<?=site_url('admin/rosters/view/'.$team->id)?>"><?=$team->team_name?></a></td>
                <td><?=$team->division_name?></td>
                <td><?=$team->first_name.' '.$team->last_name; ?></td>
                <td class="text-center">
                <div class="field">
                    <input id="active-<?=$team->id?>" type="checkbox" name="switchExample" class="switch toggle-control is-info"
                            data-item="<?=$team->id?>" data-url="<?=site_url('admin/teams/ajax_toggle_active')?>" <?php if($team->active){echo "checked";}?>>
                    <label for="active-<?=$team->id?>"></label>
                </div>
                <!-- <div class="switch small">
                    <input  class="switch-input toggle-control" data-item="<?=$team->id?>" data-url="<?=site_url('admin/teams/ajax_toggle_active')?>"
                        id="active-<?=$team->id?>" type="checkbox" name="exampleSwitch" <?php if($team->active){echo "checked";}?>>
                    <label class="switch-paddle" for="active-<?=$team->id?>">
                    </label>
                </div> -->
                </td>
            </tr>
            <?php endforeach;?>


            <?php
        }
    }

    function ajax_toggle_active()
    {
        $response = array("success" => False);
        $teamid = $this->input->post('item');
        $active = $this->teams_model->toggle_active_flag($teamid);
        $response['success'] = True;
        $response['currentValue'] = $active;
        echo json_encode($response);
    }


}
?>
