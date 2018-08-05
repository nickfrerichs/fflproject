<?php

class Messages extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/messages_model');
        $this->bc['My Team'] = "";
        $this->bc['Messages'] = "";
    }

    function index()
    {
        $data = array();
        $data['inbox'] = $this->messages_model->get_messages_from_folder(0);
    	$this->user_view('user/myteam/messages',$data);
    }

    function test()
    {
        print_r($this->messages_model->get_messages_from_folder(1));
    }

    function compose($action="",$id="")
    {
        $data = array();
        if ($action == "reply")
        {
            $message = $this->messages_model->get_message($id);
            $data['reply_teamid'] = $message->from_team_id;
            if (strtolower(substr($message->subject,0,4)) != 're:')
                $data['reply_subject'] = 'Re: '.$message->subject;
            else
                $data['reply_subject'] = $message->subject;
        }
        $data['owners'] = $this->messages_model->get_league_owners_data();
        $data['teamid'] = $this->teamid;
        $this->bc['Messages'] = site_url('myteam/messages');
        $this->bc['Compose Message'] = "";
        $this->user_view('user/myteam/messages/compose', $data);
    }

    function send_message()
    {
        $to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $body = $this->input->post('body');
        $this->messages_model->insert_new_message($to, $subject, $body);
    }

    function ajax_get_message()
    {
        $id = $this->input->post('id');
        $data['message'] = $this->messages_model->get_message($id);

        $this->load->model('security_model');
        $this->security_model->set_user_notifications();

        $this->load->view('user/myteam/messages/ajax_display_message',$data);
    }

    function delete_message()
    {
        $forever = false;
        if ($this->input->post('forever') == true)
            $forever = true;
        $response = array('success' => false, 'msg' => '');
        $response['msg'] = $this->messages_model->delete_message($this->input->post('id'),$forever);
        $response['success'] = true;
        //$this->messages_model->delete_message(9);

        echo json_encode($response);

    }

    function ajax_message_list()
    {
        $data['messages'] = $this->messages_model->get_messages_from_folder($this->input->post('id'));
        $this->load->view('user/myteam/messages/ajax_message_list',$data);
    }

}
