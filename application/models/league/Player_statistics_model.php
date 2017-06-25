<?php

class Player_statistics_model extends MY_Model{

    function get_statistics_year($player_id, $year = 0, $week_type = 'REG')
    {
        $wtype = $this->db->select('id')->from('nfl_week_type')->where('text_id',$week_type)->get()->row()->id;
        if ($year == 0)
            $year = $this->current_year;
        return $this->db->select('fantasy_statistic.points, fantasy_statistic.player_id, fantasy_statistic.year, fantasy_statistic.week')
                ->select('nfl_statistic.value, nfl_scoring_cat.text_id, nfl_scoring_cat.short_text')
                ->from('fantasy_statistic')
                ->join('nfl_statistic','nfl_statistic.id = fantasy_statistic.nfl_statistic_id')
                ->join('nfl_scoring_cat','fantasy_statistic.nfl_scoring_cat_id = nfl_scoring_cat.id')
                ->where('fantasy_statistic.player_id', $player_id)
                ->where('fantasy_statistic.year', $year)
                ->where('fantasy_statistic.nfl_week_type_id',$wtype)
                ->where('fantasy_statistic.league_id',$this->leagueid)
                ->order_by('week','asc')->get()->result();
    }


    //function get_nfl_players($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',
    //        $show_owned = true, $show_inactive = false, $hide_non_lea = true)
    function get_nfl_players($limit = 1000, $start = 0)
    {
        $pos_list = $this->common_model->league_nfl_position_id_array();
        if (count($pos_list) < 1)
            $pos_list = array(-1);

        return $this->db->select('player.first_name, player.last_name')
            ->select('nfl_position.short_text as position')->select('nfl_team.club_id')
            ->select('team.team_name')
            ->select('sum(fantasy_statistic.points) as points')
            ->from('player')
            ->join('fantasy_statistic','fantasy_statistic.player_id = player.id and fantasy_statistic.year = '.$this->current_year.
                ' and fantasy_statistic.league_id = roster.league_id and roster.league_id='.$this->leagueid,'left')
            ->join('roster','roster.player_id = player.id','left')
            ->join('team','team.id = roster.team_id','left')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('nfl_team','player.nfl_team_id = nfl_team.id')
            ->where('player.active',true)
            ->where_in('nfl_position_id', $pos_list)
            ->group_by('player.id')
            ->order_by('player.last_name','asc')
            ->limit($limit,$start)
            ->get()->result();

    }

    function get_player_data($player_id)
    {
        // also used by waiver_wire_model > log_transaction
        return $this->db->select('first_name, last_name, nfl_position.short_text as pos, profile_url, photo, number, player.id as player_id')
                ->from('player')
                ->select('nfl_team.club_id')
                ->select('actual_pick as draft_pick')
                ->select('IFNULL(draft_order.round,"Undrafted") as round',false)
                ->select('IFNULL(team.team_name,"Free Agent") as team_name',false)
                ->select('team.id as team_id')
                ->join('nfl_position','nfl_position.id = player.nfl_position_id')
                ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
                ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
                ->join('team','team.id = roster.team_id','left')
                ->join('draft_order','draft_order.player_id = player.id and draft_order.league_id='.$this->leagueid.
                       ' and draft_order.year='.$this->current_year,'left')
                ->where('player.id',$player_id)->get()->row();
    }


    function get_player_opponent_weeks_array($player_id,$year=0)
    {
        if ($year == 0)
            $year = $this->session->userdata('current_year');

        $p_team = $this->common_model->player_club_id($player_id);

        $games = $this->db->select('v,h,week,UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year = '.$year.' and gt ="'.$this->session->userdata('week_type').'"'.
            ' and (v = "'.$p_team.'" or h = "'.$p_team.'")')
            ->get()->result();

        $weeks = array();
        foreach($games as $game)
        {

            if ($p_team == $game->v)
                $weeks[$game->week] = '@'.$game->h;
            else
                $weeks[$game->week] = $game->v;
        }

        return $weeks;

    }

    function get_statistics_year_array($player_id, $year = 0, $week_type = 'REG')
    {
        $statistics_year = $this->get_statistics_year($player_id, $year, $week_type);

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
            $stats[$stat->week]['cats'][$stat->text_id] = array('value' => $stat->value, 'points' => $stat->points, 'cat_text' => $stat->short_text);
            if (isset($stats[$stat->week]['total']['value']))
                $stats[$stat->week]['total']['value'] += $stat->points;
            else
                $stats[$stat->week]['total']['value'] = $stat->points;

            if (!array_key_exists($stat->text_id, $category_text))
                $category_text[$stat->text_id] = $stat->short_text;

        }

        $opp_weeks = $this->get_player_opponent_weeks_array($player_id);

        # Fill any empty categories for a week with zeros, so it looks nicer.
        foreach ($stats as $week => $stat)
        {    //print_r($row);
            if (array_key_exists($week,$opp_weeks))
                $stats[$week]['opp'] = $opp_weeks[$week];
            else
                $stats[$week]['opp'] = "Bye";

            foreach ($category_text as $text_id => $text)
            {
                if (!isset($stats[$week]['cats'][$text_id]))
                {
                    $stats[$week]['cats'][$text_id]['value'] = 0;
                    $stats[$week]['cats'][$text_id]['points'] = 0;
                    $stats[$week]['cats'][$text_id]['cat_text'] = $text;
                }
            }
            if (!isset($stats[$week]['total']))
                $stats[$week]['total']['value'] = 0;
        }
        return $stats;
    }

    function get_player_news_from_id($id)
    {
        return $this->db->select('player_news.body, player_news.analysis')
            ->from('player_news')
            ->where('id',$id)->get()->row();
    }


}
