<?php

class Common extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('security_model');
    }

    function message_ack($id)
    {
        $acks = $this->session->userdata("message_acks");
        $acks[] = $id;
        $this->session->set_userdata('message_acks',$acks);
    }
}
?>
