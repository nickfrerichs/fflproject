<?php

class Rosters extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/rosters_model');
        $this->bc["League Admin"] = "";
        $this->bc["Teams"] = site_url('admin/teams');
    }


    function index()
    {
        echo "Nothing to see here.";
    }

    function view($teamid)
    {
        if ($this->admin_security_model->is_team_in_league($teamid))
        {
            $this->load->model('player_search_model');
            $data = array();
            $data['roster'] = $this->rosters_model->get_team_roster_data($teamid);
            $data['team_name'] = $this->rosters_model->get_team_name($teamid);
            $data['positions'] = $this->player_search_model->get_nfl_positions_data();
            $data['teamid'] = $teamid;
            $data['lineup_years'] = $this->rosters_model->get_lineup_years($teamid);

            $this->bc[$data['team_name']] = "";

            $this->admin_view('admin/rosters/rosters', $data,$this->bc);
        }
    }

    function lineup($teamid)
    {
        if ($this->admin_security_model->is_team_in_league($teamid))
        {
            $data =  array();
            $this->load->model('player_search_model');
            $data['lineup_years'] = $this->rosters_model->get_lineup_years($teamid);
            $data['teamid'] = $teamid;
            $data['team_name'] = $this->rosters_model->get_team_name($teamid);
            $data['positions'] = $this->player_search_model->get_nfl_positions_data();


            $this->bc[$data['team_name']] = site_url('admin/rosters/view'.'/'.$teamid);
            $this->bc['Starting Lineup'] = "";

            $this->admin_view('admin/rosters/lineup', $data, $this->bc);

        }
    }

    function ajax_addplayer()
    {
        $response = array('success' => False);
        $teamid = $this->input->post('teamid');
        $playerid = $this->input->post('playerid');
        if ($this->rosters_model->player_is_available($playerid))
        {
            $this->rosters_model->add_player_to_team($playerid, $teamid);
            $response['success'] = True;
        }
        echo json_encode($response);
        //redirect(site_url().'admin/rosters/view/'.$teamid);
    }

    function removeplayer($teamid, $var)
    {
        $this->rosters_model->remove_player_from_team($var, $teamid);
        redirect(site_url().'admin/rosters/view/'.$teamid);
    }


    function ajax_get_lineup_weeks()
    {
        $response = array("success" => False);

        $teamid = $this->input->post('teamid');
        $year = $this->input->post('year');
        $response["weeks"] = $this->rosters_model->get_lineup_weeks($teamid, $year);

        $response["success"] = True;
        echo json_encode($response);
    }

    function ajax_get_lineup()
    {
        $week = $this->input->post('week');
        $year = $this->input->post('year');
        $teamid = $this->input->post('teamid');

        $starters = $this->rosters_model->get_starters($teamid, $week, $year);

        ?>

        <?php foreach($starters as $p): ?>
            <tr>
                <td><?=$p->first_name?> <?=$p->last_name?> (<?=$p->pos_text?> - <?=$p->club_id?>)</td>
                <td class="text-center"><?=$p->lea_pos?></td>
                <td class="has-text-centered">
                    <button class="button is-small is-link admin-sit-button" data-id="<?=$p->player_id?>">Sit</button>
                </td>
            </tr>

        <?php endforeach; ?>

        <?php
    }


    function ajax_get_bench()
    {
        $week = $this->input->post('week');
        $year = $this->input->post('year');
        $teamid = $this->input->post('teamid');
        $bench = $this->rosters_model->get_bench($teamid, $week, $year);
        $pos_lookup = $this->common_model->get_leapos_lookup_array($year);

        ?>

        <?php foreach ($bench as $p): ?>
            <tr>
                <td><?=$p->first_name?> <?=$p->last_name?> (<?=$p->pos_text?> - <?=$p->club_id?>)</td>
                <td class="has-text-centered">
                    <?php foreach($pos_lookup as $posid => $pl): ?>
                        <?php if(in_array($p->nfl_position_id, explode(",",$pl['list']))): ?>
                            <button class="button admin-start-button is-link is-small" data-id="<?=$p->player_id?>" data-posid="<?=$posid?>"><?=$pl['pos_text']?></button>
                        <?php endif;?>
                    <?php endforeach;?>
                </td>
            </tr>
        <?php endforeach;?>


        <?php

    }

    function ajax_get_league_positions()
    {
        $year = $this->input->post('year');
        $positions = $this->rosters_model->get_league_positions($year);

        ?>

        <?php foreach($positions as $p): ?>

            <button class="button small"><?=$p->text_id?></button>


        <?php endforeach; ?>

        <?php
    }

    function ajax_sit_player()
    {
        $response = array('success' => False);
        $playerid = $this->input->post('playerid');
        $teamid = $this->input->post('teamid');
        $year = $this->input->post('year');
        $week = $this->input->post('week');

        $this->rosters_model->sit_player($playerid, $teamid, $week, $year);
        $response['success'] = True;
        echo json_encode($response);
    }

    function ajax_start_player()
    {
        $response = array('success' => False);
        $playerid = $this->input->post('playerid');
        $teamid = $this->input->post('teamid');
        $year = $this->input->post('year');
        $week = $this->input->post('week');
        $posid = $this->input->post('posid');

        $this->rosters_model->start_player($playerid, $posid, $teamid, $week, $year);
        $response['success'] = True;
        echo json_encode($response);
    }
}
?>
