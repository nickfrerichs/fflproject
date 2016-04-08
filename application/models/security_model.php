<?php

class Security_model extends MY_Model
{

    function set_session_variables()
    {
        $owner = $this->db->select('owner.id as owner_id, owner.active_league, owner.first_name, owner.last_name')
                ->select('team.id as team_id, team_name, owner.active_league')
                ->from('owner')
                ->join('team','team.owner_id = owner.id and team.league_id = owner.active_league')
                ->where('owner.user_accounts_id',$this->userid)->get()->row();

        $this->session->set_userdata('owner_id', $owner->owner_id);
        $this->session->set_userdata('league_id', $owner->active_league);
        $this->session->set_userdata('team_id', $owner->team_id);
        $this->session->set_userdata('team_name', $owner->team_name);
        $this->session->set_userdata('first_name', $owner->first_name);
        $this->session->set_userdata('last_name', $owner->last_name);

        if ($this->db->from('league_admin')->where('league_id',$owner->active_league)->where('league_admin_id',$this->userid)->get()->num_rows() > 0)
            $this->session->set_userdata('is_league_admin', True);
        else
            $this->session->set_userdata('is_league_admin', False);

        $this->load->model('league/chat_model');
        $names = $this->chat_model->get_firstnames;
        if ($names[$owner->first_name] > 1)
            $chatname = $owner->first_name+' '+$owner->last_name[0];
        else
            $chatname = $owner->first_name;
        $this->session->set_userdata('chat_name',$chatname);

        $week_type = $this->db->select('nfl_season')->from('league_settings')->where('league_id',$this->session->userdata('league_id'))->get()->row()->nfl_season;
        $this->session->set_userdata('week_type', $week_type);

        $this->load_leagues($this->session->userdata('owner_id'));

        $this->set_dynamic_session_variables();

        // can this go away?
        $this->load->model('myteam/myteam_settings_model');
    }

    // The idea is that these will be checked every 5 mins and stored as session variables.
    function set_dynamic_session_variables()
    {
        $week_year = $this->get_current_week();
        $this->session->set_userdata('current_year', $week_year->year);
        $this->session->set_userdata('current_week', $week_year->week);
        $this->session->set_userdata('expire_dynamic_vars',time()+20); // Make sure to check dynamic vars every 5 mins.
        $this->session->set_userdata('live_scores',$this->live_scores_on());
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

    function get_current_week()
    {
        // Get the most recently past game start time.
        // If it's start time is more than 12 hours ago
        // Then get the next game is the current week.
        $current_time = time();
        $most_recent = $this->db->select('eid, week, year, UNIX_TIMESTAMP(start_time) as start')->from('nfl_schedule')
            ->where('start_time <',t_mysql($current_time))->order_by('start_time','desc')
            ->limit(1)->get()->row();
        $next_game = $this->db->select('eid, week, year, UNIX_TIMESTAMP(start_time) as start')->from('nfl_schedule')
            ->where('start_time >',t_mysql($current_time))->order_by('start_time','asc')
            ->limit(1)->get()->row();

        if (count($next_game) == 0)
            return $most_recent;

        // It's mid week, works for Thursday through Sunday
        if ($most_recent->week == $next_game->week)
            return $next_game;
        else  // It's after Monday night, need to adjust to allow MNF to end.
        {
            // If the most recent game is 12 hours in the past, roll to the next week.
            if ($most_recent->start + (60*60*12) < $current_time)
                return $next_game;
            else
                return $most_recent;
        }


    }

    function t_mysql($unixtimestamp)
    {
        return date("Y-m-d H:i:s", $unixtimestamp);
    }

    function load_leagues($userid)
    {
        $rows = $this->db->select('league.id, league.league_name')->from('team')
            ->join('league','league.id = team.league_id')->where('team.owner_id',$userid)->get()->result();

        $leagues = array();
        foreach($rows as $row)
            $leagues[$row->id] = $row->league_name;

        $this->session->set_userdata('leagues', $leagues);

    }

}
