<?php

class Scores_model extends MY_Model{


    function get_live_scores_key()
    {
        return $this->db->select('live_scores_key')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->live_scores_key;
    }

    function get_fantasy_matchups($teamid=0, $week=0, $year=0)
    {
        if ($teamid == 0)
            $teamid = $this->teamid;

        if ($week == 0)
            $week = $this->current_week;

        if ($year == 0)
            $year = $this->current_year;

        $matchups = array();
        // Get starting lineup requirements
        $pos_year = $this->common_model->league_position_year($year);
        $positions = $this->db->select('position.text_id, position.max_start, position.id')->from('position')
            ->where('league_id',$this->leagueid)->where('year',$pos_year)->order_by('display_order','asc')->get()->result();

        // use summary table to speed up the load a little (.02s :)) if it's not the current week.
        $fs = "fantasy_statistic_week";
        if ($year == $this->current_year && $week == $this->current_week)
            $fs = "fantasy_statistic";

        $started_players = $this->db->select('player.id as player_id, player.short_name, player.photo, player.number')
            ->select('starter.team_id, starter.starting_position_id')
            ->select('nfl_team.club_id')
            ->select('nfl_position.short_text as nfl_pos, nfl_position.type as nfl_pos_type')
            ->select('IFNULL(sum(fs.points),"-") as points',false)
            ->from('starter')
            ->join('player','player.id = starter.player_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id','left')
            ->join($fs.' as fs','fs.player_id = player.id and fs.year = '.$year.' and fs.week='.$week.
                    ' and fs.league_id = '.$this->leagueid,'left')
            ->where('starter.year',$year)->where('starter.week',$week)
            ->where('starter.league_id',$this->leagueid)
            ->group_by('player.id')
            ->get()->result();

        $bench_players = $this->db->select('player.id as player_id, player.short_name, player.photo, player.number')
            ->select('bench.team_id')
            ->select('nfl_team.club_id')
            ->select('nfl_position.short_text as nfl_pos, nfl_position.type as nfl_pos_type')
            ->select('IFNULL(sum(fs.points),"-") as points',false)
            ->from('bench')
            ->join('player','player.id = bench.player_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id','left')
            ->join($fs.' as fs','fs.player_id = player.id and fs.year = '.$year.' and fs.week='.$week.
                    ' and fs.league_id = '.$this->leagueid,'left')
            ->where('bench.year',$year)->where('bench.week',$week)
            ->where('bench.league_id',$this->leagueid)
            ->group_by('player.id')
            ->get()->result();

        // 1. Fill all starts spots with empty positions
        if ($year == $this->current_year)
        {
            $teams = $this->db->select('team.id, team.team_name, team.team_abbreviation, team.logo')->from('team')
                ->where('league_id',$this->leagueid, 'team.active',1)->get()->result();
        }
        else
        {
            $team_ids = $this->common_model->team_id_array($year);
            $teams = $this->db->select('team.id, team.team_name, team.team_abbreviation')->from('team')
                ->where('league_id',$this->leagueid)->where_in('team.id',$team_ids)->get()->result();
        }
        $teams_array = array();
        $teams_array[0] = $this->get_empty_team();
        foreach($teams as $t)
        {
            $teams_array[$t->id]['bench'] = array();
            $teams_array[$t->id]['starters'] = array();
            $teams_array[$t->id]['team'] = $t;
            $teams_array[$t->id]['points'] = 0;
            $teams_array[$t->id]['bench_points'] = 0;
            foreach($positions as $p)
            {
                for($i=0;$i<$p->max_start;$i++)
                {
                    $pos = array('pos_text' => $p->text_id,
                                 'pos_id' => $p->id,
                                 'player' => null);
                    $teams_array[$t->id]['starters'][] = $pos;
                }
            }
        }

        // 2. Fill all start spots with players, if they are started.
        foreach($started_players as $p)
        {
            foreach($teams_array[$p->team_id]['starters'] as $key => $spot)
            {
                if($spot['pos_id'] == $p->starting_position_id && !$spot['player'])
                {
                    if($p->nfl_pos_type == 1 || ($p->nfl_pos_type == 3))
                        {$team_class = $p->club_id.'_d';}
                    else {$team_class = $p->club_id.'_o';}

                    $teams_array[$p->team_id]['starters'][$key]['player'] = $p;
                    $teams_array[$p->team_id]['starters'][$key]['teamclass'] = $team_class;
                    break;
                }
            }
            $teams_array[$p->team_id]['points']+=$p->points;
        }

        // 3. Add bench players for bench stats
        foreach($bench_players as $p)
        {
            $teams_array[$p->team_id]['bench'][] = $p;
            $teams_array[$p->team_id]['bench_points'] += $p->points;
        }


        $schedule = $this->db->select('home_team_id as home_id, away_team_id as away_id, game')
            ->select('home.team_name as home_name, away.team_name as away_name')
            ->from('schedule')->join('team as home','home.id = home_team_id')
            ->join('team as away', 'away.id = away_team_id','left')
            ->where('year', $year)->where('week',$week)
            ->where('schedule.league_id',$this->leagueid)
            ->order_by('game','asc')->get()->result();

        $matchups = array();
        foreach($schedule as $s)
        {
            // Set specified team game to the first array spot.
            if ($s->home_id == $teamid || $s->away_id == $teamid)
            {
                $specified_game = array('home_team' => $teams_array[$s->home_id],
                                        'away_team' => $teams_array[$s->away_id]);
                array_unshift($matchups,$specified_game);
                continue;
            }
            if(!isset($teams_array[$s->home_id]))
                $matchups[$s->game]['home_team'] = $this->get_empty_team();
            else
                $matchups[$s->game]['home_team'] = $teams_array[$s->home_id];
            if(!isset($teams_array[$s->away_id]))
                $matchups[$s->game]['away_team'] = $this->get_empty_team();
            else
                $matchups[$s->game]['away_team'] = $teams_array[$s->away_id];
        }
        return $matchups;
    }

