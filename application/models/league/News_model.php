<?php

class News_model extends MY_Model{

    function __construct(){
        parent::__construct();
    }


    function get_news_data($limit = 100000, $start=0)
    {
        $return = array('news' => array(), 'total' => 0);
        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
    	$return['news'] = $this->db->select('id,data,title,UNIX_TIMESTAMP(date_posted) as date_posted,UNIX_TIMESTAMP(last_updated) as date_posted')
            ->from('content')->where('league_id',$this->leagueid)->where('text_id',"news")->where('year',$this->current_year)->order_by('date_posted','desc')->limit($limit,$start)
    		->get()->result();

        $return['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;

        return $return;
    }

}

?>
