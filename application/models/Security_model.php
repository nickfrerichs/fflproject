<?php

class Security_model extends CI_Model
{
    protected $is_owner = False;
    protected $userid;
    function __construct()
    {
        parent::__construct();

        $this->load->library('ion_auth');

        $this->load->model('common/common_noauth_model');

        $this->site_settings = $this->db->select('name, debug_user, debug_admin, debug_year, debug_week, debug_week_type_id')
            ->select('session_refresh_time, live_element_refresh_time')
            ->from('site_settings')->get()->row();
        $this->userid = $this->ion_auth->get_user_id();
        $this->session->set_userdata('user_id',$this->userid);
        if ($this->db->from('owner')->where('user_accounts_id',$this->userid)->count_all_results() > 0)
            $this->is_owner = True;
    }

    function set_session_variables()
    {
        // Set owner session variables (accounts that are in a league)
        $this->session->set_userdata('is_owner',$this->is_owner);
        if ($this->is_owner)
        {
            $this->set_owner_session_variables();
            $this->set_dynamic_session_variables();
        }

        // Set debug session variable
        if ($this->site_settings->debug_user)
            $this->session->set_userdata('debug',True);
        elseif($this->site_settings->debug_admin && $this->ion_auth->is_admin())
            $this->session->set_userdata('debug',True);
        else
            $this->session->set_userdata('debug',False);

        $this->session->set_userdata('site_name', $this->site_settings->name);
        $this->session->set_userdata('is_site_admin',$this->ion_auth->is_admin());

        $this->session->set_userdata('CI_VERSION',CI_VERSION);
    }

    function set_owner_session_variables()
    {
        $owner = $this->db->select('owner.id as owner_id, owner.active_league, owner.first_name, owner.last_name')
                ->select('team.id as team_id, team_name, owner.active_league, team.active, league.league_name, league.season_year')
                ->select('league_settings.offseason, owner_setting.chat_balloon, league_settings.show_whos_online, league_settings.use_draft_ranks')
                ->from('owner')
                ->join('team','team.owner_id = owner.id and team.league_id = owner.active_league and team.active = 1','left')
                ->join('league','league.id = owner.active_league','left')
                ->join('league_settings','league_settings.league_id = owner.active_league','left')
                ->join('owner_setting','owner_setting.owner_id = owner.id','left')
                ->where('owner.user_accounts_id',$this->userid)->get()->row();

        $this->session->set_userdata('owner_id', $owner->owner_id);
        $this->session->set_userdata('league_id', $owner->active_league);
        $this->session->set_userdata('team_id', $owner->team_id);
        $this->session->set_userdata('team_name', $owner->team_name);
        $this->session->set_userdata('first_name', $owner->first_name);
        $this->session->set_userdata('last_name', $owner->last_name);
        $this->session->set_userdata('league_name', $owner->league_name);
        $this->session->set_userdata('offseason', $owner->offseason);
        $this->session->set_userdata('chat_balloon', $owner->chat_balloon);
        $this->session->set_userdata('use_draft_ranks', $owner->use_draft_ranks);


        if ($this->db->from('league_admin')->where('league_id',$owner->active_league)->where('league_admin_id',$this->userid)->get()->num_rows() > 0)
            $this->session->set_userdata('is_league_admin', True);
        else
            $this->session->set_userdata('is_league_admin', False);

        if ($owner->show_whos_online == 1 || ($owner->show_whos_online == 2 && $this->session->userdata('is_league_admin')))
            $this->session->set_userdata('show_whos_online',True);
        else
            $this->session->set_userdata('show_whos_online',False);

        $week_type = $this->db->select('nfl_season')->from('league_settings')->where('league_id',$this->session->userdata('league_id'))->get()->row()->nfl_season;
        $this->session->set_userdata('week_type', $week_type);

        $this->load->model('league/chat_model');

        $names = $this->chat_model->get_firstnames();

        if ($names[strtolower($owner->first_name)] > 1)
            $chatname = $owner->first_name.' '.$owner->last_name[0];
        else
            $chatname = $owner->first_name;
        $this->session->set_userdata('chat_name',$chatname);

        $this->load_leagues($this->session->userdata('owner_id'));

    }

