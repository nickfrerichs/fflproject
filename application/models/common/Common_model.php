<?php
class Common_model extends CI_Model{

    function __construct(){
        parent::__construct();
        if ($this->session->userdata('is_owner'))
        {
            $this->teamid = $this->session->userdata('team_id');
            $this->current_year = $this->session->userdata('current_year');
            $this->current_week = $this->session->userdata('current_week');
            $this->current_weektype = $this->session->userdata('week_type');
            $this->leagueid = $this->session->userdata('league_id');
        }
    }

    function player_game_start_time($playerid)
    {
        return $this->common_noauth_model->player_game_start_time($playerid, $this->current_year, $this->current_week, $this->current_weektype);
    }

    function is_player_lineup_locked($player_id)
    {
        return $this->common_noauth_model->is_player_lineup_locked($player_id, $this->teamid, $this->current_year, $this->current_week, $this->current_weektype);
    }

    function player_opponent($player_id,$week=0,$year=0)
    {
        if ($year == 0)
            $year = $this->session->userdata('current_year');
        if ($week == 0)
            $week = $this->session->userdata('current_week');
        $this->common_noauth_model($player_id,$year,$week,$this->current_weektype);
    }

    function player_club_id($playerid)
    {
        return $this->common_noauth_model->player_club_id($playerid);
    }

    function team_info($team_id)
    {
        return $this->db->select('team.team_name, owner.first_name, owner.last_name, owner.id as owner_id')
            ->select('team.id as team_id, email as owner_email')
            ->from('team')
            ->join('owner','owner.id = team.owner_id')
            ->join('user_accounts','user_accounts.id = owner.user_accounts_id')
            ->where('team.id',$team_id)->get()->row();
    }

    function num_season_weeks()
    {
        # Should probably change this to use nfl_schedule
        if ($this->current_weektype == "REG")
            return $this->db->select('max(week) as week')->from('nfl_schedule')->where('year',$this->current_year)
                ->where('gt',$this->current_weektype)->get()->row()->week;
            #return 17;
        return 0;
    }

    function num_weeks_in_schedule($year=0)
    {
        if($year == 0)
            $year = $this->current_year;
        $row = $this->db->select('max(week) as w')->from('schedule')->where('league_id',$this->leagueid)->where('year',$year)->get()->row();
        if ($row->w == "")
            return 0;
        return $row->w;
    }

    function team_id_array($year)
    {
        $teamids = array();
        $sched = $this->db->select('distinct(home_team_id) as team_id')->from('schedule')->where('league_id',$this->leagueid)->where('year',$year)->get()->result();
        foreach ($sched as $s)
        {
            if(!in_array($s->team_id, $teamids))
                $teamids[] = $s->team_id;
        }
        $sched = $this->db->select('distinct(away_team_id) as team_id')->from('schedule')->where('league_id',$this->leagueid)->where('year',$year)->get()->result();
        foreach ($sched as $s)
        {
            if(!in_array($s->team_id, $teamids))
                $teamids[] = $s->team_id;
        }

        return $teamids;
    }

