<?php
class Sse extends MY_User_Controller{

// This controller presents the ajax data used by various jquery ajax/post functions.
// The views dislay content to be put between <tbody></tbody> tags.

    function __construct()
    {
        parent::__construct();
        $this->load->model('sse_model');
    }

    function test()
    {
        $this->load->model('season/draft_model');
        $this->draft_model->get_update_key();
    }

    // The stream of data
    function stream($sse_func = "")
    {
        $sse_live_scores = False;
        $sse_live_draft = False;
        if ($sse_func == 'sse_live_scores')
            $sse_live_scores = True;
        if ($sse_live_draft || $sse_func == 'sse_live_draft')
        {
            $this->load->model('season/draft_model');
            $this->load->model('myteam/myteam_settings_model');
            $last_draft_key_update = 0;
            $sse_live_draft = True;
        }
        //$this->session->set_userdata('sse')
        session_write_close();
        //$count = 10;
        header("Content-Type: text/event-stream\n\n");
        header("Cache-Control: no-cache\n\n");
        $num = 1000;
        $count = $num;
        $runtime = 0;
        // live_scores_key, draft_update_key, chat_key
        $last_keys = $this->sse_model->keys();
        $this->sse_model->reset_sse_settings();
        $last_live_element_check = 0;
        $interval = $this->session->userdata('live_element_refresh_time');

        $ls_first = True;
        $draft_first = True;

        while($count > 0)
        {
            $start = microtime(True);
            $now = time();
            $settings = $this->sse_model->get_sse_settings();  // Is this even used??
            $keys = $this->sse_model->keys();
            $data = array();

            // If the chat_key is new, output chats that occured since the last one.
            // Need a way to know to send chats if chat_balloon is disabled...ran into complications because two windows could be open.
            // So for now, I'm always sending chats, they will get ignored if not needed.
            if ((1==1 || $this->session->userdata('chat_balloon')) && $last_keys->chat_key != $keys->chat_key)
            {
                $this->load->model('league/chat_model');
                // If chat is open, include the owners messages too, otherwise we don't want balloons for ourself
                if ($settings->sse_chat)
                    $chatdata = $this->chat_model->get_messages($last_keys->chat_key,5,True);
                else
                    $chatdata = $this->chat_model->get_messages($last_keys->chat_key,5,True);
                if (count($chatdata)>0)
                    $data['chat'] = $chatdata;

            }

            if ($sse_live_draft)
            {
                //
                $seconds_left = $keys->draft_update_key - $now;
                if ($seconds_left < 0)
                    $seconds_left = -1;
                if (!isset($last_seconds_left) || ($last_seconds_left != $seconds_left && $seconds_left % 5 == 0))
                {   
                    $last_seconds_left = $seconds_left;
                   // $adadsf = "";
                    //$seconds_left = $keys->draft_update_key - $now;
                    $data['live_draft']['seconds_left'] = $seconds_left;
                }
                // If sse_draft is set, and the draft_update_key has changed, output stuff needed for the draft.
                //if (($settings->sse_draft && $last_keys->draft_update_key != $keys->draft_update_key))
                if (($draft_first) || ($last_keys->draft_update_key != $keys->draft_update_key) || ($last_keys->draft_paused != $keys->draft_paused))
                {
                    $draft_first = false;
                    $draft_settings = $this->draft_model->get_settings();
                    $data['live_draft']['update'] = True;
                    $data['live_draft']['paused'] = $keys->draft_paused;
                    // Get last X players picked: playerid & drafted team text
                    $data['live_draft']['recent_picks'] = $this->draft_model->get_recent_picks_data();
                    $data['live_draft']['current_pick'] = $this->draft_model->get_current_pick_data();
                    $data['live_draft']['upcoming_picks'] = $this->draft_model->get_upcoming_picks_data();
                    $data['live_draft']['start_time'] = $draft_settings->draft_start_time;
                    $data['live_draft']['current_time'] = $now;

                    if ($data['live_draft']['paused'] > 0)
                        $data['live_draft']['current_pick']->{'seconds_left'} = $data['live_draft']['paused'];
                    else
                        $data['live_draft']['current_pick']->{'seconds_left'} = $keys->draft_update_key - $now;

                    if ($data['live_draft']['current_pick']->logo)
                        $data['live_draft']['current_pick']->{'logo_url'} = $this->myteam_settings_model->get_logo_url($data['live_draft']['current_pick']->team_id,'thumb');
                    else
                        $data['live_draft']['current_pick']->{'logo_url'} = $this->myteam_settings_model->get_default_logo_url();
    
                    $data['live_draft']['update'] = True;

                    $data['live_draft']['myteam'] = $this->draft_model->get_myteam();
                    $data['live_draft']['byeweeks'] = $this->common_model->get_byeweeks_array();
                }

                // In case of stale key (no one is watching the draft), do a refresh on first load.
                // This also handles when a player misses a pick
                if ($keys->draft_update_key < $now)
                    $this->draft_model->get_update_key();
            }

            // If sse_live_scores is set and live_scores_key has changed, output stuff needed for live scores.
            if (($ls_first && $sse_live_scores) || ($sse_live_scores && $last_keys->live_scores_key != $keys->live_scores_key))
            {
                $this->load->model('season/scores_model');
                $data['live']['players_live'] = $this->scores_model->get_player_live_array();
                $data['live']['nfl_games'] = $this->scores_model->get_nfl_game_live_array();
                $data['live']['scores'] = $this->scores_model->get_fantasy_scores_array();
                $data['live']['key'] = $keys->live_scores_key;
                $ls_first = False;
            }

            // Update things at a slower interval like live scoring icon, etc
            // Whos_online goes here too.
            if ($last_live_element_check < $now - $interval)
            {
                $this->load->model('security_model');
                $this->load->model('league/chat_model');
                $check_in = $this->chat_model->update_last_check_in();
                if ($this->security_model->live_scores_on())
                    $data['ls'] = 'on';
                else
                    $data['ls'] = 'off';
                $last_live_element_check = $now;

                if($this->session->userdata('show_whos_online'))
                    $data['wo'] = $this->chat_model->whos_online();

                $data["ur"] = $this->chat_model->get_unread_count();
            }
            $runtime += (microtime(True) - $start);
            //$data['debug'] = number_format((microtime(True) - $start),2);
            if (count($data) >0)
                sse_json($data);
            ob_flush(); // Needed to add this after moving to centos, no idea why.
            flush();
            $runtime += (microtime(True) - $start);
            usleep(250000); //half a second
            //$count--;
            $last_keys = $keys;
        }
        // echo "\n";
        // echo $runtime;
        // echo "\n";
        // echo $runtime/$num;

    }

    // Update session varaible containing enabled SSE functions
    function turn_on($function)
    {
        $this->sse_model->turn_on($function);
    }

    // Update session variable to remove an SSE function
    function turn_off($function)
    {
        $this->sse_model->turn_off($function);
    }

}
?>
