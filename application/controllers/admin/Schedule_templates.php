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
            redirect('admin/schedule_templates');
        }

        $templates = $this->schedule_model->get_templates_data();
        $this->admin_view('admin/schedule/schedule_template', array('templates' => $templates));
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
            redirect('admin/schedule_templates');
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
        $id = str_replace("type","",$this->input->post('type'));
        $value = $this->input->post('value');

        $this->schedule_model->set_gametype_name($id, $value);
        $response['success'] = True;

        echo json_encode($response);


    }


    function titles()
    {
        $data = array();
        $this->bc['Schedule Templates'] = site_url('admin/schedule_templates');
        $this->bc['Titles'] = "";

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
        $response['success'] = True;

        echo json_encode($response);
    }
    

    function ajax_title_order_edit()
    {
        $response = array('success' => false);
        $id = $this->input->post('var1');
        $value = $this->input->post('value');

        $this->schedule_model->set_title_display_order($id, $value);
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
