<?php

class Schedule_model extends MY_Model{

    function get_schedule_data($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $data = $this->db->select('home.team_name as home_team, away.team_name as away_team, title_def_id')
                ->select('schedule.week, schedule.year, schedule.game')
                ->select('schedule.home_team_id, schedule.away_team_id, schedule.game_type_id')
                ->select('schedule_game_type.text_id as game_type')
                ->select('schedule.id as schedule_id')
                ->from('schedule')
                ->join('team as home', 'home.id = schedule.home_team_id', 'left')
                ->join('team as away', 'away.id = schedule.away_team_id', 'left')
                ->join('schedule_game_type', 'schedule_game_type.id = schedule.game_type_id', 'left')
                ->where('schedule.league_id', $this->leagueid)
                ->where('schedule.year', $year)
                ->order_by('schedule.week', 'asc')
                ->order_by('schedule.game', 'asc')
                ->get();

        return $data->result();
    }

    function get_schedule_array($year)
    {
        $schedule = $this->get_schedule_data($year);
        $schedule_array = array();
        foreach ($schedule as $s)
        {
            $schedule_array[$s->week][$s->game] = array('type' => $s->game_type,
                                                        'home' => $s->home_team,
                                                        'away' => $s->away_team,
                                                        'away_id' => $s->away_team_id,
                                                        'home_id' => $s->home_team_id,
                                                        'type_id' => $s->game_type_id,
                                                        'title_id' => $s->title_def_id,
                                                        'id' => $s->schedule_id);
        }
        return $schedule_array;
    }

    function get_game_types_data()
    {
        return $this->db->select('id, text_id, default')->from('schedule_game_type')
                ->where('league_id', $this->leagueid)->get()->result();
    }

    function get_teams_data($year = 0)
    {
        // Could only select teams active that year by first getting array from that year's schedule.'
        if ($year == 0)
            $year = $this->current_year;
        $this->db->select('team.id as team_id, team.team_name')
                ->select('division.id as division_id, division.name as division_name')
                ->from('team')
                ->join('team_division','team_division.team_id = team.id and team_division.year='.$year,'left')
                ->join('division','team_division.division_id = division.id', 'left')
                ->where('team.league_id',$this->leagueid);
        if ($year == $this->current_year)
            $this->db->where('team.active',1);
        $data = $this->db->order_by('division.id','asc')
                ->get();

        return $data->result();
    }

    function get_divisions_data()
    {
        $data = $this->db->select('division.id, division.name')
                ->from('division')
                ->where('division.league_id', $this->leagueid)->get();

        return $data->result();
    }

    function save_template($data)
    {
        if(isset($data['id']))
            $this->db->where('id', $data['id'])->update('schedule_template', $data);
        else
            $this->db->insert('schedule_template',$data);
    }


    function get_templates_data()
    {
        return $this->db->select('*')->from('schedule_template')->get()->result();
    }

    function get_template_data($id)
    {
        return $this->db->select('*')->from('schedule_template')->where('id',$id)->get()->row();
    }

    function get_template_matchups_data($id)
    {
        return $this->db->select('*')->from('schedule_template_matchup')
                ->where('schedule_template_id', $id)
                ->order_by('week','asc')->order_by('game','asc')
                ->get()->result();
    }

    function save_template_matchups($id, $data)
    {
        $this->db->delete('schedule_template_matchup',
                array('schedule_template_id' => $id));
        $this->db->insert_batch('schedule_template_matchup', $data);
    }

    function delete_template($id)
    {
        $this->db->delete('schedule_template_matchup',
                array('schedule_template_id' => $id));
        $this->db->delete('schedule_template', array('id' => $id));
    }

    function delete_game($id)
    {
        $this->db->delete('schedule', array('id' => $id,
                                            'league_id' => $this->leagueid));
    }

    function add_games($week, $count, $year=0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $data = array();
        $game = $this->db->select('(max(game) +1) as next')->from('schedule')
                ->where('week', $week)->where('year',$year)->where('league_id',$this->leagueid)
                ->get()->row()->next;
        if ($game == null)
            $game = 1;
        for($i = 1; $i <= $count; $i++)
        {
            $data[] = array('week' => $week,
                            'game' => $game,
                            'year' => $year,
                            'league_id' => $this->leagueid,
                            'nfl_week_type_id' => $this->common_model->get_week_type_id());
            $game++;
        }
        $this->db->insert_batch('schedule', $data);
    }

