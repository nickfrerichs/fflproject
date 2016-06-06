<?php

class Players extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('league/player_statistics_model');
        $this->load->model('player_search_model');
        $this->bc['League'] = "";
        $this->bc['NFL Players'] = "";
    }

    function index()
    {
        $data = array();
        $data['positions'] = $this->player_search_model->get_nfl_positions_data();
        $data['years'] = $this->player_search_model->get_league_years();
        $this->user_view('user/league/player_statistics.php', $data);
        # By default, show top scores?
    }

    function id($player_id, $year = "")
    {
        if ($year == "")
            $year = $this->current_year;

        $statistics_year = $this->player_statistics_model->get_statistics_year($player_id, $year, $this->week_type);
        $player = $this->player_statistics_model->get_player_data($player_id);

        # Prepare array indexed by week containing array of stat categoriesa and their value,
        # array is empty if no stats for that week.
        if ($this->week_type == "POST")
            $games = 4;
        else
            $games = 17;

        for ($week = 1; $week <= $games; $week++)
            $stats[$week] = array();
        $category_text = array();

        foreach ($statistics_year as $stat)
        {
            $stats[$stat->week][$stat->text_id] = array('value' => $stat->value, 'points' => $stat->points);
            if (isset($stats[$stat->week]['total']['value']))
                $stats[$stat->week]['total']['value'] += $stat->points;
            else
                $stats[$stat->week]['total']['value'] = $stat->points;

            if (!array_key_exists($stat->text_id, $category_text))
                $category_text[$stat->text_id] = $stat->short_text;
            //if (!in_array($stat->text_id, $category_text))
            //    $category_text[] = $stat->text_id;
        }

        $opp_weeks = $this->player_statistics_model->get_player_opponent_weeks_array($player_id);

        # Fill any empty categories for a week with zeros, so it looks nicer.
        foreach ($stats as $week => $stat)
        {    //print_r($row);
            if (array_key_exists($week,$opp_weeks))
                $stats[$week]['opp'] = $opp_weeks[$week];
            else
                $stats[$week]['opp'] = "Bye";


            foreach ($category_text as $text_id => $text)
            {
                if (!isset($stats[$week][$text_id]))
                {
                    $stats[$week][$text_id]['value'] = 0;
                    $stats[$week][$text_id]['points'] = 0;
                }
            }
            if (!isset($stats[$week]['total']))
                $stats[$week]['total']['value'] = 0;
        }

        $this->user_view('user/league/player_statistics/show_player.php', array('stats' => $stats,
                                                            'cats' => $category_text,
                                                            'player' => $player,
                                                            'year' => $year));
    }



}
