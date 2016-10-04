<?php
class Sse extends MY_User_Controller{

// This controller presents the ajax data used by various jquery ajax/post functions.
// The views dislay content to be put between <tbody></tbody> tags.

    function __construct()
    {
        parent::__construct();
        $this->load->model('sse_model');
    }

    // The stream of data
    function stream()
    {
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

        $first = True;

        while($count > 0)
        {
            $start = microtime(True);
            $now = time();
            $settings = $this->sse_model->get_sse_settings();
            $keys = $this->sse_model->keys();
            $data = array();

            # REMOVE, JUST FOR debugging
            $settings->sse_live_scores = True;
            // If the chat_key is new, output chats that occured since the last one.
            if (($settings->sse_chat || $this->session->userdata('chat_balloon')) && $last_keys->chat_key != $keys->chat_key)
            {
                $this->load->model('league/chat_model');
                // If chat is open, include the owners messages too, otherwise we don't want balloons for ourself
                // {"message_id":"1115","message_text":"asdf","date":"1474228635","owner_id":"11","first_name":"Nick","last_name":"Frerichs","chat_name":"Nick"}
                if ($settings->sse_chat)
                    $chatdata = $this->chat_model->get_messages($last_keys->chat_key,5,True);
                else
                    $chatdata = $this->chat_model->get_messages($last_keys->chat_key,5,True);
                if (count($chatdata)>0)
                    $data['chat'] = $chatdata;

            }

            // If sse_draft is set, and the draft_update_key has changed, output stuff needed for the draft.
            if (($settings->sse_draft && $last_keys->draft_update_key != $keys->draft_update_key))
            {
                echo "New Draft\n";
            }

            // If sse_live_scores is set and live_scores_key has changed, output stuff needed for live scores.
            if ($first || ($settings->sse_live_scores && $last_keys->live_scores_key != $keys->live_scores_key))
            {
                $this->load->model('season/scores_model');
                $data['live']['scores'] = $this->scores_model->get_fantasy_scores_array();
                $data['live']['players_live'] = $this->scores_model->get_player_live_array();
                $data['live']['nfl_games'] = $this->scores_model->get_nfl_game_live_array();
                $this->sse_model->set_last_live_score($settings->last_live_score);
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
            $first = False;
        }
        echo "\n";
        echo $runtime;
        echo "\n";
        echo $runtime/$num;

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

    function test()
    {
        $settings = $this->sse_model->get_sse_settings();
    }
}
?>