    function create_schedule_from_template($template_id, $map)
    {
        $matchups = $this->db->select('week, game, home, away')
                ->from('schedule_template_matchup')
                ->where('schedule_template_id', $template_id)->get()->result();

        $row = $this->db->select('id')->from('schedule_game_type')
                ->where('league_id', $this->leagueid)
                ->order_by('default', 'desc')->get()->row();
        if (count($row) > 0)
            $default_type = $row->id;
        else
            $default_type = 0;
        $data = array();

        foreach($matchups as $m)
        {
            $data[] = array('home_team_id' => $map[$m->home],
                            'away_team_id' => $map[$m->away],
                            'game_type_id' => $default_type,
                            'week' => $m->week,
                            'year' => $this->current_year,
                            'league_id' => $this->leagueid,
                            'game' => $m->game,
                            'nfl_week_type_id' => $this->common_model->get_week_type_id());
        }
        $this->db->delete('schedule', array('league_id' => $this->leagueid, 'year' => $this->current_year));
        $this->db->insert_batch('schedule', $data);

    }


    function save_schedule($schedule, $year=0)
    {
        if ($year == 0)
            $year = $this->current_year;
        foreach($schedule as $week_id => $week)
        {
            foreach($week as $game_id => $game)
            {
                if (array_key_exists('type',$game)){$game_type = $game['type'];}else{$game_type = 0;}
                if (array_key_exists('title',$game)){$game_title = $game['title'];}else{$game_title = 0;}
                $data = array('home_team_id' => $game['home'],
                              'away_team_id' => $game['away'],
                              'game_type_id' => $game_type,
                              'title_def_id' => $game_title);
                $this->db->where('week',$week_id)->where('game', $game_id)
                        ->where('league_id', $this->leagueid)->where('year', $year)
                        ->update('schedule', $data);
            }
        }
    }

    function save_schedule_array($schedule)
    {
        $data = array();
        foreach($schedule as $s)
        {
            $data = array('home_team_id' => $s['home'],
                            'away_team_id' => $s['away'],
                            'game_type_id' => $s['type'],
                            'title_def_id' => $s['title']);
            $this->db->update('schedule',$data,array('id' => $s['id'], 'league_id' => $this->leagueid));

        }
        // Delete any orphaned titles with a schedule_id
        $this->db->query('Delete title FROM title left join schedule on schedule.id = title.schedule_id and '.
            'title.title_def_id = schedule.title_def_id where title.league_id = 3 and title.schedule_id != 0 and schedule.title_def_id is NULL');
    }

    function get_gametypes_data()
    {
        return $this->db->select('id, text_id, default, title_game')->from('schedule_game_type')
                ->where('league_id',$this->leagueid)->get()->result();
    }

    function get_gametype_data($id)
    {
        return $this->db->select('id, text_id, default, for_title')->from('schedule_game_type')
                ->where('league_id',$this->leagueid)->where('id',$id)->get()->row();
    }

    function get_titles_data()
    {
        return $this->db->select('id, text, display_order')
            ->from('title_def')->where('league_id',$this->leagueid)->get()->result();
    }

    function add_gametype($id, $title_game)
    {
        $this->db->insert('schedule_game_type', array('text_id' => $id,
                                                    'league_id' => $this->leagueid,
                                                    'title_game' => $title_game,
                                                    'default' => 0));
    }

    function set_default_gametype($id)
    {
        $this->db->where('id', $id)->where('league_id', $this->leagueid)
                ->update('schedule_game_type', array('default' => 1));
        $this->db->where('id !=', $id)->where('league_id', $this->leagueid)
                ->update('schedule_game_type', array('default' => 0));
    }

    function delete_gametype($id)
    {
        $this->db->delete('schedule_game_type', array('league_id' => $this->leagueid,
                                                  'id' => $id));
    }

    function delete_title_def($id)
    {
        // Delete any titles with this def_id
        $this->db->delete('title', array('league_id' => $this->leagueid,
                                         'title_def_id' => $id));
                                    
        // Change any schedules with this title_def_id to be 0
        $this->db->where('league_id',$this->leagueid)->where('title_def_id',$id);
        $this->db->update('schedule',array('title_def_id' => 0));

        // Delete the title_def
        $this->db->delete('title_def', array('league_id' => $this->leagueid,
                                                    'id' => $id));

    }

    function set_title_text($id, $value)
    {
        $data = array('text' => $value);
        $this->db->where('league_id',$this->leagueid)->where('id',$id);
        $this->db->update('title_def', $data);
    }

    function  set_title_display_order($id, $value)
    {
        $data = array('display_order' => $value);
        $this->db->where('league_id',$this->leagueid)->where('id',$id);
        $this->db->update('title_def',$data);
    }

    function set_gametype_name($id, $name)
    {
        $data = array('text_id' => $name);
        $this->db->where('league_id',$this->leagueid)->where('id',$id);
        $this->db->update('schedule_game_type',$data);
    }