    function get_empty_team()
    {
        $team['team'] = (object)array('id' => 0, 'team_name' => 'bye');
        $team['points'] = 0;
        $team['starters'] = array();
        return $team;
    }

    function get_fantasy_scores_array()
    {
        $data['players'] = array();
        $players = $this->db->select('IFNULL(sum(fantasy_statistic.points),"-") as points, starter.player_id as player_id',false)
                ->select('team.id as team_id')
                ->from('starter')
                ->join('fantasy_statistic', 'starter.player_id = fantasy_statistic.player_id '.
                        'AND starter.week = fantasy_statistic.week AND starter.year = fantasy_statistic.year '.
                        'AND starter.league_id = fantasy_statistic.league_id','left')
                ->join('team','team.id = starter.team_id')
                ->where('starter.week', $this->current_week)
                ->where('starter.league_id',$this->leagueid)
                ->where('starter.year', $this->current_year)
                ->group_by('starter.player_id, starter.team_id')
                ->get()->result();

        foreach($players as $p)
        {
            $data['players'][$p->player_id] = $p->points;
            if (!isset($data['teams'][$p->team_id]))
                $data['teams'][$p->team_id] = $p->points;
            else
                $data['teams'][$p->team_id]+=$p->points;
        }

        return $data;

    }

    function get_player_live_array()
    {
        //$ls_key = $this->db->select('live_scores_key')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->live_scores_key;
        $players_array = array();
        $players = $this->db->select('l.player_id, l.gsis_id as game_id, l.play_id, l.text')
            ->from('starter')
            ->join('nfl_live_player as l','l.player_id = starter.player_id','left')
            ->where('starter.league_id',$this->leagueid)
            ->where('starter.week',$this->current_week)
            ->where('starter.year',$this->current_year)
            ->get()->result();

        foreach($players as $p)
        {
            if ($p->player_id == "")
                continue;
            $players_array[$p->player_id] = array('play_id' => $p->play_id,
                                                  'game_id' => $p->game_id,
                                                  'text' => $p->text);
        }

        return $players_array;
    }

