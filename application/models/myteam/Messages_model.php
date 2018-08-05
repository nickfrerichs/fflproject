<?php

class Messages_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->ownerid = $this->session->userdata('owner_id');
    }

    function get_league_teams_data()
    {
    	return $this->db->select('id, team_name')->from('team')
    		->where('league_id',$this->leagueid)
    		->where('active',1)
    		->where_not('id',$this->teamid)
            ->order_by('team_name','asc')
    		->get()->result();
    }

    function get_league_owners_data()
    {
    	return $this->db->select('owner.id as owner_id, team.id as team_id, owner.first_name, owner.last_name')
    		->from('team')->join('owner', 'owner.id = team.owner_id')
    		->where('team.active',1)->where('team.league_id',$this->leagueid)
            ->order_by('owner.last_name','asc')
    		->get()->result();
    }

    function insert_new_message($to, $subject, $body)
    {
        $message_date = date("Y-m-d H:i:s");
        // One copy for sender, sent items folder, read
        $this->db->insert('message', array('team_id' => $this->teamid,
                                          'to_team_id' => $to,
                                          'from_team_id' => $this->teamid,
                                          'subject' => $subject,
                                          'body' => $body,
                                          'read' => 1,
                                          'message_date' => $message_date,
                                          'folder_id' => 1));

        // One copy for recipient, inbox, unread
        $this->db->insert('message', array('team_id' => $to,
                                          'to_team_id' => $to,
                                          'from_team_id' => $this->teamid,
                                          'subject' => $subject,
                                          'body' => $body,
                                          'read' => 0,
                                          'message_date' => $message_date,
                                          'folder_id' => 0));



        $body = "League: ".$this->session->userdata('league_name').
                "\nMessage From: ".$this->session->userdata('first_name')." ".$this->session->userdata('last_name').
                " (".$this->session->userdata('team_name').")\n-------------------\n\n".$body;
        $this->email_new_message($to,$subject,$body);

    }

    function email_new_message($to, $subject, $body)
    {
        $recipient = $this->db->select('email')->from('team')->join('owner','owner.id = team.owner_id')
            ->join('user_accounts','user_accounts.id = owner.user_accounts_id')
            ->where('team.id',$to)->get()->row()->email;

        $this->config->load('fflproject');
        $this->load->library('email');
        $this->email->from($this->config->item('fflp_email_reply_to'), $this->config->item('fflp_email_site_title'));
        $this->email->to($recipient);
        $this->email->subject($subject);
        $body = prepare_email_body($body);
        $this->email->message($body);
        $this->email->send();
    }

    function get_messages_from_folder($folder_id)
    {
        return $this->db->select('from_team_id, subject, UNIX_TIMESTAMP(message_date) as unix_date')
            ->select('message.id, read, team.team_name as from_team_name, owner.first_name, owner.last_name')
            ->from('message')->join('team','team.id = message.from_team_id')
            ->join('owner', 'owner.id = team.owner_id')
            ->where('team_id',$this->teamid)->where('folder_id',$folder_id)
            ->order_by('unix_date','desc')
            ->get()->result();
    }

    function get_message($id)
    {
        $data = array('read' => 1);
        $this->db->where('id',$id);
        $this->db->update('message',$data);
        return $this->db->select('from_team_id, subject, UNIX_TIMESTAMP(message_date) as unix_date, body')
            ->select('message.id, read, team.team_name as from_team_name, owner.first_name, owner.last_name')
            ->from('message')->join('team','team.id = message.from_team_id')
            ->join('owner', 'owner.id = team.owner_id')
            ->where('message.id',$id)->get()->row();
    }

    function trash_message($id)
    {
        $this->db->where('id',$id)->update('message', array('folder_id' => 2));
    }

    function delete_message($id,$forever=false)
    {
        $message = $this->db->select('folder_id')->from('message')->where('id',$id)->get()->row();
        if ($message->folder_id == 2 || $forever)
        {
            $this->db->delete('message', array('id' => $id));
            return "Message deleted forever.";
        }
        else
        {
            $this->trash_message($id);
            return "Message moved to trash.";
        }
    }
}