    function add_title($text)
    {
        $data = array(  'text' => $text, 
                        'league_id' => $this->leagueid);

        $this->db->insert('title_def',$data);
    }

    function get_title_games($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;

        // return $this->db->select('schedule.id, team_title.id as title_id')->from('schedule')
        //     ->join('schedule_title','schedule.schedule_title_id = schedule_title.id')
        //     ->join('schedule_result','schedule_result.schedule_id = schedule.id')
        //     ->join('team_title','team_title.schedule_title_id = schedule_title.id','left')
        //     ->where('schedule.year',$year)
        //     ->get()->result();

        return $this->db->select('schedule.id as schedule_id, title.team_id as team_id, title_def.id as title_def_id, title.id as title_id')
            ->select('h.team_name as h_team_name, a.team_name as a_team_name, team_score as h_team_score, opp_score as a_team_score')
            ->select('schedule_result.team_id as h_team_id, schedule_result.opp_id as a_team_id')
            ->select('schedule.week, schedule.year')
            ->select('title_def.text as title_text')
            ->from('schedule')
            ->join('title_def','schedule.title_def_id = title_def.id')
            ->join('title','title.schedule_id = schedule.id','left')
            ->join('schedule_result','schedule_result.schedule_id = schedule.id and schedule_result.team_id > schedule_result.opp_id','left')
            ->join('team as h','h.id = schedule_result.team_id','left')
            ->join('team as a','a.id = schedule_result.opp_id','left')
            ->where('schedule.league_id',$this->leagueid)
            ->where('schedule.year',$year)
            ->get()->result();


        // return $this->db->select('schedule.id as schedule_id, title.team_id as team_id, title_def.id as title_def_id, title.id as title_id')
        //     ->select('h.team_name as h_team_name, a.team_name as a_team_name, team_score as h_team_score, opp_score as a_team_score')
        //     ->select('schedule_result.team_id as h_team_id, schedule_result.opp_id as a_team_id')
        //     ->select('schedule.week, schedule.year')
        //     ->select('title_def.text as title_text')
        //     ->from('title_def')
        //     ->join('title','title.title_def_id = title_def.id and title.year='.$year,'left')
        //     ->join('schedule','schedule.title_def_id = title_def.id and schedule.year = '.$year,'left')
        //     ->join('schedule_result','schedule_result.schedule_id = schedule.id and schedule_result.team_id > schedule_result.opp_id','left')
        //     ->join('team as h','h.id = schedule_result.team_id','left')
        //     ->join('team as a','a.id = schedule_result.opp_id','left')
        //     ->get()->result();

    }

    function get_other_assigned_titles($year)
    {
        return $this->db->select('title.id as title_id, title_def.text, team.team_name')
            ->from('title')
            ->join('title_def','title.title_def_id = title_def.id','left')
            ->join('team','team.id = title.team_id')
            ->where('title.schedule_id',0)
            ->where('title_def.league_id',$this->leagueid)
            ->where('title.year',$year)
            ->get()->result();
    }

    function get_title_defs()
    {
        return $this->db->select('id, text, display_order')
            ->from('title_def')
            ->where('league_id',$this->leagueid)
            ->order_by('display_order','asc')
            ->get()->result();
    }

    function assign_title($team_id, $schedule_id, $title_id, $year)
    {
        $num = $this->db->from('title')->where('schedule_id',$schedule_id)->where('league_id',$this->leagueid)
            ->where('title_def_id',$title_id)->count_all_results();

        $data = array('team_id' => $team_id,
                      'title_def_id' => $title_id,
                      'year' => $year,
                      'schedule_id' => $schedule_id,
                      'league_id' => $this->leagueid);

        if ($num > 0 && $schedule_id != 0)
        {
            $this->db->where('schedule_id',$schedule_id)->where('league_id',$this->leagueid)
                ->update('title',$data);
        }
        else
        {
            $this->db->insert('title',$data);
        }
    }

    // function assign_team_title($team_id, $title_id, $year)
    // {
    //     $num = $this->db->from('team_title')->where('year',$year)->where('league_id',$this->leagueid)
    //         ->where('schedule_title_id',$title_id)->count_all_results();
    //     $data = array('team_id' => $team_id,
    //                   'schedule_title_id' => $title_id,
    //                   'year' => $year,
    //                   'schedule_id' => $schedule_id,
    //                   'league_id' => $this->leagueid);
        
    // }

    function delete_title($title_id,$title_def_id=null,$year=null)
    {

        if ($title_id)
            $this->db->where('id',$title_id)->where('league_id',$this->leagueid);
        elseif($title_def_id && $year)
            $this->db->where('title_def_id',$title_def_id)->where('year',$year)->where('league_id',$this->leagueid);
        $this->db->delete('title');
    }

}
