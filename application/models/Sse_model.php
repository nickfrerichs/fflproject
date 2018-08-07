<?php

class Sse_model extends MY_Model{

//    protected $leagueid;
//    protected $ownerid;
    function __construct(){
        parent::__construct();
        $this->leagueid = $this->session->userdata('league_id');
        $this->ownerid = $this->session->userdata('owner_id');
    }

    function get_sse_settings()
    {
        return $this->db->select('sse_chat, sse_draft, sse_live_scores')
            ->from('owner_setting')
            ->where('owner_id',$this->ownerid)->where('league_id',$this->leagueid)
            ->get()->row();
    }

    function reset_sse_settings()
    {
        $data = array('sse_chat' => 0,
                      'sse_draft' => 0,
                      'sse_live_scores' =>0);

        $this->db->where('league_id',$this->leagueid)->where('owner_id',$this->ownerid);
        $this->db->update('owner_setting',$data);
    }

    function turn_on($function)
    {
        $data = array($function => 1);
        $this->db->where('league_id',$this->leagueid)->where('owner_id',$this->ownerid);
        $this->db->update('owner_setting',$data);
    }

    function turn_off($function)
    {
        $data = array($function => 0);
        $this->db->where('league_id',$this->leagueid)->where('owner_id',$this->ownerid);
        $this->db->update('owner_setting',$data);
    }

    function live_scores_key()
    {
        return $this->db->select('live_scores_key')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->live_scores_key;
    }

    function keys()
    {
        return $this->db->select('live_scores_key, draft_update_key, draft_paused, chat_key, draft_end')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row();
    }

}
?>
