<?php

class Schedule_templates extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/schedule_model');
        $this->bc["League Admin"] = "";
        $this->bc['Schedule Templates'] = "";
    }

    function index()
    {


        // $this->load->helper('form');
        // if ($this->input->post('create'))
        // {
        //     $data = array('name' => $this->input->post('name'),
        //         'teams' => $this->input->post('teams'),
        //         'divisions' => $this->input->post('divisions'),
        //         'weeks' => $this->input->post('weeks'),
        //         'per_week' => $this->input->post('per_week'),
        //         'description' => $this->input->post('description'));

        //     $this->schedule_model->save_template($data);
        //     redirect('admin/schedule_templates');
        // }

        $templates = $this->schedule_model->get_templates_data();
        $this->admin_view('admin/schedule/schedule_template', array('templates' => $templates));
    }

    function ajax_create_template()
    {
        $response = array('success' => False);

        $data = array('name' => $this->input->post('name'),
        'teams' => $this->input->post('num_teams'),
        'divisions' => $this->input->post('num_divs'),
        'weeks' => $this->input->post('num_reg_weeks'),
        'per_week' => $this->input->post('num_games_per_week'),
        'description' => $this->input->post('desc'));

        $this->schedule_model->save_template($data);

        $response['success'] = True;

        echo json_encode($response);
    }

    function ajax_edit_template_info()
    {
        $response = array('success' => False);

        $data = array('id' => $this->input->post('template_id'),
            'name' => $this->input->post('name'),
            'teams' => $this->input->post('num_teams'),
            'divisions' => $this->input->post('num_divs'),
            'weeks' => $this->input->post('num_weeks'),
            'per_week' => $this->input->post('per_week'),
            'description' => $this->input->post('desc'));

        // $response['data'] = $data;
        // echo json_encode($response);
        // die();

        $this->schedule_model->save_template($data);

        $response['message'] = "Template info saved.";
        $response['success'] = True;

        echo json_encode($response);
    }

    function ajax_edit_template_data()
    {
        $response = array('success' => False);

//        $postdata = $this->input->post('data');

        $games = $this->input->post('games');
        $template_id = $this->input->post('template_id');
        $response['games'] = $games;
        // $response['success'] = True;
        // echo json_encode($response);
        // die();
        $data = array();
        foreach($games as $game)
        {   
            $key = $game['week'].$game['game'];
            // NICK START HERE WORKING TOWARDS SAVE_TEMPLATE_MATCHUPS in model
            if ($game['homeaway'] == "away")
                $data[$key]['away'] = $game['value'];
            if ($game['homeaway'] == "home")
                $data[$key]['home'] = $game['value'];                
            $data[$key]['week'] = $game['week'];
            $data[$key]['game'] = $game['game'];
            $data[$key]['schedule_template_id'] = $template_id;
        }

        $this->schedule_model->save_template_matchups($template_id, $data);
        $response['success'] = True;
        $response['message'] = "Template matchups saved.";
        echo json_encode($response);
    }

    function edit($id)
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
            redirect('admin/schedule_templates');
        }

        // if ($this->input->post('update'))
        // {
        //     $data = array('id' => $id,
        //         'name' => $this->input->post('name'),
        //         'teams' => $this->input->post('teams'),
        //         'divisions' => $this->input->post('divisions'),
        //         'weeks' => $this->input->post('weeks'),
        //         'per_week' => $this->input->post('per_week'),
        //         'description' => $this->input->post('description'));

        //     $this->schedule_model->save_template($data);
        //     redirect('admin/schedule_templates');
        // }

        $template = $this->schedule_model->get_template_data($id);
        $data = $this->schedule_model->get_template_matchups_data($id);
        $matchups = array();
        foreach ($data as $row)
        {

            $matchups[$row->week][$row->game]['home'] = $row->home;
            $matchups[$row->week][$row->game]['away'] = $row->away;
        }

        $this->load->helper('form');

        $this->bc['Schedule Templates'] = site_url('admin/schedule_templates');
        $this->bc[$template->name] = "";

        $this->admin_view('admin/schedule/schedule_edit_template',
                array('template' => $template, 'matchups' => $matchups));
    }

    function delete($id)
    {
        $this->schedule_model->delete_template($id);
        redirect('admin/schedule_templates');
    }

    function ajax_add_gametype()
    {
        $response = array('success' =>false);

        $text_id = $this->input->post('text_id');
        $title_game = $this->input->post('title_game');

        $this->schedule_model->add_gametype($text_id, $title_game);

        $response['success'] = True;

        echo json_encode($response);

    }

    function gametypes($action = null, $id = null)
    {
        if ($this->input->post('add'))
        {
            $text_id = $this->input->post('text_id');
            if ($this->input->post('title_game'))
                $title_game = true;
            else
                $title_game = false;
            $this->schedule_model->add_gametype($text_id, $title_game);
            redirect('admin/schedule_templates/gametypes');
        }

        if ($action == 'default' && is_numeric($id))
        {
            $this->schedule_model->set_default_gametype($id);
            redirect('admin/schedule_templates/gametypes');
        }

        if ($action == 'delete' && is_numeric($id))
        {
            $this->schedule_model->delete_gametype($id);
            redirect('admin/schedule_templates/gametypes');
        }

        $gametypes = $this->schedule_model->get_gametypes_data();

        $this->load->helper('form');
        $this->bc['Schedule Templates'] = site_url('admin/schedule_templates');
        $this->bc['Game Types'] = "";
        $this->admin_view('admin/schedule/schedule_gametypes', array('types' => $gametypes));

    }

    function ajax_gametype_name_edit()
    {
        $response = array('success' => false);
        $id = $this->input->post('var1');
        $value = $this->input->post('value');

        $this->schedule_model->set_gametype_name($id, $value);
        $response['value'] = $value;
        $response['success'] = True;

        echo json_encode($response);


    }


    function titles()
    {
        $data = array();
        $this->bc['Schedule Templates'] = site_url('admin/schedule_templates');
        $this->bc['Titles Definitions'] = "";

        $data['titles'] = $this->schedule_model->get_titles_data();
        
        $this->admin_view('admin/schedule/schedule_titles',$data);
    }

    function ajax_edit_title()
    {
        $result = array('success' => false);
        if ($this->input->post('id'))
        {
            // Code to edit existing title
        }
        else
        {
            $text = $this->input->post('text');
            // Add a new title, no ID supplied
            $this->schedule_model->add_title($text);
            $result['success'] = True;
        }

        echo json_encode($result);

    }

    function ajax_title_text_edit()
    {

        $response = array('success' => false);
        $id = $this->input->post('var1');
        $value = $this->input->post('value');

        $this->schedule_model->set_title_text($id, $value);
        $response['value'] = $value;
        $response['success'] = True;

        echo json_encode($response);
    }
    

    function ajax_title_order_edit()
    {
        $response = array('success' => false);
        $id = $this->input->post('var1');
        $value = $this->input->post('value');

        $this->schedule_model->set_title_display_order($id, $value);
        $response['value'] = $value;
        $response['success'] = True;

        echo json_encode($response);
    }

    function ajax_delete_title()
    {
        $result = array('success' => false);
        $id = $this->input->post('id');
        $this->schedule_model->delete_title($id);
        $result['success'] = True;

        echo json_encode($result);
    }

}
?>
