<?php

class Rosters extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
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
        if ($this->security_model->is_team_in_league($teamid))
        {
            $this->load->model('player_search_model');
            $data = array();
            $data['roster'] = $this->rosters_model->get_team_roster_data($teamid);
            $data['team_name'] = $this->rosters_model->get_team_name($teamid);
            $data['positions'] = $this->player_search_model->get_nfl_positions_data();
            $data['teamid'] = $teamid;

            $this->bc[$data['team_name']] = "";

            $this->admin_view('admin/rosters/rosters', $data,$this->bc);
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




}
?>
