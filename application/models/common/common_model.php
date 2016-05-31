<?php
class Common_model extends CI_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->current_weektype = $this->session->userdata('week_type');
        $this->leagueid = $this->session->userdata('league_id');
    }

    function player_game_start_time($playerid)
    {
        $club_id = $this->player_club_id($playerid);
        $row = $this->db->select('UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year = '.$this->current_year.' and week ='.$this->current_week.' and gt ="'.$this->current_weektype.'"'.
            ' and (h="'.$club_id.'" or v="'.$club_id.'")')
            ->get()->row();


        if (count($row) == 0)
            return "";
        else
            return $row->start_time;
    }

    function player_opponent($player_id,$week=0,$year=0)
    {
        if ($year == 0)
            $year = $this->session->userdata('current_year');
        if ($week == 0)
            $week = $this->session->userdata('current_week');

        $p_team = $this->player_club_id($player_id);

        $game = $this->db->select('v,h,UNIX_TIMESTAMP(start_time) as start_time')->from('nfl_schedule')
            ->where('year = '.$year.' and week = '.$week.' and gt ="'.$this->current_weektype.'"'.
            ' and (v = "'.$p_team.'" or h = "'.$p_team.'")')
            ->get()->row();
        if (count($game) == 0)
            return 'Bye';
        if ($p_team == $game->v)
            return '@'.$game->h;
        else
            return $game->v;

    }

    function player_club_id($playerid)
    {
        return $this->db->select('nfl_team.club_id')->from('player')->join('nfl_team','nfl_team.id = player.nfl_team_id')
            ->where('player.id',$playerid)->get()->row()->club_id;
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
        if ($this->current_weektype == "REG")
            return 17;
        return 0;
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
        if ($year == 0) // If none passed, assume they want to know for the current year
            $year = $this->current_year;
        return $this->db->select('max(year) as y')->from('position')->where('position.league_id',$this->leagueid)
                ->where('year <=',$year)->get()->row()->y;
    }

    function scoring_def_year($year = 0)
    {
        if ($year == 0) // If none passed, assume they want to know for the current year
            $year = $this->current_year;
        return $this->db->select('max(year) as y')->from('scoring_def')->where('scoring_def.league_id',$this->leagueid)
                ->where('year <=',$year)->get()->row()->y;
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
}

?>
