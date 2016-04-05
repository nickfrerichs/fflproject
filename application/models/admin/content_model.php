<?php

class Content_model extends MY_Model
{
    function get_page_data($text_id)
    {
        $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
            ->where('text_id',$text_id)->where('year',$this->current_year)->get()->row();
        return $row;
    }

    function save_content($content_id, $text_id='',$content='',$title='', $date_posted = 0)
    {
        if ($title =='')
            $title = ucfirst($text_id);

        // Create new
        if($content_id == 0 && $text_id != '')
        {
            if ($date_posted == 0)
                $date_posted = time();
            $data = array('data' => $content,
                          'text_id' => $text_id,
                          'title' => $title,
                          'league_id' => $this->leagueid,
                          'year' => $this->current_year,
                          'date_posted' => t_mysql($date_posted),
                          'last_updated' => t_mysql(time()));
            $this->db->insert('content',$data);
        }
        else
        {
            $data = array('data' => $content, 'last_updated' => t_mysql(time()));
            if ($date_posted != 0)
                $data['date_posted'] = t_mysql($date_posted);
            $this->db->where('id',$content_id);
            $this->db->update('content',$data);
        }
    }

    function ok_to_save($text_id)
    {
        if ($text_id == "news")
            return true;


        $predefined = array('playoffs');
        if(in_array($text_id,$predefined))
        {
            $num = $this->db->from('content')->where('league_id',$this->leagueid)->where('year',$this->current_year)
                ->where('text_id',$text_id)->count_all_results();
            if ($num == 0)
                return true;
        }
        return false;
    }

}

?>
