<?php

class Standings extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/standings_model');
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
            if ($this->input->post('notation_text') && $this->input->post('notation_symbol'))
            {
                $text = $this->input->post('notation_text');
                $symbol = $this->input->post('notation_symbol');
                $this->standings_model->add_notation_def($text,$symbol);
                redirect('admin/standings/notations');
            }
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
            $this->admin_view('admin/standings/notations',$data);
        }
    }

    function set_team_notation()
    {
        $teamid = $this->input->post('teamid');
        $notationid = $this->input->post('notationid');
        $this->standings_model->set_team_notation($teamid, $notationid);
    }


}

?>