    function get_nfl_game_live_array()
    {

        $team_class_array = array();

        // First we fill all NFL team def and off keys with "bye"

        $teams = $this->db->select('club_id')->from('nfl_team')->where('club_id !=','NONE')->get()->result();
        foreach($teams as $t)
        {
            $team_class_array[$t->club_id.'_o']['s'] = "bye";
            $team_class_array[$t->club_id.'_d']['s'] = "bye";
        }


        // Next we get all NFL team def and off keys with the game status.
        $matchup = $this->db->select('gsis, h, v, t, eid, q, hs, vs, unix_timestamp(start_time) as start')
                ->from('nfl_schedule')->where('week',$this->current_week)->where('year',$this->current_year)
                ->where('gt',$this->week_type)->get()->result();

        foreach ($matchup as $m)
        {
            // Final, show score
            if (strtolower($m->q[0]) == "f")
                $match = "Final: ".$m->v.' '.$m->vs.' '.$m->h.' '.$m->hs;
            elseif (strtolower($m->q) == "p" && $m->start < time())
                $match = "Final: ".$m->v.'@'.$m->h.' postgame';
            else // future game, show match up
                $match = $m->v.'@'.$m->h.date(' D g:i',$m->start);
            $team_class_array[$m->h.'_o']['s'] = $match;
            $team_class_array[$m->v.'_o']['s'] = $match;
            $team_class_array[$m->h.'_d']['s'] = $match;
            $team_class_array[$m->v.'_d']['s'] = $match;
            if($m->vs != -1)
                $team_class_array[$m->v.'_o']['pts'] = $m->vs;
            if($m->hs != -1)
                $team_class_array[$m->h.'_o']['pts'] = $m->hs;
        }

        $livegames = $this->db->select('nfl_schedule_gsis, down, to_go, quarter, off.club_id as off_club_id, def.club_id as def_club_id')
            ->select('yard_line, time, h, v, home_score, away_score, note, details, nfl_live_game.id, play_id, nfl_live_game.update_key')
            ->from('nfl_schedule')
            ->join('nfl_live_game', 'nfl_schedule.gsis = nfl_live_game.nfl_schedule_gsis')
            ->join('nfl_team as off', 'off.id = nfl_live_game.off_nfl_team_id')
            ->join('nfl_team as def', 'def.id = nfl_live_game.def_nfl_team_id')
            ->where('nfl_live_game.week', $this->current_week)->where('nfl_live_game.year',$this->current_year)
            ->where('nfl_live_game.nfl_week_type_id','(select id from nfl_week_type where text_id = "'.$this->week_type.'")',false)
            ->get()->result();

        // Do some parsing to come up with a status string, then assign it to all combinations of fantasy player types and if they
        // are on or off the field.
        $suffix = array(1 => 'st', 2=>'nd', 3=>'rd', 4=>'th', 5=>'th', 'Halftime' => '', '0' => '');
        $downs = array(1=>'1st',2=>'2nd',3=>'3rd',4=>'4th');
        $quarters = array(1=>'1st',2=>'2nd',3=>'3rd',4=>'4th','Halftime'=>'Halftime', 5=>'OT');
        foreach($livegames as $game)
        {
            // Yard line string
            if ($game->yard_line == 0){$yard_line = '50 ydln';}
            if ($game->yard_line > 0){$yard_line = $game->def_club_id.' '.(50-$game->yard_line);}
            if ($game->yard_line < 0){$yard_line = $game->off_club_id.' '.(50+$game->yard_line);}

            if ($game->yard_line > 0){$yard_line = 'opp '.(50-$game->yard_line);}
            if ($game->yard_line < 0){$yard_line = 'own '.(50+$game->yard_line);}

            // Quarter string
            if ($game->quarter == 'Halftime')
                $quarter = 'Halftime';
            elseif($game->quarter == '5' || $game->quarter == '6')
                $quarter = "OT";
            else
                $quarter = $game->quarter.'Q';

            // Put it all together and get the status
            $t = explode(':',$game->time);
            $gametime = $quarter.' ('.ltrim($t[0].':'.$t[1],'0').')';
            if ($quarter == 'Halftime')
            {
                $on_status = 'Halftime '.$game->v.' @ '.$game->h;
                $o_off_status = $on_status;
                $d_off_status = $on_status;
            }
            else
            {
                $on_status = $gametime.' '.$game->down.$suffix[$game->down].' & '.$game->to_go.' '.$yard_line;
                $o_off_status = $gametime.' '.$game->def_club_id.' on defense.';
                $d_off_status = $gametime.' '.$game->off_club_id.' on offense.';
            }


            if ($game->note == "KICKOFF")
                $on_status = $gametime.' '.$game->def_club_id.' kickoff';

            if ($game->note == "XP")
                $on_status = $gametime.' '.$game->off_club_id.' XP Good';

            if ($game->note == "INT")
                $on_status = $gametime.' '.$game->off_club_id.' throws an INT!';

            if ($game->note == "PUNT")
                $on_status = $gametime.' '.$game->off_club_id.' punting, '.$yard_line;

            if ($game->note == "TIMEOUT")
                $on_status .= ' timeout.';

            if ($game->note == "FUMBLE")
                $on_status = $game->off_club_id.' FUMBLE!';

            if ($game->note == "TD")
                $on_status = $game->off_club_id.' Touchdown!';

            if ($game->note == "FG")
                $on_status = $game->off_club_id.' FG is good!';

            if ($game->note == "XPB")
                $on_status = $game->off_club_id.' XP is blocked!';

            if ($game->note == "PENALTY")
                $on_status .= ' (penalty)';

            if ($game->details == "*** play under review ***")
                $on_status.=' (under review)';

            // This is used for NFL games on standard view
            $time = ltrim(substr($game->time,0,-3),'00');
            if ($game->down == 0)
                $down = '';
            else
                $down = $game->down.$suffix[$game->down].' & '.$game->to_go;
            $gamedata = array('d' => $down,
                              'q' => $game->quarter,
                              't' => $time.' '.$quarters[$game->quarter],
                              'y' => $yard_line);
            //$ls_key = $this->db->select('live_scores_key')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->live_scores_key;

            // Here's where we use the data collected and parsed and assign the status strings.
            // If fantasy defensive player's team is on defense - on the field
            $team_class_array[$game->def_club_id.'_d']['s'] = $on_status;
            //if ($ls_key == $game->update_key) // Only if the details are from the most recent update
            $team_class_array[$game->def_club_id.'_d']['d'] = $game->details;
            $team_class_array[$game->def_club_id.'_d']['p'] = $game->play_id;
            $team_class_array[$game->def_club_id.'_d']['y'] = $game->yard_line+50;
            $team_class_array[$game->def_club_id.'_d']['a'] = 1;

            // If fantasy offensive player's team is on defense - off the field
            $team_class_array[$game->def_club_id.'_o']['s'] = $o_off_status;
            $team_class_array[$game->def_club_id.'_o']['p'] = $game->play_id;
            $team_class_array[$game->def_club_id.'_o']['y'] = $game->yard_line+50;
            $team_class_array[$game->def_club_id.'_o']['a'] = 0;
            $team_class_array[$game->def_club_id.'_o']['data'] = $gamedata;

            // If fantasy offenseive player's team is on offense - on the field
            $team_class_array[$game->off_club_id.'_o']['s'] = $on_status;
            //if ($ls_key == $game->update_key) // Only if the details are from the most recent update
            $team_class_array[$game->off_club_id.'_o']['d'] = $game->details;
            $team_class_array[$game->off_club_id.'_o']['p'] = $game->play_id;
            $team_class_array[$game->off_club_id.'_o']['y'] = $game->yard_line+50;
            $team_class_array[$game->off_club_id.'_o']['a'] = 1;
            $team_class_array[$game->off_club_id.'_o']['data'] = $gamedata;

            // If fantasy defensive player's team is on offense - off the field
            $team_class_array[$game->off_club_id.'_d']['s'] = $d_off_status;
            $team_class_array[$game->off_club_id.'_d']['p'] = $game->play_id;
            $team_class_array[$game->off_club_id.'_d']['y'] = $game->yard_line+50;
            $team_class_array[$game->off_club_id.'_d']['a'] = 0;
        }

        return $team_class_array;
    }


