<?php

class History extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->bc['League'] = "";
        $this->bc['History'] = "";
    }


    public function index($selected_year = 0)
    {
        redirect('league/history/year/'.$this->session->userdata('current_year'));
        $data = array();
        $data['selected_year'] = $selected_year;
        if ($selected_year == 0)
            $data['selected_year'] = $this->session->userdata('current_year');


        $this->load->model('player_search_model');

        $data['years'] = $this->player_search_model->get_league_years();
        $this->user_view('user/league/history', $data);
    }

    public function year($selected_year = 0)
    {
        if ($selected_year == 0)
            redirect('league/history/year/'.$this->session->userdata('current_year'));
        $data = array();
        $data['selected_year'] = $selected_year;
        if ($selected_year == 0)
            $data['selected_year'] = $this->session->userdata('current_year');


        $this->load->model('player_search_model');

        $data['years'] = $this->player_search_model->get_league_years();
        $this->user_view('user/league/history', $data);
    }

    public function player_records()
    {
        $this->load->model('player_search_model');
        $data = array();
        $data['positions'] = $this->player_search_model->get_nfl_positions_data();
        $data['years'] = $this->player_search_model->get_league_years();

        $this->bc['History'] = site_url('league/history');
        $this->bc['Player Records'] = "";
        $this->user_view('user/league/history/player_records', $data);

    }
}
?>
