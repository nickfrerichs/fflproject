<?php

class Divisions extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/divisions_model');
        $this->load->model('admin/admin_security_model');
        $this->bc["League Admin"] = "";
        $this->bc["Divisions"] = "";
    }


    function index()
    {
        $divisions_array = array();
        $divisions = $this->divisions_model->get_league_divisions();
        $teams = $this->divisions_model->get_teams_data();
        $this->load->helper('form');
        // Create array of divisions that include team members.

        foreach ($teams as $team)
        {
            if($team->division_id == null)
            {
                $divisions_array[0]['name'] = 'none';
                $divisions_array[0]['teams'][] = array('id' => $team->team_id, 'name' => $team->team_name);
            }
            else
            {
                $divisions_array[$team->division_id]['name'] = $team->division_name;
                $divisions_array[$team->division_id]['teams'][] = array('id' => $team->team_id, 'name' => $team->team_name);
            }
        }
        $this->admin_view('admin/divisions/divisions', array('division_array' => $divisions_array, 'divisions' => $divisions));
    }

    // function save()
    // {
    //     if($this->input->post('save'))
    //     {
    //         foreach($this->input->post() as $key => $value)
    //         {
    //             if (is_numeric($value))
    //             {
    //                 $this->divisions_model->save_team($key, $value);
    //             }
    //         }
    //     }
    //     redirect('admin/divisions');
    // }

    function ajax_save_divisions()
    {
        $response = array('success' => false);
        $teams = $this->input->post('teams');

        foreach($teams as $team_id => $div_id)
        {
            if (is_numeric($div_id))
            {
                $this->divisions_model->save_team($team_id, $div_id);
            }
        }

        $response['success'] = True;

        echo json_encode($response);
    }

    function ajax_add_division()
    {
        $response = array('success' => false);
        $div_name = $this->input->post('name');
        $this->divisions_model->add_division($div_name);

        $response['success'] = True;

        echo json_encode($response);
    }

    function ajax_delete_division()
    {
        $response = array('success' => False);
        $id = $this->input->post('id');

        $this->divisions_model->delete_division($id);

        $response['success'] = True;

        echo json_encode($response);
    }

    function manage()
    {
        // if ($this->input->post('add'))
        // {
        //     $div_name = $this->input->post('name');
        //     $this->divisions_model->add_division($div_name);
        //     redirect(site_url('admin/divisions/manage'));
        // }
        $divisions = $this->divisions_model->get_league_divisions();
        $this->load->helper('form');
        $this->bc['Divisions'] = site_url('admin/divisions');
        $this->bc['Manage'] = "";
        $this->admin_view('admin/divisions/manage', array('divisions' => $divisions));
    }

    // function delete($id)
    // {
    //     $this->divisions_model->delete_division($id);
    //     redirect('admin/divisions/manage');
    // }


}
?>
