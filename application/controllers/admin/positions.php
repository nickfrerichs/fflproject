<?php

class Positions extends MY_Admin_Controller{
    
    function __construct() 
    {
        parent::__construct();
        
        $this->load->model('admin/security_model');
        $this->load->model('admin/positions_model');
    }
    
    function index()
    {
        $league_positions = $this->positions_model->get_league_positions_data();
        $nfl_positions = $this->positions_model->get_nfl_positions_array();
        $this->admin_view('admin/positions/positions', array('league_positions' => $league_positions,
                                                    'nfl_positions' => $nfl_positions));
    }
    
    function add()
    {
        $this->load->helper('form');
        $nfl_positions = $this->positions_model->get_nfl_positions_data(true);
        $this->admin_view('admin/positions/positions_add', array('nfl_positions' => $nfl_positions));        
    }
    
    function edit($var)
    {
        if ($this->security_model->is_position_in_league($var))
        {
            $this->load->helper('form');
            $nfl_positions = $this->positions_model->get_nfl_positions_data(true);
            $position = $this->positions_model->get_league_position_data($var);
            $this->admin_view('admin/positions/positions_add', array('nfl_positions'=> $nfl_positions,
                                                            'pos' => $position,
                                                            'edit' => true));
        }
    }
    
    function save($posid = null)
    {
        $edit = false;
        if($this->positions_model->position_exists($this->input->post('text_id')))
            $edit = true;
        
        $values = array('text_id' => $this->input->post('text_id'),
            'long_text' => $this->input->post('long_text'),
            'league_positions' => $this->input->post('league_positions'),
            'max_roster' => $this->input->post('max_roster'),
            'min_roster' => $this->input->post('min_roster'),
            'max_start' => $this->input->post('max_start'),
            'min_start' => $this->input->post('min_start'));
        if($edit)
            $values['id'] = $posid;
        $this->positions_model->save_position($values);
        redirect('admin/positions');
        
    }
    
    function delete($var)
    {
        if ($this->security_model->is_position_in_league($var))
        {
            $this->positions_model->delete_position($var);
            redirect('admin/positions');
        }
    }
}