<?php

class Schedule extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/schedule_model');
        $this->bc["League Admin"] = "";
        $this->bc['Schedule'] = "";
    }


    function index()
    {
        $this->year();
        // $data = array();
        // $schedule = $this->schedule_model->get_schedule_data();
        // $schedule_array = array();
        // $data['years'] = $this->common_model->get_league_years();
        // $data['selected_year'] = $this->current_year;
        // foreach ($schedule as $s)
        // {
        //     $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
        //                                                 'home' => $s->home_team,
        //                                                 'away' => $s->away_team);
        // }
        // $data['schedule'] = $schedule_array;
        // $this->admin_view('admin/schedule/schedule', $data);
    }

    function year($selected_year = 0)
    {
        if ($selected_year == 0)
            $selected_year = $this->current_year;
        $data = array();
        $data['selected_year'] = $selected_year;
        $schedule = $this->schedule_model->get_schedule_data($selected_year);
        $schedule_array = array();
        $data['years'] = $this->common_model->get_league_years();
        foreach ($schedule as $s)
        {
            $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
                                                        'home' => $s->home_team,
                                                        'away' => $s->away_team);
        }
        $this->bc[$selected_year] = "";
        if ($selected_year != $this->current_year)
            $this->bc["Schedule"] = site_url('admin/schedule');
            
        $data['schedule'] = $schedule_array;
        $this->admin_view('admin/schedule/schedule', $data);
    }

    function edit($year=0)
    {
        if ($year == 0)
            $year = $this->current_year;

        $schedule_array = $this->schedule_model->get_schedule_array($year);
        $game_types = $this->schedule_model->get_game_types_data();
        $team_list = $this->schedule_model->get_teams_data($year);
        $titles = $this->schedule_model->get_titles_data();

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc[$year] = site_url('admin/schedule/year/'.$year);
        $this->bc['Edit'] = "";

        $data = array();
        $data['schedule'] = $schedule_array;
        $data['game_types'] = $game_types;
        $data['team_list'] = $team_list;
        $data['titles'] = $titles;
        $data['selected_year'] = $year;

        $this->admin_view('admin/schedule/schedule_edit', $data);
    }

    function titles($year=0)
    {
        $data = array();
        if ($year == 0)
            $year = $this->current_year;

        $data['titles'] = $this->schedule_model->get_title_games($year);
        $data['other_titles'] = $this->schedule_model->get_other_assigned_titles($year);
        $data['title_defs'] = $this->schedule_model->get_title_defs();
        $data['teams'] = $this->schedule_model->get_teams_data($year);
        $data['selected_year'] = $year;

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc[$year] = site_url('admin/schedule/year/'.$year);
        $this->bc['Titles'] = "";

        $this->admin_view('admin/schedule/assign_titles', $data);
        
        
    }

    function delete_game($week, $game)
    {
        $this->schedule_model->delete_game($week, $game);
        redirect('admin/schedule/edit');
    }

    function create()
    {


        $teams = $this->schedule_model->get_teams_data();
        $divisions = $this->schedule_model->get_divisions_data();
        $templates = $this->schedule_model->get_templates_data();
        $matchups = array();
        $template = null;


        $this->load->helper('form');

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Create'] = "";

        $this->admin_view('admin/schedule/schedule_create', array('teams' => $teams,
                                                                'divisions' => $divisions,
                                                                'templates' => $templates,
                                                                'matchups' => $matchups,
                                                                'template' => $template));
    }

    function ajax_create_schedule_from_template()
    {
        $result = array('success' => false);
        $id_map = array();
        $template_id = $this->input->post('template_id');
        foreach($this->input->post('team_array') as $team)
        {
            $id_map[$team['num']] = $team['team_id'];
        }
        $this->schedule_model->create_schedule_from_template($template_id, $id_map);

        $result['success'] =  true;

        echo json_encode($result);
    }

    function ajax_add_games()
    {
        $result = array('success' => False);
        $num = $this->input->post('num');
        $week = $this->input->post('week');
        if ($this->input->post('year'))
            $year = $this->input->post('year');
        else
            $year = $this->current_year;
        
         $this->schedule_model->add_games($week,$num,$year);

         $result['success'] = True;

         echo json_encode($result);
    }

    function ajax_delete_game()
    {
        $result = array('success' => False);
    
        $id = $this->input->post('id');
        $this->schedule_model->delete_game($id);

        $result['success'] = True;
        echo json_encode($result);

    }

    function ajax_create_load_teams()
    {
        $result = array('success' => False);
        $template_id = $this->input->post('template_id');

        $viewdata['matchups'] = $this->schedule_model->get_template_matchups_data($template_id);
        $viewdata['template'] = $this->schedule_model->get_template_data($template_id);
        $viewdata['teams'] = $this->schedule_model->get_teams_data();
        $viewdata['divisions'] = $this->schedule_model->get_divisions_data();


        $result['html'] = $this->load->view('admin/schedule/ajax_create_load_teams',$viewdata,True);

        $result['success'] = True;

        echo json_encode($result);
        

    }

    function ajax_save_schedule()
    {
        $result = array('success' => False);
        $schedule = $this->input->post('schedule');
        
        $this->schedule_model->save_schedule_array($schedule);
        $result['success'] = True;
        echo json_encode($result);
       

    }

    function ajax_assign_title()
    {
        $result = array('success' => False);

        $title_def_id = $this->input->post('title_def_id');
        $schedule_id = $this->input->post('schedule_id');
        $team_id = $this->input->post('team_id');
        $year = $this->input->post('year');
        if ($team_id == 0)
        {
            $this->schedule_model->delete_title(null,$title_def_id,$year);
            echo json_encode($result);
            return;
        }

        if ($schedule_id == "")
            $schedule_id = 0;

//        if ($schedule_id)
//        {
            $this->schedule_model->assign_title($team_id, $schedule_id, $title_def_id, $year);
//        }
        // else
        // {
        //     $this->schedule_model->assign_team_title($team_id,$title_id,$year);
        // }
        $result['success'] = True;

        echo json_encode($result);
    }

    function ajax_delete_title()
    {
        $result = array('success' => False);
        $title_id = $this->input->post('title_id');
        $this->schedule_model->delete_title($title_id);

        $result['success'] = True;
        echo json_encode($result);
    }

}
?>
