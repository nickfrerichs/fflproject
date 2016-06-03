<?php

class Scores extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('season/scores_model');
        $this->live = $this->session->userdata('live_scores');
        $this->bc[$this->current_year." Season"] = "";
        $this->bc['Weekly Scores'] = "";
    }


    function index()
    {

        $this->week();
    }

    function week($week = 0)
    {
        if ($week == 0)
            $week = min($this->current_week,$this->common_model->num_weeks_in_schedule());
        $data = array();


        //if ($this->input->post('week'))
        //    $week = $this->input->post('week');

        $data['matchups'] = $this->scores_model->get_fantasy_matchups(null,$week);
        $data['weeks'] = $this->scores_model->get_weeks();
        $data['selected_week'] = $week;
        $data['selected_year'] = $this->session->userdata['current_year'];
        $this->bc['Weekly Scores'] = site_url('season/scores');
        $this->bc['Week '.$week] = "";
        $this->user_view('user/season/scores',$data);


    }

    function live($teamid = 0)
    {
        $data = array();

        //if (!$this->live)
        //    redirect(site_url('season/scores'));

        $data['week'] = $this->current_week;
        $data['year'] = $this->current_year;
        $data['week_type'] = $this->week_type;
        $data['live'] = $this->live;
        $data['nfl_opp'] = $this->scores_model->get_nfl_matchups_array();

        // Get my selected game scores, my team by default

        // Get the rest of the league player score data

        //$data['scores'] = $this->scores_model->get_scores_data($data['week'], $data['year'], $data['week_type']);

        $data['matchups'] = $this->scores_model->get_fantasy_matchups($teamid);
        $data['selected_game'] = $data['matchups'][0];
        unset($data['matchups'][0]);

        $this->user_view('user/season/scores/current_week',$data);


    }

    function live_scores()
    {
        if ($this->session->userdata('live_scores'))
            echo "1";
        else
            echo "0";
        //$this->load->model('security_model');

        //if ($this->security_model->live_scores_on())
        //    echo "1";
        //else
        //    echo "0";
    }

    function stream_live_scores_key()
    {
        header("Content-Type: text/event-stream\n\n");
        header("Cache-Control: no-cache\n\n");
        while(1)
        {
            echo "data: ".$this->scores_model->get_live_scores_key()."\n\n";
            ob_flush(); // Needed to add this after moving to centos, no idea why.
            flush();
            usleep(5000000); //5 seconds
        }
    }

    function live_json()
    {

        $data['scores'] = $this->scores_model->get_fantasy_scores_array();
        $data['players_live'] = $this->scores_model->get_player_live_array();
        $data['nfl_games'] = $this->scores_model->get_nfl_game_live_array();


        //$data['game_scores'] = $this->scores_model->get_nfl_game_scores_array();
        echo json_encode($data);

        if (1 == 0)
        {
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }

    }

    function test()
    {
        print_r($this->scores_model->get_nfl_matchups_data(1, 2015, "REG"));
    }

    function display()
    {
        $week = $this->current_week;
        $year = $this->current_year;
        $week_type = $this->week_type;

        // Years needed to display nav bar to select statistics to display
        $years = $this->scores_model->get_scores_years();
        $week_types = $this->scores_model->get_week_types();
        $this->load->helper('form');

        if($this->input->post('week'))
        {
            $week = $this->input->post('week');
            $year = $this->input->post('year');
        }
        elseif($this->input->post('year'))
        {
            $year = $this->input->post('year');
        }

        if ($week_type == "POST")
            $schedule = $this->scores_model->get_week_schedule_data(1, $year, $this->week_type);
        else
            $schedule = $this->scores_model->get_week_schedule_data($week, $year, $this->week_type);

        if (count($schedule) == 0)
        {
            // If no schedule exist, this is the minimalist view.
            $this->user_view('user/season/scores/no_schedule',
            array('years' => $years,'week' => $week,'year' => $year, 'schedule' => $schedule, 'live' => false));
            //die();
        }
        elseif ($year >= $this->current_year && $week > $this->current_week)
        {
            echo $this->current_year;
            echo $this->session->userdata('current_year');
            // For future weeks, a much simpler display with just the schedule
            $this->user_view('user/season/scores/futureweek',
            array('years' => $years,'week' => $week,'year' => $year, 'schedule' => $schedule, 'live' => false));
        }
        else
        {
            if ($week_type == "POST") # Use total, not just for 1 week.
                $scores = $this->scores_model->get_scores_data(0, $year, "POST");
            else
                $scores = $this->scores_model->get_scores_data($week, $year, $week_type);

            $nfl_matchups = $this->scores_model->get_nfl_matchups_data($week, $year, $week_type);
            $nfl_teams = $this->scores_model->get_nfl_club_ids();

            $team_data = array();
            // Creates array of team_id's containing team data and player scores
            foreach ($scores as $score)
            {
                if (!isset($team_data[$score->team_id]['points']))
                  $team_data[$score->team_id]['points'] = $score->points;
                else
                  $team_data[$score->team_id]['points'] += $score->points;
                $team_data[$score->team_id]['team_name'] = $score->team_name;
                $team_data[$score->team_id]['id'] = $score->team_id;
                $team_data[$score->team_id]['players'][] =
                       array('name' => $score->short_name, 'pos' => $score->pos, 'points' => $score->points,
                             'id' => $score->player_id, 'club_id' => $score->club_id, 'team_id' => $score->nfl_team_id);
            }

            $games_array = array();

            // Creates array of games containing game info with home and away team data arrays
            foreach ($schedule as $s)
            {
                if (($s->home_id == $this->teamid) || ($s->away_id == $this->teamid))
                    $my_game_id = $s->game;

                if(isset($team_data[$s->home_id]))
                    $games_array[$s->game]['home'] = $team_data[$s->home_id];
                else
                {
                    $games_array[$s->game]['home'] = array('points' => null, 'team_name' => $s->home_name,
                                                           'id' => $s->home_id, 'players' => array());
                }

                if(isset($team_data[$s->away_id]))
                    $games_array[$s->game]['away'] = $team_data[$s->away_id];
                else
                {
                    $games_array[$s->game]['away'] = array('points' => null, 'team_name' => $s->away_name,
                                                           'id' => $s->away_id, 'players' => array());
                }
                $games_array[$s->game]['game_id'] = $s->game;
            }

            // If current logged in user has a team, make that the first item in the games_array.
            if (isset($my_game_id))
            {
                $my_game = $games_array[$my_game_id];
                unset($games_array[$my_game_id]);
                array_unshift($games_array, $my_game);
            }

            // Populate $gsis lookup array with byes
            $bye = 'bye';
            if ($week_type == "POST")
                $bye = 'out';

            foreach ($nfl_teams as $nfl_team)
                $gsis[$nfl_team->club_id] = array('gsis' => $bye, 'time' => '', 'match' => $bye);

            // Create lookup array for teamnames => gsis ids for live updates, also contains match text so
            // there is something to display as default for nonlive games.
            if (1==1)
            {
                foreach ($nfl_matchups as $m)
                {
                    if (strtolower($m->q[0]) == "f")
                        $match = $m->v.' '.$m->vs.' '.$m->h.' '.$m->hs;
                    else
                        $match = $m->v.'@'.$m->h;
                    $gsis[$m->h] = array('gsis' => $m->gsis,'time' => $m->t,'match' => $match);
                    $gsis[$m->v] = array('gsis' => $m->gsis,'time' => $m->t,'match' => $match);
                }
            }

            /*
            if ((($this->current_year == $year) && ($this->current_week == $week) && $this->scores_model->live_scores_on()) || $week_type == "POST")
            {
                if ($week_type == "POST")
                    $week = $this->current_week;
                $this->user_view('user/season/scores/live',
                array('years' => $years,'week' => $week,'year' => $year,'games' => $games_array,'gsis' => $gsis,'live' => true, 'week_types' => $week_types, 'week_type' => $week_type));
            }
            */

                $this->user_view('user/season/scores/notlive',
                array('years' => $years,'week' => $week,'year' => $year,'games' => $games_array,'live' => false, 'week_types' => $week_types));

        }

    }

    function get_live_json($year = "", $week = "")
    {
        if ($year == "")
            $year = $this->current_year;
        if ($week == "")
            $week = $this->current_week;
        $week_type = $this->week_type;

        if ($week_type == 'POST')
            $player_scores = $this->scores_model->get_players_scores_data(0, $year, $week_type);
        else
            $player_scores = $this->scores_model->get_players_scores_data($week, $year, $week_type);

        $schedule = $this->scores_model->get_week_schedule_data($week, $year);
        $live_game_data = $this->scores_model->get_live_game_data($week, $year, $week_type);
        $nfl_matchups = $this->scores_model->get_nfl_matchups_data($week, $year, $week_type);

        // nfl game scores included in json to display as status when a game is completed, indexed
        // by club_id to be used as lookup table
        $gamescores = array();
        foreach($nfl_matchups as $m)
        {
            $gamescores[$m->h] = $m->v.' '.$m->vs.' '.$m->h.' '.$m->hs;
            $gamescores[$m->v] = $m->v.' '.$m->vs.' '.$m->h.' '.$m->hs;
        }


        // array of nfl games in progress and info about that game's current status
        $live_games = array();
        foreach ($live_game_data as $game)
        {
            if (strtolower($game->quarter[0]) == 'f')
            {
                $match = $game->v.' '.$game->away_score.' '.$game->h.' '.$game->home_score;
                $live_games[$game->nfl_schedule_gsis] =
                    array('gametime'=>'f','off_club_id'=>$game->off_club_id,
                          'def_club_id'=>$game->def_club_id, 'match' => $match);
                continue;
            }
            if ($game->down == 1){$suffix = 'st';}
            elseif ($game->down == 2){$suffix = 'nd';}
            elseif ($game->down == 3){$suffix = 'rd';}
            elseif ($game->down == 4){$suffix = 'th';}
            else {$suffix = '';}

            if ($game->yard_line == 0){$yard_line = '50 ydln';}
            if ($game->yard_line > 0){$yard_line = $game->def_club_id.' '.(50-$game->yard_line);}
            if ($game->yard_line < 0){$yard_line = $game->off_club_id.' '.(50+$game->yard_line);}

            if ($game->yard_line > 0){$yard_line = 'opp '.(50-$game->yard_line);}
            if ($game->yard_line < 0){$yard_line = 'own '.(50+$game->yard_line);}

            if ($game->quarter == 'Halftime')
                $quarter = 'Halftime';
            else
                $quarter = $game->quarter.'Q';

            $t = explode(':',$game->time);
            $gametime = $quarter.' ('.ltrim($t[0].':'.$t[1],'0').')';
            if ($quarter == 'Halftime')
                $status = 'Halftime '.$game->off_club_id.' v '.$game->def_club_id;
            else
                $status = $gametime.' '.$game->down.$suffix.' & '.$game->to_go.' '.$yard_line;

            $details = $game->details;
            $details = substr($game->details,0,100);
            $live_games[$game->nfl_schedule_gsis] =
            //array('down'=>$game->down.$suffix,'to_go'=>$game->to_go, 'quarter'=>$game->quarter.'Q', 'note' =>$game->note,
            //      'off_club_id'=>$game->off_club_id,'def_club_id'=>$game->def_club_id,'yard_line'=>$yard_line,'time'=>$game_time);
            array('note' =>$game->note, 'status' => $status, 'details' => $details,
                  'off_club_id'=>$game->off_club_id,'def_club_id'=>$game->def_club_id,'gametime'=>$gametime);


        }

        // array of fantasy team scores (calculated), array of player fantasy points
        $teams = array();
        $players = array();

        foreach ($player_scores as $p)
        {
            $players[$p->player_id] = $p->points;
            if (!isset($teams[$p->team_id]))
                $teams[$p->team_id] = $p->points;
            else
                $teams[$p->team_id] += $p->points;
        }


        $data['live_games'] = $live_games;
        $data['game_scores'] = $gamescores;
        $data['teams'] = $teams;
        $data['players'] = $players;
        //print_r($data);

        $this->load->view('user/season/scores/get_live_json', array('data' => json_encode($data)));
    }


}
