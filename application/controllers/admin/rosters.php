<?php

class Rosters extends MY_Admin_Controller{
    
    function __construct() 
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/rosters_model');
    }
    
    
    function index()
    {
        echo "Nothing to see here.";
    }
    
    function view($teamid)
    {
        $roster = $this->rosters_model->get_team_roster_data($teamid);
        $team_name = $this->rosters_model->get_team_name($teamid);
        $this->load->helper('form');
        $this->admin_view('admin/rosters/rosters', array('roster' => $roster, 'teamname' => $team_name, 'teamid'=>$teamid)); 
    }
    
    function addplayer($teamid)
    {
        if ($this->security_model->is_team_in_league($teamid))
        {
            $this->load->model('player_search_model');
            $this->load->helper('form');
            
            if ($this->input->post('select'))
            {
                $selected_pos = $this->input->post('selected_pos');
                $this->session->set_userdata('psearch_pos',$selected_pos);
            }
            else
                $selected_pos = $this->session->userdata('psearch_pos');

           // if($selected_pos == "all")
           //     $selected_pos = false;
            $this->load->library('pagination');


           // $config['base_url'] = site_url().'admin/rosters/'.$teamid.'/addplayer/'.($selected_pos ? $selected_pos : "all");
            $config['base_url'] = site_url().'admin/rosters/addplayer/'.$teamid;
            $config['total_rows'] = $this->player_search_model->nfl_players_count($selected_pos);
            $config['per_page'] = 25; 
            $config['uri_segment'] = 5;

            $this->pagination->initialize($config); 
            $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;

            if (($config['total_rows'] < $page) && ($page != 0))
                redirect(site_url('admin/rosters/addplayer/'.$teamid));
            if ($page % $config['per_page'] != 0)
                redirect(site_url('admin/rosters/addplayer/'.$teamid));

            $players = $this->player_search_model->get_nfl_players($config['per_page'], $page, $selected_pos);
            $links = $this->pagination->create_links();
            $positions = $this->player_search_model->get_nfl_positions_data();

            $this->admin_view('admin/rosters/addplayer',
                    array('players' => $players, 
                        'links' => $links, 
                        'positions' => $positions,
                        'teamid' => $teamid));
        }
    }
    
    function doaddplayer($teamid, $var)
    {

        if ($this->rosters_model->player_is_available($var))
        {
            $this->rosters_model->add_player_to_team($var, $teamid);
        }
        redirect(site_url().'admin/rosters/view/'.$teamid);
    }
    
    function removeplayer($teamid, $var)
    {
        $this->rosters_model->remove_player_from_team($var, $teamid);
        redirect(site_url().'admin/rosters/view/'.$teamid);
    }
        
    
    
    
}