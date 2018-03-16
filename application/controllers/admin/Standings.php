<?php

class Standings extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/standings_model');
        $this->bc["League Admin"] = "";
        $this->bc["Standings"] = "";
    }

    function index()
    {
        $data = array();
        $data['teams'] = $this->standings_model->get_league_team_data();
        $data['notations'] = $this->standings_model->standings_notation_defs();
        $this->admin_view('admin/standings/standings',$data);
    }

    function notations($action="show", $id=0)
    {
        if ($action == "add")
        {
            // if ($this->input->post('notation_text') && $this->input->post('notation_symbol'))
            // {
            //     $text = $this->input->post('notation_text');
            //     $symbol = $this->input->post('notation_symbol');
            //     $this->standings_model->add_notation_def($text,$symbol);
            //     redirect('admin/standings/notations');
            // }
            $this->bc['Standings'] = site_url('admin/standings');
            $this->bc['Notations'] = site_url('admin/standings/notations');
            $this->bc['Add'] = "";
            $this->admin_view('admin/standings/new_notation');
        }
        elseif($action == "delete" && $id!=0)
        {
            $this->standings_model->delete_notation_def($id);
            redirect('admin/standings/notations');
        }
        elseif($action == "edit")
        {
            echo "Editing not implemented yet.";
        }
        else
        {
            $data['defs'] = $this->standings_model->standings_notation_defs();
            $this->bc['Standings'] = site_url('admin/standings');
            $this->bc['Notations'] = "";
            $this->admin_view('admin/standings/notations',$data);
        }
    }

    function set_team_notation()
    {
        $teamid = $this->input->post('teamid');
        $notationid = $this->input->post('notationid');
        $this->standings_model->set_team_notation($teamid, $notationid);
    }

    function ajax_add_notation()
    {
        $result = array('success' => False);
        $text = $this->input->post('text');
        $symbol = $this->input->post('symbol');
        $this->standings_model->add_notation_def($text,$symbol);
        $result['success'] = True;

        echo json_encode($result);
    }

}

?>
