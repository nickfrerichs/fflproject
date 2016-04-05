<?php

class Chat_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        //$this->current_year = $this->session->userdata('current_year');
        //$this->current_week = $this->session->userdata('current_week');
        $this->ownerid = $this->session->userdata('owner_id');
        $this->leagueid = $this->session->userdata('league_id');
    }

    function save_message($message)
    {
        $now = date('Y-m-d H:i:s');
        $data = array('owner_id' => $this->ownerid,
                        'message_text' => $message,
                        'message_date' => $now,
                        'league_id' => $this->leagueid);
        $this->db->insert('chat_message', $data);
        $chat_key = $this->db->insert_id();

        $this->db->where('league_settings.league_id',$this->leagueid);
        $this->db->update('league_settings',array('chat_key' => $chat_key));

        $twitteron = $this->db->select('twitter_chat_updates')->from('league_settings')
            ->where('league_id',$this->leagueid)->get()->row()->twitter_chat_updates;

        if ($twitteron)
        {
            $text = $this->session->userdata('chat_name').': '.$message;
            $this->load->model('common/common_model');
            $this->common_model->twitter_post($text);
        }

    }

    function get_chat_key()
    {
        return $this->db->select('chat_key')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->chat_key;
    }

    function set_last_read_key($key = '')
    {
        $key = $this->db->select('chat_key')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->chat_key;
        $settingid = $this->db->select('id')->from('owner_setting')->where('owner_id',$this->ownerid)
            ->where('league_id',$this->leagueid)->get()->row();
        if(count($settingid) == 0)
        {
            $data = array('owner_id' => $this->ownerid,
                          'league_id' => $this->leagueid,
                          'chat_read' => $key);
            $this->db->insert('owner_setting',$data);
        }
        else
        {
            $this->db->where('id',$settingid->id);
            $this->db->update('owner_setting', array('chat_read' => $key));
        }
    }

    function get_unread_count()
    {
        $last_read = $this->get_last_read_key();
        return $this->db->select('count(id) as num')->from('chat_message')
            ->where('league_id',$this->leagueid)->where('id >',$last_read)->get()->row()->num;
    }

    function get_last_read_key()
    {
        $row = $this->db->select('chat_read')->from('owner_setting')->where('owner_id',$this->ownerid)
            ->where('league_id',$this->leagueid)->get()->row();
        if (count($row) == 0)
            return 0;
        else
            return $row->chat_read;
    }

    function get_messages($key = '')
    {
        $firstnames = $this->get_firstnames();

        $this->db->select('message_text, unix_timestamp(message_date) as date, owner_id')
            ->select('owner.first_name, owner.last_name, owner.first_name as chat_name')
            ->from('chat_message')
            ->where('league_id',$this->leagueid);
        if($key != '')
            $this->db->where('chat_message.id >',$key);
        $this->db->join('owner','chat_message.owner_id = owner.id')
            ->order_by('message_date','desc');
        if($key == '')
            $this->db->limit(100);

        $data = $this->db->get()->result();

        foreach($data as $d)
        {
            if($firstnames[strtolower($d->first_name)] > 1)
                $d->chat_name = $d->first_name.' '.$d->last_name[0];
        }
        return array_reverse($data);
    }

    function get_firstnames()
    {
        $owners = $this->db->select('owner.first_name')->from('team')
            ->join('owner','owner.id = team.owner_id')
            ->where('team.league_id',$this->leagueid)->get()->result();
        $firstnames = array();
        foreach($owners as $o)
        {
            if(array_key_exists(strtolower($o->first_name), $firstnames))
                $firstnames[strtolower($o->first_name)]++;
            else
                $firstnames[strtolower($o->first_name)] = 1;
        }
        return $firstnames;
    }
}
?>
