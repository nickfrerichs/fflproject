<?php

class Schedule extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/schedule_model');
        $this->bc["League Admin"] = "";
        $this->bc['Schedule'] = "";
    }


    function index()
    {
        $schedule = $this->schedule_model->get_schedule_data();
        $schedule_array = array();
        foreach ($schedule as $s)
        {
            $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
                                                        'home' => $s->home_team,
                                                        'away' => $s->away_team);
        }
        $this->admin_view('admin/schedule/schedule', array('schedule' => $schedule_array));
    }

    function edit()
    {

        if($this->input->post('add'))
        {
            $this->schedule_model->add_games($this->input->post('week'),
                                             $this->input->post('num'));
            redirect('admin/schedule/edit');
        }

        if($this->input->post('save_schedule'))
        {
            $data = array();
            foreach($this->input->post() as $key => $value)
            {
                if (stripos($key,'away') !== false && $value != '')
                {
                    $away = explode("_",str_replace('away','',$key));
                    $data[$away[0]][$away[1]]['away'] = $value;
                }
                if (stripos($key,'home') !== false && $value != '')
                {
                    $home = explode("_",str_replace('home','',$key));
                    $data[$home[0]][$home[1]]['home'] = $value;
                }
                if (stripos($key,'type') !== false && $value != '')
                {
                    $type = explode("_",str_replace('type','',$key));
                    $data[$type[0]][$type[1]]['type'] = $value;
                }
            }
            $this->schedule_model->save_schedule($data);
            redirect('admin/schedule');
        }

        $schedule = $this->schedule_model->get_schedule_data();
        $game_types = $this->schedule_model->get_game_types_data();
        $team_list = $this->schedule_model->get_teams_data();
        $schedule_array = array();
        foreach ($schedule as $s)
        {
            $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
                                                        'home' => $s->home_team,
                                                        'away' => $s->away_team,
                                                        'away_id' => $s->away_team_id,
                                                        'home_id' => $s->home_team_id,
                                                        'type_id' => $s->game_type_id);
        }

        $this->load->helper('form');

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Edit'] = "";

        $this->admin_view('admin/schedule/schedule_edit', array('schedule' => $schedule_array,
                                                           'game_types' => $game_types,
                                                           'team_list' => $team_list));
    }

    function delete_game($week, $game)
    {
        $this->schedule_model->delete_game($week, $game);
        redirect('admin/schedule/edit');
    }

    function create()
    {

        if ($this->input->post('create_schedule'))
        {
            $id_map = array();
            foreach($this->input->post() as $key => $value)
            {
                if (is_numeric($key))
                    $id_map[$value] = $key;
            }
            $this->schedule_model->create_schedule_from_template($this->input->post('template_id'), $id_map);

            redirect('admin/schedule');
        }

        $teams = $this->schedule_model->get_teams_data();
        $divisions = $this->schedule_model->get_divisions_data();
        $templates = $this->schedule_model->get_templates_data();
        $matchups = array();
        $template = null;
        if ($this->input->post('select_template'))
        {
            $matchups = $this->schedule_model->get_template_matchups_data($this->input->post('template'));
            $template = $this->schedule_model->get_template_data($this->input->post('template'));
        }

        $this->load->helper('form');

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Create'] = "";

        $this->admin_view('admin/schedule/schedule_create', array('teams' => $teams,
                                                                'divisions' => $divisions,
                                                                'templates' => $templates,
                                                                'matchups' => $matchups,
                                                                'template' => $template));
    }

    function template()
    {
        $this->load->helper('form');
        if ($this->input->post('create'))
        {
            $data = array('name' => $this->input->post('name'),
                'teams' => $this->input->post('teams'),
                'divisions' => $this->input->post('divisions'),
                'weeks' => $this->input->post('weeks'),
                'per_week' => $this->input->post('per_week'),
                'description' => $this->input->post('description'));

            $this->schedule_model->save_template($data);
            redirect('admin/schedule/template');
        }

        $templates = $this->schedule_model->get_templates_data();
        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Templates'] = "";
        $this->admin_view('admin/schedule/schedule_template', array('templates' => $templates));

    }

    function edit_template($id)
    {
        if ($this->input->post('save'))
        {
            $data = array();
            foreach($this->input->post() as $key => $value)
            {
                if (stripos($key,'away') !== false && $value != '')
                {
                    $away = explode("_",str_replace('away','',$key));

                    $data[$away[0]]['away'] = $value;
                    $data[$away[0]]['week'] = $away[1];
                    $data[$away[0]]['schedule_template_id'] = $id;
                    $data[$away[0]]['game'] = $away[2];
                }
                if (stripos($key,'home') !== false && $value != '')
                {
                    $home = explode("_",str_replace('home','',$key));
                    $data[$home[0]]['home'] = $value;
                    $data[$home[0]]['week'] = $home[1];
                    $data[$home[0]]['schedule_template_id'] = $id;
                    $data[$home[0]]['game'] = $home[2];
                }
            }
            $this->schedule_model->save_template_matchups($id, $data);
            redirect('admin/schedule/template');
        }

        if ($this->input->post('update'))
        {
            $data = array('id' => $id,
                'name' => $this->input->post('name'),
                'teams' => $this->input->post('teams'),
                'divisions' => $this->input->post('divisions'),
                'weeks' => $this->input->post('weeks'),
                'per_week' => $this->input->post('per_week'),
                'description' => $this->input->post('description'));

            $this->schedule_model->save_template($data);
            redirect('admin/schedule/template');
        }

        $template = $this->schedule_model->get_template_data($id);
        $data = $this->schedule_model->get_template_matchups_data($id);
        $matchups = array();
        foreach ($data as $row)
        {

            $matchups[$row->week][$row->game]['home'] = $row->home;
            $matchups[$row->week][$row->game]['away'] = $row->away;
        }

        $this->load->helper('form');

        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Templates'] = site_url('admin/schedule/template');
        $this->bc[$template->name] = "";

        $this->admin_view('admin/schedule/schedule_edit_template',
                array('template' => $template, 'matchups' => $matchups));
    }

    function delete_template($id)
    {
        $this->schedule_model->delete_template($id);
        redirect('admin/schedule/template');
    }


    function gametypes($action = null, $id = null)
    {
        if ($this->input->post('add'))
        {
            $this->schedule_model->add_gametype($this->input->post('text_id'));
            redirect('admin/schedule/gametypes');
        }

        if ($action == 'default' && is_numeric($id))
        {
            $this->schedule_model->set_default_gametype($id);
            redirect('admin/schedule/gametypes');
        }

        if ($action == 'delete' && is_numeric($id))
        {
            $this->schedule_model->delete_gametype($id);
            redirect('admin/schedule/gametypes');
        }

        $gametypes = $this->schedule_model->get_gametypes_data();

        $this->load->helper('form');
        $this->bc['Schedule'] = site_url('admin/schedule');
        $this->bc['Game Types'] = "";
        $this->admin_view('admin/schedule/schedule_gametypes', array('types' => $gametypes));
    }
}
