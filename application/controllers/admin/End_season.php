<?php

class End_season extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/end_season_model');
        $this->bc["League Admin"] = "";
        $this->bc["End Season"] = "";
    }

    function index()
    {
    	$data = array();
    	$data['real_year'] = $this->end_season_model->get_real_year();
    	$data['season_appears_finished'] = $this->end_season_model->is_finished();
        $this->admin_view('admin/end_season/end_season',$data);
    }

    function start_next_season()
    {
        $current_season = $this->session->userdata('current_year');
        $next_season = $this->end_season_model->get_real_year();
        if ($next_season > $current_season)
        {
            // // Enable off Season
            $this->end_season_model->enable_offseason();

            // Set season_year in league table to the new year
            $this->end_season_model->set_season_year($next_season);

            // Clear rosters except for keepers, use current_season to get keepers assigned during that season
            $this->end_season_model->clear_rosters($current_season);

            // Make a copy of any current keepers to the next season
            $this->end_season_model->copy_keepers($current_season,$next_season);

            $this->load->model('security_model');
            $this->security_model->set_dynamic_session_variables();

            // Reset draft stuff
            $this->end_season_model->clear_draft_order($next_season);

            redirect('admin/end_season');
        }
    }
    function reset_current_season()
    {
        $this->end_season_model->enable_offseason();
        $this->end_season_model->clear_rosters($this->current_year);
        $this->end_season_model->clear_draft_order($this->current_year);
        $this->end_season_model->clear_player_transactions($this->current_year);
        $this->end_season_model->clear_schedule($this->current_year);
        $this->end_season_model->clear_scores($this->current_year);
        redirect('admin/end_season');
    }
}
?>
