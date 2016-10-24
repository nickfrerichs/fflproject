<?php

class Rosters_model extends MY_Model
{
    function get_team_roster_data($teamid)
    {
        $data = $this->db->select('roster.id, roster.player_id') #roster
                ->select('player.short_name') #player
                ->select('nfl_team.club_id') #nfl_team
                ->select('nfl_position.text_id as position') #nfl_position
                ->from('roster')
                ->join('player', 'player.id = roster.player_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->where('roster.league_id', $this->leagueid)
                ->where('roster.team_id', $teamid)
                ->order_by('nfl_position.display_order','asc')
                ->get();
        return $data->result();

    }

    function get_team_name($teamid)
    {
        $data = $this->db->select('team_name')
                ->from('team')
                ->where('team.id', $teamid)
                ->get();
        return $data->row()->team_name;
    }

    function player_is_available($playerid)
    {
        $data = $this->db->select('roster.id')
                ->from('roster')
                ->where('player_id', $playerid)
                ->where('league_id', $this->leagueid)
                ->get();
        if ($data->num_rows == 0)
          return true;
        return false;
    }

    function add_player_to_team($playerid, $teamid)
    {
        $data = array('league_id' => $this->leagueid,
            'team_id' => $teamid,
            'player_id' => $playerid);
        $this->db->insert('roster',$data);
    }

    function remove_player_from_team($playerid, $teamid)
    {
        $this->load->model('common/common_model');
        $gamestart = $this->common_model->player_game_start_time($playerid);

        // Delete from starter table
        $this->db->where('league_id',$this->leagueid)->where('team_id',$teamid)->where('player_id',$playerid);
        if ($gamestart > time()) // if game is in the future, include this week
            $this->db->where('week >=',$this->current_week);
        else
            $this->db->where('week >',$this->current_week);
        $this->db->delete('starter');

        // Delete any keeper rows for current team with this player for this year
        $this->db->where('player_id',$playerid)->where('team_id',$teamid)->where('year',$this->current_year)->delete('team_keeper');

        $this->db->where('player_id', $playerid)
                ->where('team_id', $teamid)
                ->delete('roster');
    }

    function get_lineup_years($teamid)
    {
        return $this->db->select('distinct(year) as year')->from('schedule')->where('home_team_id',$teamid)->or_where('away_team_id',$teamid)
            ->order_by('year','desc')->get()->result();
    }

    function get_lineup_weeks($teamid, $year)
    {
        $data = array();
        $result = $this->db->select('week')->from('schedule')
            ->where('(home_team_id = '.$teamid.' or away_team_id = '.$teamid.') and year = '.$year,null,false)
            ->order_by('week','desc')->get()->result();

        foreach ($result as $r)
        {
            $data[] = $r->week;
        }

        return $data;
    }

    function get_starters($teamid, $week, $year)
    {
        return $this->db->select('player.id as player_id, player.first_name, player.last_name, player.nfl_position_id, nfl_team.club_id, nfl_position.text_id as pos_text')
            ->select('position.text_id as lea_pos')
            ->from('starter')
            ->join('player','starter.player_id = player.id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id','left')
            ->join('position','starter.starting_position_id = position.id','left')
            ->where('starter.team_id',$teamid)->where('week',$week)->where('starter.year',$year)
            ->order_by('nfl_position.display_order','asc')
            ->get()->result();
    }

    function get_bench($teamid, $week="", $year="")
    {
        return $this->db->select('player.id as player_id, player.first_name, player.last_name, player.nfl_position_id, nfl_team.club_id, nfl_position.text_id as pos_text')
            ->from('roster')
            ->join('player','roster.player_id = player.id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->where('roster.player_id not in (select `player_id` from `starter` where `week` = '.$week.
                    ' and `year` = '.$year.' and `team_id` = '.$teamid.')')
            ->where('roster.team_id',$teamid)
            ->order_by('nfl_position.display_order','asc')
            ->get()->result();
    }

    function get_league_positions($year)
    {
        $pos_year = $this->common_model->league_position_year($year);

        return $this->db->select('text_id, id')->from('position')->where('year',$pos_year)
            ->where('league_id',$this->leagueid)->get()->result();
    }



    function sit_player($playerid, $teamid, $week, $year)
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$this->week_type)->get()->row()->id;

        $this->db->where('week', $week)->where('year', $year)
                ->where('nfl_week_type_id',$wtype)
                ->where('team_id', $teamid)->where('player_id', $playerid)
                ->where('league_id', $this->leagueid)->delete('starter');
        

    }

    function start_player($playerid, $posid, $teamid, $week, $year)
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$this->week_type)->get()->row()->id;
        $num = $this->db->from('starter')->where('player_id',$playerid)->where('team_id',$teamid)
                ->where('week',$week)->where('year',$year)->count_all_results();
        if ($num < 1) // Check to make sure player isn't already started, ran into this twice this year
        {
            $data = array('league_id' => $this->leagueid,
                          'player_id' => $playerid,
                          'starting_position_id' => $posid,
                          'team_id' => $teamid,
                          'week' => $week,
                          'nfl_week_type_id' => $wtype,
                          'year' => $year);

            $this->db->insert('starter', $data);
        }
    }


}
