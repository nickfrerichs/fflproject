<?php

class Test_ extends CI_Controller{

    function __construct()
    {
        parent::__construct();
        //$this->load->model('common/common_model');
    }

    function mail()
    {
        echo "Sending a test message.";
        gmail_send('frerichs@gmail.com','Test Subject','Test Body');
    }

    function editor()
    {
        $this->user_view('editor_test');
    }

    function twitter()
    {
        $this->common_model->twitter_post('test again 3');
    }

    function live()
    {
        $this->load->model('security_model');
        $this->security_model->live_scores_on();
    }

    function email()
    {
        # To test sending email using codeigniter email helper
        $this->load->library('email');
        $this->email->clear();
        $this->email->initialize();
        $this->email->set_newline("\r\n");
        $this->email->from("noreply@mylanparty.net", "FFL");
        $this->email->to("frerichs@gmail.com");
        $this->email->subject("Test Email");
        $this->email->message("Test Email Body");
        print_r($this->email);

        echo $this->email->send();
    }
}
?>
