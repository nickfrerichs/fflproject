<?php

class Content_model extends MY_Model
{
    function get_page_data($text_id,$id = null)
    {
        if ($text_id)
        {
            $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
                ->where('text_id',$text_id)->where('year',$this->current_year)->get()->row();
        }
        elseif($id)
        {
            $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
                ->where('id',$id)->get()->row();
        }
        return $row;
    }

    function get_postseason_data($year=0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
                ->where('text_id','playoffs')->where('year',$year)->get()->row();

        return $row;
        
    }

    function create_postseason_content($year=0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $row = $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
                ->where('text_id','playoffs')->where('year',$year)->get()->row();
        if (count($row) == 0)
        {
            $data = array('data' => '',
                        'title' => '',
                        'year' => $year,
                        'date_posted' => '0000-00-00 00:00:00',
                        'last_updated' => t_mysql(time()),
                        'league_id' => $this->leagueid,
                        'text_id' => 'playoffs');
            $this->db->insert('content',$data);
        }
    }

    function get_news_data()
    {
        return $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
            ->where('text_id','news')->order_by('date_posted','desc')->get()->result();
    }

    function get_news_page_data($id)
    {
        return $this->db->select('*')->from('content')->where('league_id',$this->leagueid)
            ->where('id',$id)->get()->row();
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
            $return_id = $this->db->insert_id();
        }
        else
        {
            $data = array('data' => $content, 'last_updated' => t_mysql(time()), 'title' => $title);
            if ($date_posted != 0)
                $data['date_posted'] = t_mysql($date_posted);
            $this->db->where('id',$content_id);
            $this->db->update('content',$data);
            $return_id = $content_id;
        }
        return $return_id;
    }

    function delete_content_item($id)
    {
        $this->db->where('league_id',$this->leagueid)->where('id',$id)->delete('content');
    }

    function ok_to_save($text_id)
    {
        if ($text_id == "news")
            return true;


        $predefined = array('playoffs','rules');
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
