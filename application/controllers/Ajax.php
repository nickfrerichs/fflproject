<?php

class Ajax extends CI_Controller{
    
    function __construct()
    {
        parent::__construct();
        if (1==2) 
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE, 
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE, 
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => TRUE
                ); 
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }
    }
    
    function players($var = "")
    {
        $this->load->library('ion_auth');

        // If not logged in redirect to login page
        if (!$this->ion_auth->logged_in())
        {
             die();
        }    
        $this->load->model('ajax_model');
        $results = $this->ajax_model->player_search($var);
        header("Content-Type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        echo '<playerlist>';
        foreach ($results as $result)
        {
            echo '<player>';
            echo '<name>'.$result->full_name.' ('.$result->club_id.')</name>';
            echo '<playerid>'.$result->id.'</playerid>';
            echo '</player>';
        }
        echo '</playerlist>';
    }
    
}
