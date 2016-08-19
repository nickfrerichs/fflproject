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

        //
        // $club_id = $this->player_club_id($playerid);
        // $row = $this->db->select('UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
        //     ->where('year = '.$this->current_year.' and week ='.$this->current_week.' and gt ="'.$this->current_weektype.'"'.
        //     ' and (h="'.$club_id.'" or v="'.$club_id.'")')
        //     ->get()->row();
        //
        //
        // if (count($row) == 0)
        //     return "";
        // else
        //     return $row->start_time;
    }

    function is_player_lineup_locked($player_id)
    {
        return $this->common_noauth_model->is_player_lineup_locked($player_id, $this->leagueid, $this->current_year, $this->current_week, $this->current_weektype);
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
        // return $this->db->select('nfl_team.club_id')->from('player')->join('nfl_team','nfl_team.id = player.nfl_team_id')
        //     ->where('player.id',$playerid)->get()->row()->club_id;
    }

    function team_info($team_id)
    {
        return $this->db->select('team.team_name, owner.first_name, owner.last_name, owner.id as owner_id')
            ->select('team.id as team_id, uacc_email as owner_email')
            ->from('team')
            ->join('owner','owner.id = team.owner_id')
            ->join('user_accounts','uacc_id = owner.user_accounts_id')
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

    function num_weeks_in_schedule()
    {
        $row = $this->db->select('max(week) as w')->from('schedule')->where('league_id',$this->leagueid)->where('year',$this->current_year)->get()->row();
        if ($row->w == "")
            return 0;
        return $row->w;
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

    function get_user_messages()
    {
        // Actual messages get set in the security_model with session variables.
        if (is_array($this->session->userdata('user_messages')))
            return $this->session->userdata('user_messages');
        return array();
    }

    function clear_user_message($text="")
    {
        $messages = $this->session->userdata('user_messages');
        foreach($messages as $key => $m)
        {
            if( strpos($m['id'], $text) !== false )
                unset($messages[$key]);
        }
        $this->session->set_userdata('user_messages',$messages);
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
}

?>
