<?php

class Content_model extends MY_Model{

    function get_content($text_id)
    {
        $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
            ->where('text_id',$text_id)->where('year',$this->current_year)->get()->row();
        return $row;
    }

}
?>
