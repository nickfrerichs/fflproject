<?php
class Cli extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        if (!is_cli())
        {
            echo "Must run from command line.";
            exit;
        }
        $this->load->model('automation_model');
    }

    function ww_approve()
    {   
        $this->load->model('common_noauth_model');
        $leagues = $this->common_noauth_model->get_leagues_data();
        foreach($leagues as $l)
        {
            $this->automation_model->approve_waiver_wire_requests($l->id);
        }
    }
}

 ?>
