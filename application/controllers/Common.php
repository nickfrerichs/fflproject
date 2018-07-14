<?php

class Common extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');

        // Turn debugging on, if enabled.
        if ($this->session->userdata('debug') && !$this->input->is_ajax_request())
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE,
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE,
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => TRUE
                );
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }
        //$this->load->model('security_model');
        if (!$this->ion_auth->logged_in())
        {
             die();
        }
    }

    function notification_ack($id)
    {
        $acks = $this->session->userdata("notification_acks");
        $acks[] = $id;
        $this->session->set_userdata('notification_acks',$acks);
    }

    function liveElements($last_check_in = 0)
    {
        $this->load->model('league/chat_model');

        $interval = $this->session->userdata('live_element_refresh_time');
        $check_in = $this->chat_model->update_last_check_in();

        $response = array("T" => $check_in);

        if ($this->session->userdata('live_scores'))
            $response["ls"] = "1";
        else
            $response["ls"] = "0";


        $response["ur"] = $this->chat_model->get_unread_count();

        if($this->session->userdata('show_whos_online'))
        {
            // If twice the interval has passed since last wo_check_in, send who is online data
            $last_wo_check_in = $this->input->post('last_wo_check_in');
            if ($last_wo_check_in <= $check_in-($interval*2))
                $response["wo"] = $this->chat_model->whos_online();
        }


        // Check for new chat messages since last_checked_in, add them to this array so they can
        // Be popped up to the user
        if ($this->session->userdata('chat_balloon'))
        {
            $last_chat_key = $this->input->post('last_chat_key');
            if (is_numeric($last_chat_key))
            {
                $response["cm"] = $this->chat_model->get_messages($last_chat_key,5,False);
            }
            else
            {
                $response['ck'] = $this->chat_model->get_chat_key();
            }
        }

        echo json_encode($response);
    }

}
?>