    function get_nfl_game_scores_array()
    {
        $scores_array = array();
        $teams = $this->db->select('club_id')->from('nfl_team')->get()->result();
        foreach($teams as $t)
            $scores_array[$t->club_id] = "bye";

        $matchup = $this->db->select('gsis, h, v, t, eid, q, hs, vs')->from('nfl_schedule')->where('week',$week)->where('year',$year)
                ->where('gt','(select id from nfl_week_type where text_id = "'.$this->week_type.'")',false)->get()->result();

        foreach ($matchup as $m)
        {
            if (strtolower($m->q[0]) == "f")
                $match = $m->v.' '.$m->vs.' '.$m->h.' '.$m->hs;
            else
                $match = $m->v.'@'.$m->h;
            $scores_array[$m->h] = array('gsis' => $m->gsis,'time' => $m->t,'match' => $match);
            $scores_array[$m->v] = array('gsis' => $m->gsis,'time' => $m->t,'match' => $match);
        }
    }

    function get_weeks($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        return $this->db->select('distinct(week) as week')->from('schedule')->where('league_id',$this->leagueid)
            ->where('year',$year)->order_by('week','asc')->get()->result();
    }

    // Old stats methods
    function get_scores_years()
    {
        return $this->db->select('distinct(year)')->from('schedule')
                ->where('league_id', $this->leagueid)
                ->order_by('year', 'desc')->get()->result();

        return $this->db->select('distinct(year)')->from('fantasy_statistic')
                ->where('league_id', $this->leagueid)
                ->order_by('year', 'desc')->get()->result();

    }

