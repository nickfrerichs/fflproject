<?php

class Test_ extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('common/common_model');
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
}
?>
