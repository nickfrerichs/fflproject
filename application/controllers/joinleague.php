<?php

class Joinleague extends CI_Controller{

    function __construct()
    {
        parent::__construct();

        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');

        if ((1==0) && (!$this->input->is_ajax_request()) && $this->flexi_auth->is_admin())
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

            $data = array('v' => 'guest_invite',
                          'mask_id' => $mask_id,
                          'code' => $code);
            $this->load->view('template/simple',$data);
            //echo "Not logged in";
            //redirect('/?redirect=joinleague/invite/'.$mask_id.'/'.$code);
        }
        else
        {
            redirect('user/joinleague/'.$mask_id.'/'.$code);
        }
    }
}

?>
