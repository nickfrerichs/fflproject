<?php
class Common_noauth_model extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    function league_name_from_mask_id($maskid)
    {
        return $this->db->select('league_name')->from('league')->where('mask_id',$maskid)->get()->row()->league_name;
    }

    function get_site_name()
    {
        return $this->db->select('name')->from('site_settings')->get()->row()->name;
    }
}
?>