    function get_week_types()
    {
        return $this->db->select('distinct(nfl_week_type.text_id) as week_type')
            ->from('schedule')->join('nfl_week_type','nfl_week_type.id = schedule.nfl_week_type_id')
            ->where('league_id', $this->leagueid)
            ->get()->result();
    }

    function get_scores_data($week, $year, $wtype)
    {

        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$wtype)->get()->row()->id;

        $this->db->select('sum(fantasy_statistic.points) as points')
                ->select('player.short_name, player.id as player_id, player.photo')
                ->select('nfl_position.short_text as pos')
                ->select('team.team_name, team.id as team_id')
                ->select('nfl_team.club_id, nfl_team.id as nfl_team_id')
                ->from('starter')
                ->join('fantasy_statistic', 'starter.player_id = fantasy_statistic.player_id '.
                        'AND starter.week = fantasy_statistic.week AND starter.year = fantasy_statistic.year '.
                        'AND starter.nfl_week_type_id = fantasy_statistic.nfl_week_type_id AND '.
                        'starter.league_id = fantasy_statistic.league_id','left')
                ->join('player', 'player.id = starter.player_id')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->join('team', 'team.id = starter.team_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id', 'left');
        if ($week != 0)
            $this->db->where('starter.week', $week);
        return $this->db->where('starter.league_id',$this->leagueid)
                ->where('starter.year', $year)
                ->group_by('starter.player_id, starter.team_id')
                ->order_by('nfl_position.display_order','asc')
                ->order_by('points','desc')
                ->get()->result();

    }

    function get_players_scores_data($week, $year, $week_type)
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$week_type)->get()->row()->id;
        $this->db->select('sum(fantasy_statistic.points) as points, starter.player_id as player_id')
                ->select('starter.team_id')
                ->from('starter')
                ->join('fantasy_statistic', 'starter.player_id = fantasy_statistic.player_id '.
                        'AND starter.week = fantasy_statistic.week AND starter.year = fantasy_statistic.year AND '.
                        'fantasy_statistic.nfl_week_type_id = starter.nfl_week_type_id AND '.
                        'fantasy_statistic.league_id = starter.league_id','left');
        if ($week != 0)
            $this->db->where('starter.week', $week);
        return $this->db->where('starter.year', $year)
                ->where('starter.league_id',$this->leagueid)
                ->group_by('starter.player_id, starter.team_id')
                ->get()->result();
    }

    function get_week_schedule_data($week, $year, $week_type="REG")
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$week_type)->get()->row()->id;
        $this->db->select('home_team_id as home_id, away_team_id as away_id, game')
                ->select('home.team_name as home_name, away.team_name as away_name')
                ->from('schedule')->where('year', $year)->join('team as home','home.id = home_team_id')
                ->join('team as away', 'away.id = away_team_id','left');
        if ($week != 0)
            $this->db->where('week',$week);
        return $this->db->where('schedule.league_id',$this->leagueid)
                ->where('schedule.nfl_week_type_id',$wtype)
                ->order_by('game','asc')->get()->result();
    }

    function get_nfl_matchups_data($week=0, $year=0, $week_type = 0)
    {
        if ($week == 0){$week = $this->current_week;}
        if ($year == 0){$year = $this->current_year;}
        if ($week_type = 0){$week_type = $this->week_type;}
        return $this->db->select('gsis, h, v, t, eid, q, hs, vs')->from('nfl_schedule')->where('week',$week)->where('year',$year)
                ->where('gt',$week_type)->get()->result();
    }

    function get_live_nfl_matchups_data()
    {
        $data = array();
        $matchups = $this->db->select('gsis, h, v, t, eid, q, hs, vs')->from('nfl_schedule')->where('week',$this->current_week)->where('year',$this->current_year)
                ->where('gt',$this->week_type)->order_by('q','asc')->get()->result();

        $final = array();
        $pre = array();
        foreach($matchups as $m)
        {
            if($m->q == "F")
                $final[] = $m;
            elseif($m->q == "P")
                $pre[] = $m;
            else
                $data[] = $m;
        }
        $data = array_merge($data,$pre,$final);
        return $data;
    }

    function get_live_game_data($week, $year, $week_type)
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$week_type)->get()->row()->id;
        return $this->db->select('nfl_schedule_gsis, down, to_go, quarter, off.club_id as off_club_id, def.club_id as def_club_id')
            ->select('yard_line, time, h, v, home_score, away_score, note, details')
            ->from('nfl_live_game')
            ->join('nfl_team as off', 'off.id = nfl_live_game.off_nfl_team_id')
            ->join('nfl_team as def', 'def.id = nfl_live_game.def_nfl_team_id')
            ->join('nfl_schedule', 'nfl_schedule.gsis = nfl_live_game.nfl_schedule_gsis')
            ->where('nfl_live_game.week', $week)->where('nfl_live_game.year',$year)
            ->where('nfl_live_game.nfl_week_type_id',$wtype)
            ->get()->result();
    }

    function get_nfl_club_ids()
    {
        return $this->db->select('club_id')->from('nfl_team')->where('club_id !=','None')->get()->result();
    }

    function live_scores_on()
    {
        $num = $this->db->from('nfl_live_game')->join('nfl_week_type','nfl_week_type.id = nfl_live_game.nfl_week_type_id')
            ->where('nfl_week_type.text_id',$this->session->userdata('week_type'))
            ->where('year',$this->current_year)->where('week',$this->current_week)->not_like('quarter','final')
            ->count_all_results();

        if ($num > 0)
            return True;
        return False;
    }

    function get_nfl_matchups_array()
    {
        $team_array = array();
        $matchup = $this->db->select('h, v')->from('nfl_schedule')->where('week',$this->current_week)
            ->where('year',$this->current_year)->where('gt',$this->week_type)->get()->result();

        foreach ($matchup as $m)
        {
            $team_array[$m->h] = $m->v;
            $team_array[$m->v] = '@'.$m->h;
        }
        return $team_array;

    }

}