    function league_nfl_position_id_array($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $y = $this->league_position_year($year);
        $data = $this->db->select('position.nfl_position_id_list')
                ->from('position')
                ->where('position.league_id', $this->leagueid)
                ->where('year',$y)
                ->get();
        $pos_list = array();

        foreach ($data->result() as $posrow)
            $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));
        return $pos_list;
    }

    function nfl_position_lookup_array()
    {
        $data = array();
        $positions = $this->db->select('id,text_id')->from('nfl_position')->get()->result();

        foreach($positions as $p)
        {
            $data[$p->id] = $p->text_id;
        }
        return $data;
    }

    function league_position_year($year = 0)
    {
        $this->load->model('common/common_noauth_model');
        return $this->common_noauth_model->league_position_year($this->leagueid, $year);

        // if ($year == 0) // If none passed, assume they want to know for the current year
        //     $year = $this->current_year;
        // $pos_year = $this->db->select('max(year) as y')->from('position')->where('position.league_id',$this->leagueid)
        //         ->where('year <=',$year)->get()->row()->y;
        // if($pos_year != "")
        //     return $pos_year;
        // return 0;
    }

    function league_position_range($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $start_year = $this->league_position_year($year);
        $end_year = $this->db->select('min(year) as y')->from('position')->where('position.league_id',$this->leagueid)
            ->where('year > ',$start_year)->get()->row()->y;
        $data['db_start'] = $start_year;
        $data['db_end'] = $end_year;

        // If never changed, look up the first year the league existed, if there is no schedule, then it must be a brand
        // new league, so the current year is the start year
        if ($start_year == 0)
        {
            $sched_row = $this->db->select('distinct(year)')->from('schedule')->where('league_id',$this->leagueid)
                ->order_by('year','asc')->limit(1)->get()->row();
            if($sched_row)
                $start_year = $sched_row->year;
        }
        // If never changed, it's through the current year... if it has, the def doesn't include the last changed year, so decrement 1.
        if ($end_year == 0)
            $end_year = $this->session->userdata('current_year');
        else
            $end_year--;

        $data['start'] = $start_year;
        $data['end'] = $end_year;
        return $data;
    }

    function scoring_def_year($year = 0)
    {
        if ($year == 0) // If none passed, assume they want to know for the current year
            $year = $this->current_year;
        $def_year = $this->db->select('max(year) as y')->from('scoring_def')->where('scoring_def.league_id',$this->leagueid)
                ->where('year <=',$year)->get()->row()->y;

        if($def_year != "")
            return $def_year;
        return 0;
    }

    // Returns the range a scoring def year was effective for
    function scoring_def_range($year = 0)
    {
        
        if ($year == 0)
            $year = $this->current_year;
        $start_year = $this->scoring_def_year($year);
        $end_year = $this->db->select('min(year) as y')->from('scoring_def')->where('scoring_def.league_id',$this->leagueid)
            ->where('year > ',$start_year)->get()->row()->y;
        $data['db_start'] = $start_year;
        $data['db_end'] = $end_year;
        // If never changed, look up the first year the league existed
        if ($start_year == 0)
        {
            $sched_row = $this->db->select('distinct(year)')->from('schedule')->where('league_id',$this->leagueid)
            ->order_by('year','asc')->limit(1)->get()->row();
            if($sched_row)
                $start_year = $sched_row->year;
        }
        // If never changed, it's through the current year... if it has, the def doesn't include the last changed year, so decrement 1.
        if ($end_year == 0)
            $end_year = $this->session->userdata('current_year');
        else
            $end_year--;

        $data['start'] = $start_year;
        $data['end'] = $end_year;
        return $data;
    }


    function twitter_post($text)
    {
        $this->load->library('twitteroauth');
		// Loading twitter configuration.
		$settings = $this->db->select('twitter_consumer_token, twitter_consumer_secret, twitter_access_token, twitter_access_secret')
            ->from('league_settings')->where('league_id',$this->leagueid)->get()->row();

        if($settings->twitter_consumer_token && $settings->twitter_consumer_secret && $settings->twitter_access_token && $settings->twitter_access_secret)
        {
            try{
                $connection = $this->twitteroauth->create($settings->twitter_consumer_token,
                                                          $settings->twitter_consumer_secret,
                                                          $settings->twitter_access_token,
                                                          $settings->twitter_access_secret);


                $connection->post('statuses/update', array('status' => $text));
            } catch(Exception $e)
            {
                echo "Error while posting to twitter.";
            }
        }

    }

    function get_roster_max()
    {
        return $this->db->select('roster_max')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->roster_max;
    }

    function get_league_invite_url()
    {
        $mask = $this->db->select('mask_id')->from('league')->where('id',$this->leagueid)->get()->row()->mask_id;
        return site_url('joinleague/invite/'.$mask);
    }

    function get_user_notifications()
    {
        // Actual messages get set in the security_model with session variables.
        if (is_array($this->session->userdata('user_notifications')))
            return $this->session->userdata('user_notifications');
        return array();
    }

    function clear_user_notification($text="")
    {
        $messages = $this->session->userdata('user_notifications');
        foreach($messages as $key => $m)
        {
            if( strpos($m['id'], $text) !== false )
                unset($messages[$key]);
        }
        $this->session->set_userdata('user_notifications',$messages);
    }

    function sit_player($playerid, $teamid, $week, $year)
    {
        $this->common_noauth_model->sit_player($playerid, $teamid, $week, $year, $this->leagueid);
    }

    function start_player($playerid, $posid, $teamid, $week, $year, $weektype)
    {
        $this->common_noauth_model->start_player($playerid, $posid, $teamid, $week, $year, $weektype, $this->leagueid);
    }

    function drop_player($player_id, $teamid)
    {
        $this->common_noauth_model->drop_player($player_id, $teamid, $this->current_year, $this->current_week, $this->current_weektype);
    }

    function get_league_years()
    {
        return $this->db->select('distinct(year)')->from('schedule')->where('league_id',$this->leagueid)
            ->order_by('year','desc')
            ->get()->result();
    }

    function get_byeweeks_array()
    {
        $schedule = $this->db->select('h,v,week')->from('nfl_schedule')->where('year',$this->current_year)
            ->where('gt',$this->week_type)->get()->result();
        $nfl_teams = $this->db->select('distinct(club_id) as club_id')->from('nfl_team')
            ->where('club_id !=','NONE')->get()->result();
        $weeks = $this->num_season_weeks();
        $opp_array = array();
        $bye_array = array();

        foreach($nfl_teams as $t)
        {
            foreach (range(1,$weeks) as $w)
            {
                $opp_array[$w][$t->club_id] = 'bye';
            }
        }

        foreach($schedule as $s)
        {

            $opp_array[$s->week][$s->h] = $s->v;
            $opp_array[$s->week][$s->v] = $s->h;

        }

        foreach($opp_array as $week => $t)
        {
            foreach($t as $team => $opp)
            {
                if ($opp == 'bye')
                    $bye_array[$team] = $week;
            }
        }

        $bye_array['NONE'] = 0;
        $bye_array['FA'] = 0;
        return $bye_array;
    }

    function get_week_type_id($text = "")
    {
        if ($text == "")
            $text = $this->current_weektype;
        return $this->db->select('id')->from('nfl_week_type')->where('text_id',strtoupper($text))->get()->row()->id;
    }

    // An array indexed by league positions containing the csv field of NFL positions for that league position
    function get_leapos_lookup_array($year = "")
    {
        if ($year == "")
            $year = $this->current_year;
        $data = array();
        $pos_year = $this->common_model->league_position_year($year);

        $positions = $this->db->select('id, nfl_position_id_list, text_id as pos_text')->from('position')->where('league_id',$this->leagueid)
            ->where('year',$pos_year)->get()->result();

        foreach($positions as $p)
        {
            $data[$p->id]['list'] = $p->nfl_position_id_list;
            $data[$p->id]['pos_text'] = $p->pos_text;
        }

        return $data;
    }

    function force_league_admin()
    {
        if (!$this->session->userdata('is_league_admin'))
            redirect('/');
    }

    function force_site_admin()
    {
        if (!$this->session->userdata('is_site_admin'))
            redirect('/');
    }


}

?>
