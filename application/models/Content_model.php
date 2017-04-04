<?php

class Content_model extends MY_Model{

    function get_content($text_id, $year=0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
            ->where('text_id',$text_id)->where('year',$year)->get()->row();
        return $row;
    }

}
?>
