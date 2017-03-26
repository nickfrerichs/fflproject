<?php

class Past_seasons extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();

        $this->load->model('admin/admin_security_model');
        $this->bc["League Admin"] = "";
        $this->bc["Past Seasons"] = "";
    }

    function index()
    {
        $data = array();
        $data['years'] = $this->common_model->get_league_years();
        $this->admin_view('admin/past_seasons/past_seasons',$data);

    }

    function year($selected_year)
    {
        $data = array();
        $data['selected_year'] = $selected_year;
        $data['years'] = $this->common_model->get_league_years();
        $this->bc['Past Seasons'] = site_url("admin/past_seasons");
        $this->bc[$selected_year] = "";

        $this->admin_view('admin/past_seasons/show_past_season',$data);
    }

//     function schedule($selected_year)
//     {
//         $data = array();
//         $this->load->model('admin/schedule_model');
//         $schedule = $this->schedule_model->get_schedule_data($selected_year);
//         $schedule_array = array();
//         foreach ($schedule as $s)
//         {
//             $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
//                                                         'home' => $s->home_team,
//                                                         'away' => $s->away_team);
//         }

//         $this->bc['Past Seasons'] = site_url("admin/past_seasons");
//         $this->bc[$selected_year] = site_url("admin/past_seasons/year/".$selected_year);
//         $this->bc['Schedule'] = '';

//         $data['schedule'] = $schedule_array;
//         $data['selected_year'] = $selected_year;
//         $this->admin_view('admin/schedule/schedule', $data);
//     }

// function edit_schedule($year=0)
//     {
//         $this->load->model('admin/schedule_model');
//         if ($year == 0)
//             $year = $this->current_year;

//         $schedule_array = $this->schedule_model->get_schedule_array($year);
//         $game_types = $this->schedule_model->get_game_types_data();
//         $team_list = $this->schedule_model->get_teams_data($year);
//         $titles = $this->schedule_model->get_titles_data();
        
//         $this->bc['Past Seasons'] = site_url("admin/past_seasons");
//         $this->bc[$year] = site_url("admin/past_seasons/year/".$year);
//         $this->bc['Schedule'] = site_url("admin/past_seasons/year/".$year."/schedule");
//         $this->bc['Edit'] = "";

//         $data = array();
//         $data['schedule'] = $schedule_array;
//         $data['game_types'] = $game_types;
//         $data['team_list'] = $team_list;
//         $data['titles'] = $titles;
//         $data['selected_year'] = $year;

//         $this->admin_view('admin/schedule/schedule_edit', $data);
//     }

}
?>