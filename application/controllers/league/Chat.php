<?php

class Chat extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/chat_model');
    }


    public function post()
    {
        $message = $this->input->post('message');
        $this->chat_model->save_message($message);
    }

    public function get_messages()
    {
        $data = array();

        // get chats newer than this key
        if($this->input->post('chat_key'))
        {
            $data['messages'] = $this->chat_model->get_messages($this->input->post('chat_key'));
        }
        else // return all chats
        {
            $data['messages'] = $this->chat_model->get_messages();

        }
        $this->load->view('user/league/chat/ajax_show_messages',$data);

        $this->chat_model->set_last_read_key();


    }

    public function ajax_get_messages()
    {
        $key = $this->input->post(('chat_key'));
        $this->chat_model->set_last_read_key($key);
        $messages = $this->chat_model->get_messages();
        //print_r($messages);
        echo json_encode($messages);


    }

    public function stream_get_chat_key()
    {
        //$count = 10;
        header("Content-Type: text/event-stream\n\n");
        header("Cache-Control: no-cache\n\n");
        while(1)
        {
            echo "data: ".$this->chat_model->get_chat_key()."\n\n";
            ob_flush(); // Needed to add this after moving to centos, no idea why.
            flush();
            usleep(500000); //half a second
            //$count--;
        }
    }

    public function key()
    {
        echo $this->chat_model->get_chat_key();
    }

    public function unread()
    {
        echo $this->chat_model->get_unread_count();
    }

    public function last_read()
    {
        echo $this->chat_model->get_last_read_key();
    }
}
?>
