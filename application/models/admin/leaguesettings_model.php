<?php

class Leaguesettings_model extends MY_Model{

    function get_league_settings_data($leagueid)
    {
        return $this->db->select('s.max_teams, s.roster_max, s.shared_player_pool, s.join_password, s.nfl_season,')
            ->select('s.twitter_consumer_token, s.twitter_consumer_secret, s.twitter_access_token, s.twitter_access_secret')
            ->select('s.twitter_player_moves, s.twitter_chat_updates, league.league_name')
            ->from('league')->join('league_settings as s','s.league_id = league.id')
            ->where('league.id',$leagueid)
            ->get()->result();
    }
}
?>
