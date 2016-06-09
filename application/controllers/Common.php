<?php

class Common extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');

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
        if (!$this->flexi_auth->is_logged_in())
        {
             die();
        }
    }

    function message_ack($id)
    {
        $acks = $this->session->userdata("message_acks");
        $acks[] = $id;
        $this->session->set_userdata('message_acks',$acks);
    }

    function liveElements($last_check_in = 0)
    {

        $response = array("T" => time());
        $response["last_check_in"] = $last_check_in;
        if ($this->session->userdata('live_scores'))
            $response["ls"] = "1";
        else
            $response["ls"] = "0";

        $this->load->model('league/chat_model');
        $response["ur"] = $this->chat_model->get_unread_count();
        // Check for new chat messages since last_checked_in, add them to this array so they can
        // Be popped up to the user
        if ($last_check_in > time()-20)
            $response["cm"] = $this->chat_model->get_messages($last_check_in,5);

        echo json_encode($response);
    }
}
?>
