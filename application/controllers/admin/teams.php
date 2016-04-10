<?php

class Teams extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/teams_model');
    }

    function index()
    {
        if ($this->security_model->is_league_admin())
        {
            $teams = $this->teams_model->get_league_teams_data(false);
            $league_name = $this->teams_model->get_league_name();
            $this->load->helper('form');
            $this->admin_view('admin/teams/teams', array('leaguename' => $league_name));
        }
        else
        {
            echo "Not League admin";
        }
    }

    function show($var)
    {
        if ($this->security_model->is_team_in_league($var))
        {
            $team = $this->teams_model->get_team_data($var);
            $this->admin_view('admin/teams/show', array('team' => $team));
        }
    }

    function ajax_get_teams()
    {
        if ($this->security_model->is_league_admin())
        {
            $teams = $this->teams_model->get_league_teams_data(false);
            ?>

            <?php foreach ($teams as $team): ?>
            <tr>
                <td><a href="<?=site_url('admin/teams/show/'.$team->id)?>"><?=$team->team_name?></a></td>
                <td><a href="<?=site_url('admin/rosters/view/'.$team->id)?>">Edit Roster</td>
                <td>Division</td>
                <td><?=$team->first_name.' '.$team->last_name; ?></td>
                <td class="text-center">
                    <?php if($team->active): ?>
                        <span style="font-weight:500">Active</span>
                        (<a href="#" class="btn-active" data-id="<?=$team->id?>" data-action="disable">Toggle</a>)
                    <?php else: ?>
                        Inactive
                        (<a href="#" class="btn-active" data-id="<?=$team->id?>" data-action="enable">Toggle</a>)
                    <?php endif;?>
                </td>
            </tr>
            <?php endforeach;?>


            <?php
        }
    }

    function ajax_toggle_active()
    {
        $teamid = $this->input->post('teamid');
        $active = 0;
        if($this->input->post('action') == "enable")
            $active = 1;
        $this->teams_model->set_active_flag($teamid, $active);
    }



}
