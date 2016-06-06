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

    function join_code_required($maskid)
    {
        $row = $this->db->select('join_password')->from('league')
            ->join('league_settings','league_settings.league_id = league.id')
            ->where('league.mask_id',$maskid)->get()->row();

        if (count($row) > 0 && $row->join_password != "")
            return true;
        return false;
    }
}
?>
