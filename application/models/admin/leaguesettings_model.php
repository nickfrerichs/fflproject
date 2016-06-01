<?php

class Leaguesettings_model extends MY_Model{

    function get_league_settings_data($leagueid)
    {
        return $this->db->select('s.max_teams, s.roster_max, s.shared_player_pool, s.join_password, s.nfl_season,')
            ->select('s.twitter_consumer_token, s.twitter_consumer_secret, s.twitter_access_token, s.twitter_access_secret')
            ->select('s.twitter_player_moves, s.twitter_chat_updates, league.league_name, s.offseason')
            ->from('league')->join('league_settings as s','s.league_id = league.id')
            ->where('league.id',$leagueid)
            ->get()->row();
    }

    function toggle_setting($leagueid,$item)
    {
        $lookup = array('playermoves' => 'twitter_player_moves',
                        'chatupdates' => 'twitter_chat_updates',
                        'offseason' => 'offseason');
        $val = !$this->db->select($lookup[$item])->from('league_settings')->where('league_id',$leagueid)
            ->get()->row()->{$lookup[$item]};
        $this->db->where('league_id',$leagueid);
        $this->db->update('league_settings',array($lookup[$item] => $val));
        return $val;
    }

    function change_setting($leagueid, $type, $value)
    {
        $lookup = array('maxteams' => 'max_teams',
              'rostermax' => 'roster_max',
              'joinpassword' => 'join_password',
              'consumertoken' => 'twitter_consumer_token',
              'consumersecret' => 'twitter_consumer_secret',
              'accesstoken' => 'twitter_access_token',
              'accesssecret' => 'twitter_access_secret');

        $this->db->where('league_id',$leagueid);
        $this->db->update('league_settings', array($lookup[$type] => $value));
        return $this->db->affected_rows();
    }

}
?>
