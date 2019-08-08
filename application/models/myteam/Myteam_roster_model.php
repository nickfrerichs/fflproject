<?php

class Myteam_roster_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->current_weektype = $this->session->userdata('week_type');
    }

    function get_weeks_left()
    {
        return $this->db->select('week')->from('schedule')->where('league_id',$this->leagueid)
            ->where('nfl_week_type_id = (select id from nfl_week_type where text_id = "'.$this->week_type.'")')
            ->where('year',$this->current_year)->where('week >=',$this->current_week)
            ->where('(home_team_id = '.$this->teamid.' or away_team_id = '.$this->teamid.')',null,false)
            ->order_by('week','asc')->get()->result();
    }

    function get_roster_data()
    {

        $data = $this->db->select('roster.player_id')
                ->select('player.first_name, player.last_name, player.short_name')
                ->select('nfl_position.short_text as nfl_pos_text_id, nfl_position.id as nfl_pos_id')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('position.nfl_position_id_list, position.text_id as starting_text_id')
                ->select('team_keeper.id IS NOT NULL as keeper',false)
                ->from('roster')
                ->join('player', 'player.id = roster.player_id')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('position', 'position.id = roster.starting_position_id','left')
                ->join('team_keeper','team_keeper.team_id = '.$this->teamid.' and team_keeper.year='.$this->current_year.
                        ' and team_keeper.player_id=player.id','left')
                ->where('roster.team_id', $this->teamid)
                ->group_by('player.id')
                ->order_by('nfl_position.display_order', 'asc')
                ->order_by('player.last_name','asc')
                ->order_by('player.first_name','asc')
                ->get();

        return $data->result();



    }

    function get_bench_data($week)
    {
        $query = $this->db->query('select player.id as player_id, player.first_name, player.last_name, player.nfl_position_id, player.short_name, '.
            'nfl_position.short_text as pos_text, IFNULL(nfl_team.club_id,"FA") as club_id, IFNULL(sum(fantasy_statistic.points),0) as points, '.
            'team_keeper.id IS NOT NULL as keeper, '.
            'player_injury.injury, player_injury_type.text_id as injury_text_id, player_injury_type.short_text as injury_short_text, '.
            'player_injury.id IS NOT NULL as injured, player_injury.week as injury_week '.
            'from `roster` join `player` on `roster`.`player_id` = `player`.`id` '.
            'join nfl_position on nfl_position.id = player.nfl_position_id '.
            'left join `player_injury` on `roster`.`player_id` = `player_injury`.`player_id` '.
            'left join `player_injury_type` on `player_injury`.`player_injury_type_id` = `player_injury_type`.`id` '.
            'left join nfl_team on nfl_team.id = player.nfl_team_id '.
            ' left join team_keeper on team_keeper.team_id = '.$this->teamid.' and team_keeper.year='.$this->current_year.
            ' and team_keeper.player_id=player.id'.
            ' left join fantasy_statistic on fantasy_statistic.player_id = roster.player_id and fantasy_statistic.year = '.$this->current_year.
            ' and fantasy_statistic.league_id = roster.league_id where '.
            '`roster`.`player_id` not in (SELECT `player_id` FROM `starter` where `week` = '.$week.
            ' and `year` = '.$this->current_year.' and team_id = '.$this->teamid.') and `roster`.`league_id` = '.$this->leagueid.
            ' and roster.team_id = '.$this->teamid.' '.
            ' group by roster.player_id order by nfl_position.display_order asc, player.last_name asc, player.id asc');

        return $query->result();
    }


    function get_starters_data($teamid = false, $week = 0)
    {
        if ($week == 0)
            $week = $this->current_week;
        if(!$teamid)
            $teamid = $this->teamid;
        return $this->db->select('starter.starting_position_id, player.id as player_id, player.first_name, player.last_name, player.short_name')
            ->select('nfl_position.short_text as pos_text, IFNULL(nfl_team.club_id,"FA") as club_id',false)
            ->select('IFNULL(sum(fantasy_statistic.points),0) as points',false)
            ->select('team_keeper.id IS NOT NULL as keeper',false)
            ->select('player_injury.injury, player_injury_type.text_id as injury_text_id,player_injury_type.short_text as injury_short_text')
            ->select('player_injury.id IS NOT NULL as injured',false)
            ->select('player_injury.week as injury_week')
            ->from('starter')
            ->join('player','player.id = starter.player_id','left')
            ->join('player_injury','player_injury.player_id = player.id','left')
            ->join('player_injury_type','player_injury_type.id = player_injury.player_injury_type_id','left')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('fantasy_statistic','fantasy_statistic.player_id = starter.player_id and fantasy_statistic.year = '.$this->current_year.
                    ' and fantasy_statistic.league_id = starter.league_id','left')
            ->join('team_keeper','team_keeper.team_id = '.$this->teamid.' and team_keeper.year='.$this->current_year.
                    ' and team_keeper.player_id=player.id','left')
            ->where('starter.year',$this->current_year)->where('starter.week', $week)
            ->where('starter.team_id',$teamid)->group_by('starter.player_id')
            ->order_by('starter.id','asc')
            ->get()->result();
    }

    function get_team_schedule()
    {
        return $this->db->select('home.id as home_id, away.id as away_id, schedule.week')
                ->select('home.team_name as home_name, away.team_name as away_name')
                ->select('schedule.win_id, schedule.loss_id, schedule.tie')
                ->select('schedule.home_score, schedule.away_score')
                ->from('schedule')
                ->join('team as home', 'home.id = schedule.home_team_id')
                ->join('team as away', 'away.id = away_team_id')
                ->where('schedule.year', $this->current_year)
                ->where('(`schedule`.`home_team_id` = '.$this->teamid.' or `schedule`.`away_team_id` = '.$this->teamid.')')
                ->get()->result();
    }

    function get_league_positions_data()
    {
        $pos_year = $this->common_model->league_position_year();
        $data = $this->db->select('position.id, position.nfl_position_id_list, position.text_id')
                ->select('position.max_roster, position.min_roster, position.max_start, position.min_start')
                ->from('position')
                ->where('league_id',$this->leagueid)
                ->where('year',$pos_year)
                ->order_by('display_order', 'asc')
                ->get();

        return $data->result();
    }

    function get_starting_lineup_array($teamid = false)
    {
        if(!$teamid)
            $teamid = $this->teamid;
        $lea_pos = $this->get_league_positions_data();
        $starters_data = $this->get_starters_data($teamid);

        $starters = array();
        $count = 0;
        foreach($lea_pos as $l)
        {
            for($i=$l->max_start; $i>0; $i--)
            {
                $starters[$count] = array('pos_id' => $l->id, 'pos_text' => $l->text_id, 'player' => null);
                $count++;
            }
        }

        foreach($starters as $key => $s)
        {
            foreach($starters_data as $pkey => $p)
            {
                if ($p->starting_position_id == $s['pos_id'])
                {
                    $starters[$key]['player'] = $p;
                    unset($starters_data[$pkey]);
                    break;
                }
            }
        }

        return $starters;

    }

    function ok_to_start($lea_pos, $player_id, $week)
    {

        // 1. you must be the owner to do anything.
        if (!$this->is_player_owner($player_id))  //Not the players owner, false
            return false;

        // 2. The players NFL position must be defined as a league position, or be zero if benching.
        $nfl_pos = $this->db->select('player.nfl_position_id')->from('player')
                ->where('player.id',$player_id)->get()->row()->nfl_position_id;
        
        if($lea_pos !=0 && !$this->nfl_pos_in_lea_pos($nfl_pos, $lea_pos)) // Players NFL pos not defined in lea_pos, false
           return false;

        // 3. you cant can't make changes for past weeks
        if ($week < $this->current_week)
            return false;

        // 4. If you made it this far, it's a current or future week, check current week for locked status.
        if ($week == $this->current_week && $this->common_model->is_player_lineup_locked($player_id))
        {
            return False;
        }

        // 5. If the player is being benched, it's OK since we already checked if the game has started
        if($lea_pos == 0)
            return true;

        // 6. Lastly, it apears we're starting a player, make sure there's room on the starting roster.
        $max_starters = $this->db->select('position.max_start')->from('position')
                ->where('id', $lea_pos)->where('league_id',$this->leagueid)
                ->get()->row()->max_start;

        // If max_starters = -1, use roster_max, if it's -1, then there is no limit.

        if($max_starters != -1 && $this->num_starters($lea_pos, $this->teamid, $week) >= $max_starters) // Team already has too many starters in that position, false
            return false;

        return true; // All checks passed, return true
    }

    function start_player($player_id, $lea_pos, $week)
    {
        if ($lea_pos == 0)
            $this->common_model->sit_player($player_id, $this->teamid, $week, $this->current_year, $this->leagueid);
        else
            $this->common_model->start_player($player_id, $lea_pos, $this->teamid, $week, $this->current_year, $this->week_type);
    }



    function is_player_owner($playerid)
    {
        $data = $this->db->select('roster.id')->from('roster')
                ->where('roster.team_id', $this->teamid)
                ->where('roster.league_id', $this->leagueid)
                ->where('roster.player_id',$playerid)
                ->get();
        if ($data->num_rows() > 0)
            return true;
        return false;
    }

    function nfl_pos_in_lea_pos($nfl_pos, $lea_pos)
    {
        $lea_pos_row = $this->db->select('position.nfl_position_id_list')->from('position')
                ->where('position.league_id', $this->leagueid)
                ->where('position.id',$lea_pos)->get()->row();

        if(in_array($nfl_pos, explode(',',$lea_pos_row->nfl_position_id_list)))
                return true;
        return false;
    }

    function num_starters($lea_pos = "all", $teamid = "this", $week=0)
    {
        if ($week == 0)
            $week = $this->current_week;
        if ($teamid == "this"){$teamid = $this->teamid;}
        $count = $this->db->select('id')->from('starter');
        if (($lea_pos != "all") && (is_numeric($lea_pos)))
            $this->db->where('starting_position_id',$lea_pos);
        return $this->db->where('team_id',$this->teamid)->
                where('league_id',$this->leagueid)
                ->where('year',$this->current_year)
                ->where('week',$week)->get()->num_rows();
    }

    function num_starters_old($lea_pos = "all", $teamid = "this")
    {
        if ($teamid == "this"){$teamid = $this->teamid;}
        $count = $this->db->select('id')->from('starter');
        if (($lea_pos != "all") && (is_numeric($lea_pos)))
            $this->db->where('starting_position_id',$lea_pos);
        return $this->db->where('team_id',$this->teamid)->
                where('league_id',$this->leagueid)->get()->num_rows();
    }

    function get_nfl_opponent_array($week = 0)
    {
        if ($week == 0)
            $week = $this->current_week;
        $data = array();

        $teams = $this->db->select('club_id')->from('nfl_team')->where('club_id !=','NONE')->get()->result();
        $data["FA"] = array("opp" => "none","time" => "");
        foreach($teams as $t)
        {
            $data[$t->club_id]['opp'] = "bye";
            $data[$t->club_id]['time'] = "";
        }

        $schedule = $this->db->select('h,v,UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')->where('week',$week)
            ->where('year',$this->current_year)->where('gt',$this->current_weektype)->get()->result();

        foreach($schedule as $s)
        {
            $data[$s->v]['opp'] = '@'.$s->h;
            $data[$s->v]['time'] = $s->start_time;

            $data[$s->h]['opp'] = $s->v;
            $data[$s->h]['time'] = $s->start_time;
        }
        $data['NONE']['opp'] = "-";
        $data['FA']['time'] = 0;

        return $data;
    }

    function get_team_record_data($id=0)
    {
        if ($id == 0)
            $id = $this->teamid;

        return $this->db->select('sum(schedule_result.team_score) as points, sum(schedule_result.opp_score) as opp_points')
        ->select('sum(win=1) as wins, sum(loss=1) as losses, sum(tie=1) as ties')
        ->select('count(schedule_result.id) as total_games')
        ->select('((sum(win=1) + (sum(tie=1)/2))/count(schedule_result.id)) as winpct')
        ->from('schedule_result')
        ->join('schedule','schedule.id = schedule_result.schedule_id')
        ->where('team_id',$id)
        ->where('schedule.year',$this->current_year)
        ->get()->row();

    }

    function get_keepers_num()
    {
        return $this->db->select('keepers_num')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->keepers_num;
    }

    function keeper_add_ok()
    {
        $max = $this->db->select('keepers_num')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->keepers_num;
        $num = $this->db->from('team_keeper')->where('team_id',$this->teamid)->where('year',$this->current_year)->count_all_results();
        if($num < $max)
            return True;
        return False;
    }

    function toggle_keeper($player_id)
    {
        $row = $this->db->select('id')->from('team_keeper')->where('team_id',$this->teamid)->where('year',$this->current_year)
            ->where('player_id',$player_id)->get()->row();
        if (count($row) == 1)
        {
            $this->db->where('id',$row->id)->delete('team_keeper');
            return False;
        }
        elseif($this->keeper_add_ok())
        {
            $data = array('team_id' => $this->teamid,
                          'player_id' => $player_id,
                          'league_id' => $this->leagueid,
                          'year' => $this->current_year);
            $this->db->insert('team_keeper',$data);
            return True;
        }
    }

}
