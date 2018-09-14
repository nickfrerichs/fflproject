<?php
class Common_noauth_model extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    function league_name_from_mask_id($maskid)
    {
        return $this->db->select('league_name')->from('league')->where('mask_id',$maskid)->get()->row()->league_name;
    }

    function get_site_name()
    {
        return $this->db->select('name')->from('site_settings')->get()->row()->name;
    }

    function get_league_name($leagueid)
    {
        return $this->db->select('league_name')->from('league')->where('id',$leagueid)
            ->get()->row()->league_name;
    }

    function join_code_required($maskid)
    {
        $row = $this->db->select('join_password')->from('league')
            ->join('league_settings','league_settings.league_id = league.id')
            ->where('league.mask_id',$maskid)->get()->row();

        if (count($row) > 0 && $row->join_password != "")
            return true;
        return false;
    }

    function valid_mask($maskid)
    {
        if ($this->db->from('league')->where('mask_id',$maskid)->count_all_results() > 0)
            return True;
        return False;
    }

    function league_has_room($maskid)
    {
        $row = $this->db->select('league_settings.max_teams, league_settings.league_id')->from('league_settings')
            ->join('league','league.id = league_settings.league_id')->where('mask_id',$maskid)->get()->row();
        $active_teams = $this->db->from('team')->where('league_id',$row->league_id)->where('active',1)->count_all_results();
        // If max teams is zero and active teams is zero, must be a new league, need to let someone join.

        if ($row->max_teams == 0 && $active_teams == 0)
        {
            return True;
        }
        if ($active_teams < $row->max_teams)
            return TRUE;
        return FALSE;

    }

    function set_session_variables($expire=False)
    {
        if ($this->session->userdata('expire_basic_vars') < time() || $expire)
        {
            // Need some variables from consolidated config file.
            $this->session->set_userdata('site_name',$this->get_site_name());
            $this->session->set_userdata('expire_basic_vars', time()+$this->session->userdata('session_refresh_time'));
            $this->config->load('fflproject');
            $this->session->set_userdata('basic_debug',$this->config->item('basic_debug'));
        }
    }

    function get_week_type($leagueid)
    {
        return $this->db->select('nfl_season')->from('league_settings')->where('league_id',$leagueid)->get()->row()->nfl_season;
    }

    function get_current_week_year($leagueid)
    {
        $row = $this->db->select('season_year')->from('league')->where('id',$leagueid)->get()->row();
        if (count($row) > 0)
            $season_year = $row->season_year;
        $week_type = $this->get_week_type($leagueid);
        // Get the most recently past game start time.
        // If it's start time is more than 12 hours ago
        // Then get the next game is the current week.
        $current_time = time();
        $this->db->select('eid, week, year, UNIX_TIMESTAMP(start_time) as start')->from('nfl_schedule')
            ->where('start_time <',t_mysql($current_time))->where('gt',$week_type);
        if (isset($season_year))
            $this->db->where('year',$season_year);
        $most_recent=$this->db->order_by('start_time','desc')->limit(1)->get()->row();

        $this->db->select('eid, week, year, UNIX_TIMESTAMP(start_time) as start')->from('nfl_schedule')
            ->where('start_time >',t_mysql($current_time));
        if(isset($season_year))
            $this->db->where('year',$season_year);
        $next_game = $this->db->order_by('start_time','asc')->limit(1)->get()->row();

        if (count($next_game) == 0)
            return $most_recent;

        // It's mid week, works for Thursday through Sunday
        if (!isset($most_recent) || $most_recent->week == $next_game->week)
            return $next_game;
        else  // It's after Monday night, need to adjust to allow MNF to end.
        {
            // If the most recent game is 12 hours in the past, roll to the next week.
            if ($most_recent->start + (60*60*12) < $current_time)
                return $next_game;
            else
                return $most_recent;
        }
        return False;
    }

    function league_position_year($leagueid, $year = 0)
    {
        if ($year == 0) // If none passed, assume they want to know for the current year
        {
            $week_year = $this->get_current_week_year($leagueid);
            $year = $week_year->year;
        }
        $pos_year = $this->db->select('max(year) as y')->from('position')->where('position.league_id',$leagueid)
                ->where('year <=',$year)->get()->row()->y;
        if($pos_year != "")
            return $pos_year;
        return 0;
    }

    function final_game_start_time($year, $week, $weektype)
    {
        $row = $this->db->select('UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year',$year)->where('week',$week)->where('gt',$weektype)
            ->order_by('start_time','desc')->limit(1)->get()->row();

        if (count($row) == 0)
            return "";
        return $row->start_time;
    }

    function player_game_start_time($playerid, $year, $week, $weektype)
    {
        $club_id = $this->player_club_id($playerid);
        $row = $this->db->select('UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year = '.$year.' and week ='.$week.' and gt ="'.$weektype.'"'.
            ' and (h="'.$club_id.'" or v="'.$club_id.'")')
            ->get()->row();

        if (count($row) == 0)
            return "";
        else
            return $row->start_time;
    }

    function player_club_id($playerid)
    {
        $row = $this->db->select('nfl_team.club_id')->from('player')->join('nfl_team','nfl_team.id = player.nfl_team_id')
            ->where('player.id',$playerid)->get()->row();
        if ($row)
            return $row->club_id;
        return "None";
    }

    function get_leagues_data()
    {
        return $this->db->select('id')->from('league')->get()->result();
    }

    function add_player($player_id, $teamid, $leagueid = 0)
    {
        if ($leagueid == 0)
            $leagueid = $this->get_leagueid_from_teamid($teamid);
        $data['league_id'] = $leagueid;
        $data['team_id'] = $teamid;
        $data['player_id'] = $player_id;
        $data['starting_position_id'] = 0;
        $this->db->insert('roster',$data);

        // Recalculate the bench for current week
        $week = $this->get_current_week($leagueid);
        $year = $this->get_current_year($leagueid);

        $weektype = $this->get_current_weektype($leagueid);

        if ($this->is_current_week($week,$year,$leagueid))
            $this->update_bench_players($teamid,$year,$week,$weektype,$leagueid);

    }
    
    function sit_player($playerid, $teamid, $week, $year, $leagueid)
    {

        $this->db->where('week', $week)->where('year', $year)
                ->where('team_id', $teamid)->where('player_id', $playerid)
                ->where('league_id', $leagueid)
                ->delete('starter');
        
        if ($this->is_current_week($week,$year,$leagueid))
            $this->update_bench_players($teamid,$year,$week,false,$leagueid);
    }

    function start_player($playerid, $posid, $teamid, $week, $year, $weektype, $leagueid=0)
    {
        if ($leagueid == 0)
            $leagueid = $this->get_league_id($teamid);
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$weektype)->get()->row()->id;
        $num = $this->db->from('starter')->where('player_id',$playerid)->where('team_id',$teamid)
                ->where('week',$week)->where('year',$year)->count_all_results();
        if ($num < 1) // Check to make sure player isn't already started, ran into this twice this year
        {
            $data = array('league_id' => $leagueid,
                          'player_id' => $playerid,
                          'starting_position_id' => $posid,
                          'team_id' => $teamid,
                          'week' => $week,
                          'nfl_week_type_id' => $wtype,
                          'year' => $year);

            $this->db->insert('starter', $data);
        }

        if ($this->is_current_week($week,$year,$leagueid))
            $this->update_bench_players($teamid,$year,$week,false,$leagueid);
    }

    // This function is used by Trade/WW/Admin roster
    function drop_player($player_id, $teamid, $year, $week, $weektype)
    {
        if ($player_id == 0)
            return;

        //$gamestart = $this->player_game_start_time($player_id, $year, $week, $weektype);
        $lineup_locked = $this->is_player_lineup_locked($player_id, $teamid, $year, $week, $weektype);
        // Delete player from roster
        $this->db->where('player_id',$player_id)
            ->where('team_id',$teamid)
            ->delete('roster');

        
        // Delete any starter & bench rows for current team with this player
        $tables = array('bench','starter');
        foreach($tables as $t)
        {
            $this->db->where('player_id', $player_id)
                    ->where('team_id', $teamid);
            if(!$lineup_locked)
                $this->db->where('week >=',$week);
            else // this week's game has started, don't drop from this weeks starting lineup
                $this->db->where('week >', $week);
            $this->db->where('year', $year)
                    ->delete($t);
        }
        // Delete any keeper rows for current team with this player for this year
        $this->db->where('player_id',$player_id)->where('team_id',$teamid)->where('year',$year)->delete('team_keeper');

        // if ($this->session->userdata('current_week'))
        //     $current_week = $this->session->userdata('current_week');
        // else
        // {
        //     $leagueid = $this->get_leagueid_from_teamid($teamid);
        //     $week_year = $this->common_noauth_model->get_current_week_year($leagueid);
        //     $current_week = $week_year->week;
        // }

        // Update the bench table
        if ($this->is_current_week($week,$year,$this->get_leagueid_from_teamid($teamid)))
            $this->update_bench_players($teamid,$year,$week,$weektype);
    }


    // Updates the bench table for one team, it will be corrected to include all players who are
    // on the teams roster, but not in the starter table.
    // The intent is for this function to only run for the current week. Future bench doesn't really
    // make sense and past bench shouldn't change.
    public function update_bench_players($teamid,$year=0,$week=0,$weektype=false,$leagueid=0)
    {
        if ($leagueid==0)
            $leagueid = $this->get_leagueid_from_teamid($teamid);
        if ($year == 0)
            $year = $this->get_current_year($leagueid);
        if ($week == 0)
            $week = $this->get_current_week($leagueid);
        if ($weektype == false)
            $weektype = $this->get_current_weektype($leagueid);

        // Retrieve & build the bench array to add first
        $weektype_id = $this->db->select('id')->from('nfl_week_type')->where('text_id',$weektype)->get()->row()->id;

        // Recalculate the bench players for this team
        $this->bench_players_recalc($teamid,$year,$week,$weektype_id,$leagueid);

        // Check for other teams that have no players on the bench, if so, recalculate those
        // This is incase another owner has made no roster changes for the week
        $teams_to_bench = $this->db->select('team.id')->from('team')
            ->join('bench',"bench.week={$week} and bench.year={$year} and bench.team_id = team.id",'left')
            ->join('roster','roster.team_id = team.id','left')
            ->where('team.league_id',$leagueid)->where('bench.id is NULL')->where('roster.id is NOT NULL')
            ->get()->result();

        foreach($teams_to_bench as $t)
            $this->bench_players_recalc($t->id,$year,$week,$weektype_id,$leagueid);
    }

    // Sub function to set the bench players for a single team
    private function bench_players_recalc($teamid,$year,$week,$weektype_id,$leagueid)
    {
        // All players who are on the roster, aren't starters = bench
        $newbench_result = $this->db->select('roster.player_id')->from('roster')
            ->join('starter','starter.week = '.$week.' and starter.year = '.$year.
                    ' and starter.team_id = '.$teamid.' and roster.player_id = starter.player_id','left')
            ->where('roster.team_id',$teamid)->where('starter.player_id is NULL')
            ->get()->result();

        // Get who is currently in the bench table, "old bench"
        $oldbench_result = $this->db->select('bench.player_id')->from('bench')
            ->where('team_id',$teamid)->where('week',$week)->where('year',$year)
            ->get()->result();
        $oldbench_ids = array();
        foreach($oldbench_result as $b)
            $oldbench_ids[] = $b->player_id;

        $bench_ids = array();
        $bench_data = array();

        foreach($newbench_result as $r)
        {
            $bench_ids[] = $r->player_id;
            if (!in_array($r->player_id,$oldbench_ids))
            {
            
                $bench_data[] = array(
                    'league_id'         =>  $leagueid,
                    'team_id'           =>  $teamid,
                    'player_id'         =>  $r->player_id,
                    'week'              =>  $week,
                    'year'              =>  $year,
                    'nfl_week_type_id'  =>  $weektype_id
                );
            }
        }
        
        // Clear the bench, if they aren't in the current desired set of benched players
        if(count($bench_ids)>0)
        {
            $this->db->where('team_id',$teamid)->where('week',$week)
                ->where_not_in('player_id',$bench_ids)->delete('bench');
        }
        elseif(count($bench_ids)==0) // When bench is empty, cant usre "where_not_in"
            $this->db->where('team_id',$teamid)->where('week',$week)->delete('bench');

        if(count($bench_data)>0)
        {
            // Add the benched players from the above array that are missing
            $this->db->insert_batch('bench',$bench_data);
        }
    }

    // THIS FUNCTION SHOULDN'T BE IN NOAUTH, IT REQUIRES AUTH
    function is_player_lineup_locked($player_id, $teamid, $year, $week, $weektype)
    {
        $leagueid = $this->get_leagueid_from_teamid($teamid);
        $lock_first_game = $this->db->select('lock_lineups_first_game')->from('league_settings')->where('league_id',$leagueid)
            ->get()->row()->lock_lineups_first_game;

        if ($lock_first_game && !$this->session->userdata('debug_week'))
        {
            $first_game_time = $this->db->select('UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
                ->where('year',$year)->where('week',$week)->where('gt',$weektype)
                ->order_by('start_time','asc')->get()->row()->start_time;
            if ($first_game_time < time())
                return True;
        }

        // 4. If it's the current week and the player doesn't have a bye, check if the game has started
        //    though if debug_week is set, don't worry about the start time.
        if ($this->player_opponent($player_id,$year,$week,$weektype) != "Bye")
        {
            $start_time = $this->player_game_start_time($player_id,$year,$week,$weektype);
            if ($start_time < time() && !$this->session->userdata('debug_week'))
                return True;
        }
        return False;
    }

    function player_opponent($player_id,$year=0,$week=0,$weektype="REG")
    {

        $p_team = $this->player_club_id($player_id);

        $game = $this->db->select('v,h,UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year = '.$year.' and week = '.$week.' and gt ="'.$weektype.'"'.
            ' and (v = "'.$p_team.'" or h = "'.$p_team.'")')
            ->get()->row();
        if (count($game) == 0)
            return 'Bye';
        if ($p_team == $game->v)
            return '@'.$game->h;
        else
            return $game->v;

    }

    function get_leagueid_from_teamid($teamid)
    {
        return $this->db->select('league_id')->from('team')->where('id',$teamid)->get()->row()->league_id;
    }

    function is_current_week($week,$year,$leagueid)
    {
        $cur_week = $this->get_current_week($leagueid);
        $cur_year = $this->get_current_year($leagueid);
        if($cur_week == $week && $cur_year == $year)
            return True;
        return False;
    }

    // Get the current week from session data, or look it up if not found
    function get_current_week($leagueid=0)
    {
        if ($this->session->userdata('current_week'))
            $current_week = $this->session->userdata('current_week');
        else
        {
            $week_year = $this->get_current_week_year($leagueid);
            $current_week = $week_year->week;
        }
        return $current_week;
    }

    function get_current_year($leagueid=0)
    {
        if ($this->session->userdata('current_year'))
        {
            $current_year = $this->session->userdata('current_year');
        }
        else
        {
            $week_year = $this->get_current_week_year($leagueid);
            $current_year = $week_year->year;
        }
        return $current_year;
    }

    function get_current_weektype($leagueid)
    {
        if ($this->session->userdata('week_type'))
            $current_weektype = $this->session->userdata('week_type');
        else
        {
            $current_weektype = $this->db->select('nfl_season')->from('league_settings')->where('league_id',$this->session->userdata('league_id'))->get()->row()->nfl_season;
        }    
        return $current_weektype;
    }

    function get_league_id($teamid=0)
    {
        $leagueid = false;
        if($this->session->userdata('league_id'))
            $leagueid = $this->session->userdata('league_id');
        elseif($teamid != 0)
        {
            $leagueid = $this->db->select('league_id')->from('team')->where('id',$teamid)->get()->row()->league_id;
        }
        return $leagueid;
    }
}
?>
