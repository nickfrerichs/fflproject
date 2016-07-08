<?php

class Rules_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->leagueid = $this->session->userdata('league_id');
    }


    function get_scoring_defs_data()
    {
        $def_year = $this->common_model->scoring_def_year();

        return $this->db->select('scoring_def.per, scoring_def.points, scoring_def.round')
            ->select('nfl_scoring_cat.text_id as cat_text_id, nfl_scoring_cat.short_text as cat_short_text, nfl_scoring_cat.long_text as cat_long_text')
            ->select('nfl_scoring_cat_type.text as cat_type_text')
            ->select('IFNULL(nfl_position.short_text,"All") as pos_text')
            ->from('scoring_def')
            ->join('nfl_scoring_cat','nfl_scoring_cat.id = scoring_def.nfl_scoring_cat_id')
            ->join('nfl_scoring_cat_type','nfl_scoring_cat_type.id = nfl_scoring_cat.type')
            ->join('nfl_position','nfl_position.id = scoring_def.nfl_position_id','left')
            ->where('league_id',$this->leagueid)->where('year',$def_year)
            ->get()->result();
    }

    function get_league_positions_data()
    {
        $pos_year = $this->common_model->league_position_year();

        return $this->db->select('position.nfl_position_id_list, position.max_roster, position.min_roster')
            ->select('position.max_start, position.min_start, position.text_id, position.long_text')
            ->from('position')
            ->where('league_id',$this->leagueid)
            ->where('year',$pos_year)
            ->get()->result();
    }

    function get_nfl_pos_lookup_array()
    {
        $data = array();
        $result = $this->db->select('id, text_id, short_text')->from('nfl_position')->get()->result();
        foreach($result as $row)
        {
            $data[$row->id] = $row->short_text;
        }
        return $data;
    }

    function get_rules_content()
    {

        $row = $this->db->select('data')->from('content')->where('text_id','rules')->where('league_id',$this->leagueid)
            ->get()->row();
        if (count($row) > 0)
            return $row->data;
        return False;
    }

}
?>