    // The idea is that these will be checked every X mins as an in between for at login and
    // every page load.
    function set_dynamic_session_variables()
    {
        $this->session->set_userdata('session_refresh_time',$this->site_settings->session_refresh_time);
        $this->session->set_userdata('live_element_refresh_time',$this->site_settings->live_element_refresh_time);
        if($this->session->userdata('is_owner'))
        {
            $week_year = $this->common_noauth_model->get_current_week_year($this->session->userdata('league_id'));
            if ($this->site_settings->debug_week == -1)
                $this->session->set_userdata('current_week', $week_year->week);
            else
            {
                $this->session->set_userdata('current_week', $this->site_settings->debug_week);
                $this->session->set_userdata('debug_week', True);
            }

            if ($this->site_settings->debug_year == -1)
                $this->session->set_userdata('current_year', $week_year->year);
            else
            {
                $this->session->set_userdata('current_year', $this->site_settings->debug_year);
                $this->session->set_userdata('debug_year', True);
            }

            $this->session->set_userdata('expire_league_vars',time()+$this->session->userdata('session_refresh_time')); // Make sure to check dynamic vars every 1 mins.
            $this->session->set_userdata('live_scores',$this->live_scores_on());

            $this->session->set_userdata('draft_in_progress',$this->draft_in_progress());
            $this->set_user_notifications();
        }
    }

    function set_user_notifications()
    {

        $messages = array();

        // Check for unread messages, confusing cause I call notices to the user "messages" too.
        $msgs = $this->db->from('message')->where('to_team_id',$this->session->userdata('team_id'))
                    ->where('team_id',$this->session->userdata('team_id'))->where('read',0)->count_all_results();
        if ( $msgs > 0)
        {
            if ($msgs == 1)
                $note = "You have a new message.";
            else
                $note = "You have ".$msgs." new messages.";
            $date = $this->db->select('unix_timestamp(max(message_date)) as message_date')->from('message')
                ->where('team_id',$this->session->userdata('team_id'))->get()->row()->message_date;
            $messages[] = array('class'=>'primary',
                                'message'=> $note.
                                            '<br><a href="'.site_url('myteam/messages').'">Go to your Inbox</a>',
                                'id'=>'msg_new_messages'.$date);
        }

        if($this->session->userdata('draft_in_progress'))
        {
            $messages[] = array('class' => 'primary',
                                'message' => '<a href="'.site_url('season/draft/live').'" data-ackurl="'.
                                site_url('common/notification_ack/msg_draft_in_progress').'" class="_notification-close">Join the Draft currently in progress.</a>',
                                'id' => 'msg_draft_in_progress');
        }

        // Look for open trades
        $trades = $this->db->from('trade')->where('team2_id', $this->session->userdata('team_id'))
            ->where('year',$this->session->userdata('current_year'))->where('completed',0)->where('expires > NOW()')
            ->where('completed_date',0)->count_all_results();
        if ($trades > 0)
        {
            if ($trades == 1)
                $note = "You have 1 trade awaiting a response.";
            else
                $note = "You have ".$trades." trades awaiting responses.";
            $messages[] = array('class'=>'primary',
                                'message'=> $note.
                                '<br><a href="'.site_url('myteam/trade').'" data-ackurl="'.site_url('common/message_ack/msg_open_trades').'" class="_notification-close">View Trades</a>',
                                'id'=>'msg_open_trades');
        }

        $this->session->set_userdata('user_notifications',$messages);

    }

    function draft_in_progress()
    {
        $count = $this->db->from('league_settings')->where('league_id',$this->session->userdata('league_id'))
            ->where('draft_pick_id >',0)->count_all_results();
        if ($count > 0)
            return True;
        return False;
    }

    function live_scores_on()
    {
        $num = $this->db->from('nfl_live_game')->join('nfl_week_type','nfl_week_type.id = nfl_live_game.nfl_week_type_id')
            ->where('nfl_week_type.text_id',$this->session->userdata('week_type'))
            ->where('year',$this->session->userdata('current_year'))->where('week',$this->session->userdata('current_week'))
            ->not_like('quarter','final')->count_all_results();

        if ($num > 0)
            return True;
        return False;
    }



    function t_mysql($unixtimestamp)
    {
        return date("Y-m-d H:i:s", $unixtimestamp);
    }

    function load_leagues($userid)
    {
        $rows = $this->db->select('league.id, league.league_name')->from('team')
            ->join('league','league.id = team.league_id')->where('team.owner_id',$userid)
            ->where('team.active',1)->get()->result();

        $leagues = array();
        foreach($rows as $row)
            $leagues[$row->id] = $row->league_name;

        $this->session->set_userdata('leagues', $leagues);

    }

}
