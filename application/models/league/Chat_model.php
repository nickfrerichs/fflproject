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

        $this->set_last_read_key($chat_key);

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
        // $settingid = $this->db->select('id')->from('team')->where('owner_id',$this->ownerid)
        //     ->where('league_id',$this->leagueid)->get()->row();
        // if(count($settingid) == 0)
        // {
        //     $data = array('owner_id' => $this->ownerid,
        //                   'league_id' => $this->leagueid,
        //                   'chat_read' => $key);
        //     $this->db->insert('owner_setting',$data);
        // }
        // else
        // {
        $this->db->where('id',$this->teamid);
        $this->db->update('team', array('chat_read' => $key));
     //   }
    }

    function get_unread_count()
    {
        $last_read = $this->get_last_read_key();
        return $this->db->select('count(id) as num')->from('chat_message')
            ->where('league_id',$this->leagueid)->where('id >',$last_read)->get()->row()->num;
    }

    function update_last_check_in()
    {
        $check_in_time = time();
        $data = array('last_check_in' => t_mysql($check_in_time));
        $this->db->where('owner_id',$this->ownerid)->where('league_id',$this->leagueid);
        $this->db->update('owner_setting',$data);
        return $check_in_time;
    }

    function get_last_read_key()
    {
        $row = $this->db->select('chat_read')->from('team')->where('id',$this->teamid)
            ->where('league_id',$this->leagueid)->get()->row();
        if (count($row) == 0)
            return 0;
        else
            return $row->chat_read;
    }

    function get_messages($key = '', $limit=100, $show_owner=True)
    {
        $firstnames = $this->get_firstnames();

        $this->db->select('chat_message.id as message_id, message_text, unix_timestamp(message_date) as date, owner_id')
            ->select('owner.first_name, owner.last_name, owner.first_name as chat_name')
            ->select('IF(owner_id ='.$this->ownerid.',1,0) as is_me')
            ->from('chat_message')
            ->where('league_id',$this->leagueid);
        if($show_owner == False)
            $this->db->where('chat_message.owner_id !=',$this->ownerid);
        if($key != '')
            $this->db->where('chat_message.id >',$key);
        $this->db->join('owner','chat_message.owner_id = owner.id')
            ->order_by('message_date','desc');
        if($key == '')
            $this->db->limit($limit);

        $data = $this->db->get()->result();

        foreach($data as $d)
        {
            if($firstnames[strtolower($d->first_name)] > 1)
                $d->chat_name = $d->first_name.' '.$d->last_name[0];
            $d->message_text = auto_link($d->message_text,'url',True);

            if (date("n/j/Y",$d->date) == date("n/j/Y",time()))
                {$d->date = date("g:i a",$d->date);}
            else
                {$d->date = date("n/j g:i a",$d->date);}
            $d->html = '<tr><td class="chat-row"><b>'.$d->chat_name.'</b> <i>'.$d->date.'</i><br>'.$d->message_text.'</td></tr>';
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

    function whos_online()
    {
        # Get a list of all last check in times
        $firstnames = $this->get_firstnames();
        $data = array();
        $online = $this->db->select('owner.id, owner.last_name, owner.first_name as chat_name, owner.first_name')
            ->select('IFNULL(league_admin.league_admin_id,0) as league_admin')
            ->from('owner_setting')
            ->join('owner','owner.id = owner_setting.owner_id')
            ->join('user_accounts','user_accounts.id=owner.user_accounts_id')
            ->join('league_admin','league_admin_id = user_accounts.id and league_admin.league_id='.$this->leagueid,'left')
            ->where('owner_setting.league_id',$this->leagueid)
            ->where('owner_setting.last_check_in>',t_mysql(time()-($this->session->userdata('live_element_refresh_time')*2)))
            ->order_by('first_name','asc')
            ->order_by('last_name','asc')
            ->get()->result();

        foreach($online as $d)
        {
            $add = array();
            if ($d->league_admin > 0)
                $add['a'] = 1;
            if($firstnames[strtolower($d->first_name)] > 1)
                $add['n'] = $d->first_name.' '.$d->last_name[0];
            else
                $add['n'] = $d->first_name;
            $data[] = $add;
        }
        return $data;
    }

}
?>
