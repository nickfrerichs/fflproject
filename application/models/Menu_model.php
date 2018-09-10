<?php

class Menu_model extends CI_Model
{

    function __construct(){
        parent::__construct();
	}

	function get_menu_items_data($admin = false)
	{
        if ($this->session->userdata('league_id') <= 0 || $this->session->userdata('league_id') == "")
            $noleague = True;
        else
            $noleague = False;

		$this->db->select('menu_item.text as item_text, menu_bar.text as menu_text, menu_item.url, menu_bar.url as bar_url')->
		from('menu_bar')->join('menu_item', 'menu_bar.id = menu_item.menu_bar_id','left');

        if ($admin == false)
            $this->db->where('menu_bar.admin',0)->where('menu_bar.super_admin',0);
        elseif ($admin && $this->ion_auth->is_admin())
        {
            $this->db->where('(menu_bar.super_admin = 1 or menu_bar.admin = 1)',null,false);
        }
        elseif($this->ion_auth->is_admin())
            $this->db->where('menu_bar.super_admin',1);
        elseif($admin)
            $this->db->where('menu_bar.admin',1);
        else
            $this->db->where('menu_bar.admin', 0);
        // Not a member of a league, only show noleague items.. or admin view and not league admin
        if ($noleague || ($admin && !$this->session->userdata('is_league_admin')))
            $this->db->where('menu_item.show_noleague',1);
        $this->db->where('menu_bar.hide',0)->where('menu_item.hide',0)
              ->order_by('menu_bar.super_admin','desc')
		      ->order_by('menu_bar.order', 'asc')
              ->order_by('menu_item.order','asc');
		$data = $this->db->get()->result();
		$data_array = array();
		foreach ($data as $row)
		{
            $menu_text = $this->fill_vars($row->menu_text);
            if ($row->item_text == "")
            {
                $data_array[$menu_text] = site_url('/'.$row->bar_url);
                continue;
            }
            $item_text = $this->fill_vars($row->item_text);
			$data_array[$menu_text][$item_text] = site_url('/'.$row->url);
        }
        
        // If user is in multiple leagues, add that to menu
        if (count($this->session->userdata('leagues'))>1)
        {
            $menu_text = "League";
            $data_array[$menu_text]['_divider'] = "";
            $data_array[$menu_text]['Switch Leagues'] = "javascript:fflp_change_league();";
            // foreach($this->session->userdata('leagues') as $l_id => $l_name)
            // {
            //     $data_array[$menu_text][$l_name] = site_url();
            // }
        }

		return $data_array;
	}

    function fill_vars($text)
    {
        $text = str_ireplace("*season_year*",$this->session->userdata('current_year'),$text);
        return $text;
    }

}
