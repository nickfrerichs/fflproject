<?php

class News_model extends MY_Model{

    function __construct(){
        parent::__construct();
    }


    function get_news_data()
    {
    	return $this->db->select('id,data,title,UNIX_TIMESTAMP(date_posted) as date_posted,UNIX_TIMESTAMP(last_updated) as date_posted')
            ->from('content')->where('league_id',$this->leagueid)->where('text_id',"news")->order_by('date_posted','desc')->limit(3)
    		->get()->result();
    }

}

?>
