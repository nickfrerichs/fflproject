<?php

class Joinleague extends CI_Controller{

    function __construct()
    {
        parent::__construct();

        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');

        if ((1==1) && (!$this->input->is_ajax_request()) && $this->flexi_auth->is_admin())
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

    function invite($mask_id="", $code="")
    {
        if (!$this->flexi_auth->is_logged_in())
        {
            // If they aren't logged in, ask them to login, or register new account
            //redirect('accounts/register/'.$mask_id.'/'.$code);
            ?>
            <div class="container">
                <div class="row">
                    <div>
                        <a href="<?=site_url('/?redirect=joinleague/invite/'.$mask_id.'/'.$code)?>">Login to join this league.</a><br>
                        - or -<br>
                    <a href="<?=site_url('accounts/register/'.$mask_id.'/'.$code)?>">Create a new account and join.</a></div>
                </div>
            </div>
            <?php
            //echo "Not logged in";
            //redirect('/?redirect=joinleague/invite/'.$mask_id.'/'.$code);
        }
        else
        {
            echo "Logged in and ready to join league";
        }

    }


}

?>
