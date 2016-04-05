<?php

class Scoring_model extends MY_Model
{
    function get_scoring_cats_data($position_id = 0)
    {
        // Get array of existing defined_values to be omitted from next query
        $v = array();

        $values = $this->db->select('id, nfl_position_id, nfl_scoring_cat_id')->from('scoring_def')
                ->where('league_id', $this->leagueid)
                ->where('nfl_position_id', $position_id)
                ->get()->result();

        foreach ($values as $value)
            $v[] = $value->nfl_scoring_cat_id;

        // Returns all not currently assigned statistic_categories for this position
        $this->db->select('nfl_scoring_cat.id, nfl_scoring_cat.text_id, '
                . 'nfl_scoring_cat.long_text, nfl_scoring_cat_type.text as type_text')
                ->from('nfl_scoring_cat')->join('nfl_scoring_cat_type', 'nfl_scoring_cat.type = nfl_scoring_cat_type.id');
        if(count($v) > 0)
            $this->db->where_not_in('nfl_scoring_cat.id',$v);
        $categories = $this->db->order_by('type', 'asc')->order_by('id','asc')->get()->result();

        return $categories;

    }

    function get_values_data()
    {
        $data = $this->db->select('scoring_def.id, scoring_def.nfl_scoring_cat_id, scoring_def.per, '
                . 'scoring_def.points, scoring_def.league_id, scoring_def.round')
                ->select('nfl_scoring_cat.text_id, nfl_scoring_cat.long_text, nfl_scoring_cat.type')
                ->select('nfl_position.text_id as pos_text')
                ->select('nfl_scoring_cat_type.text as type_text')
                ->from('scoring_def')
                ->join('nfl_scoring_cat', 'nfl_scoring_cat.id = scoring_def.nfl_scoring_cat_id','left')
                ->join('nfl_position', 'nfl_position.id = scoring_def.nfl_position_id', 'left')
                ->join('nfl_scoring_cat_type', 'nfl_scoring_cat_type.id = nfl_scoring_cat.type')
                ->where('scoring_def.league_id', $this->leagueid)
                ->get();
        return $data->result();
    }

    function get_nfl_positions_data()
    {
        $data = $this->db->select('position.nfl_position_id_list')
                ->from('position')
                ->where('position.league_id', $this->leagueid)
                ->get();
        $pos_list = array();
        foreach ($data->result() as $posrow)
            $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));

            $this->db->select('nfl_position.id, nfl_position.text_id, nfl_position.long_text')
                    ->from('nfl_position');
            if (count($pos_list) > 0)
                $this->db->where_in('id', $pos_list);
            else
                $this->db->where_in('id', array(-1));
            $this->db->order_by('type','asc')
                    ->order_by('nfl_position.text_id', 'asc');
            $data = $this->db->get();
            return $data->result();
    }

    function stat_value_exists($position_id,$stat_id)
    {
        $num = $this->db->where('nfl_position_id', $position_id)
                ->where('nfl_scoring_cat_id', $stat_id)
                ->where('league_id',$this->leagueid)
                ->count_all_results('scoring_def');
        if ($num > 0)
            return true;
        return false;
    }

    function add_stat_value_entry($stat_id, $position_id)
    {
        $data = array('nfl_scoring_cat_id' => $stat_id,
                    'per' => 0,
                    'points' => 0,
                    'league_id' => $this->leagueid,
                    'nfl_position_id' => $position_id);
        $this->db->insert('scoring_def', $data);
    }

    function save_value($id, $points, $per, $round)
    {
        $this->db->where('id', $id);
        $this->db->update('scoring_def',array('points' => $points, 'per' => $per, 'round' => $round));
    }

    function delete($id)
    {
        $this->db->where('id', $id)
        ->where('league_id', $this->leagueid)
        ->delete('scoring_def');
    }
}
