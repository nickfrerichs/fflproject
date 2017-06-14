<?php

class History extends MY_User_Controller{


    function __construct()
    {
        parent::__construct();
        $this->bc['League'] = "";
        $this->bc['History'] = "";
        $this->load->model('league/history_model');
    }


    public function index($selected_year = 0)
    {
        //redirect('league/history/year/'.$this->session->userdata('current_year'));
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

    public function scores($year=0, $week=0)
    {
        $this->load->model('season/scores_model');
        
        if ($year == 0)
            $year = $this->current_year;
        if ($week == 0)
            $week = 1;
        $data = array();

        $data['matchups'] = $this->scores_model->get_fantasy_matchups(null,$week,$year);
        $data['weeks'] = $this->scores_model->get_weeks($year);
        $data['selected_week'] = $week;
        $data['selected_year'] = $year;
        $data['years'] = $this->common_model->get_league_years();
        $this->bc['History'] = site_url('league/history');
        $this->bc['Weekly Scores'] = "";

        $this->user_view('user/league/history/weekly_scores',$data);
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

    public function results($year=0)
    {
        $this->load->model('season/standings_model');
        $data = array();
        if ($year == 0)
            $year = $this->current_year;
        
        $data = array();

        $data['current_year'] = $year;
        $data['year'] = $year;        

        $data['years'] = $this->common_model->get_league_years();
        $data['selected_year'] = $year;
        $data['title_games'] = $this->history_model->get_title_games_array($year);
        $data['other_titles'] = $this->history_model->get_other_assigned_titles($year);
        $this->bc['History'] = site_url('league/history');
        $this->bc['Schedule & Results'] = "";

        $this->user_view('user/league/history/results', $data);
    }

    function postseason($year=0)
    {
        $this->load->model('content_model');
        $data = array();

        if ($year == 0)
            $year = $this->current_year;
        $data['years'] = $this->common_model->get_league_years();
        $data['content'] = $this->content_model->get_content('playoffs',$year);
        $data['selected_year'] = $year;

        $this->bc['History'] = site_url('league/history');
        $this->bc['Post Season'] = "";
        $this->user_view('user/league/history/postseason',$data);

        
    }

    function team_records($year=0)
    {
        $data = array();
        if ($year == 0)
            $year = $this->current_year;
        $data['years'] = $this->common_model->get_league_years();
        $data['selected_year'] = $year;

        $this->bc['History'] = site_url('league/history');
        $this->bc['Team Records'] = "";
        $this->user_view('user/league/history/team_records',$data);
    }

    function draft($year=0)
    {
        $data = array();
        if ($year == 0)
            $year = $this->current_year;
         $data['years'] = $this->common_model->get_league_years();
        $data['selected_year'] = $year;

        $this->bc['History'] = site_url('league/history');
        $this->bc['Draft'] = "";
        $this->user_view('user/league/history/draft',$data);
    }
}
?>
