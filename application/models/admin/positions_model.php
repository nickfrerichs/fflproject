<?php

class Positions_model extends MY_Model
{
    function get_league_positions_data()
    {
        $position_data = $this->db->select('position.id, position.text_id, position.long_text, position.nfl_position_id_list')
                ->select('position.max_roster, position.min_roster, position.max_start, position.min_start')
                ->from('position')
                ->where('league_id', $this->leagueid)
                ->get();
        
        return $position_data->result();
        //$nfl_position_data = $this->db->select('')
       
        //return $data->result();
    }
    
    // MOVE TO USER_MODEL?
    function get_league_position_data($posid)
    {
        $data = $this->db->select('position.id, position.text_id, position.long_text, position.nfl_position_id_list')
                ->select('position.max_roster, position.min_roster, position.max_start, position.min_start')
                ->from('position')
                ->where('league_id', $this->leagueid)
                ->where('id', $posid)
                ->get();
        return $data->row();
    }
    
    function get_nfl_positions_array()
    {
        $data = $this->db->select('nfl_position.id, nfl_position.text_id')
                ->from('nfl_position')
                ->get();
        $data_array = array();
        foreach ($data->result() as $result)
        {
            $data_array[$result->id] = $result->text_id;
        }
        return $data_array;
    }
    
    function get_nfl_positions_data($include_all_pos = false)
    {
        if (!$include_all_pos)
            $pos_list = $this->get_league_nfl_position_id_array();
        $this->db->select('nfl_position.id, nfl_position.text_id, nfl_position.long_text')
                ->from('nfl_position');
        if (!$include_all_pos)
                $this->db->where_in('id', $pos_list);
        $this->db->order_by('type','asc')
                ->order_by('nfl_position.text_id', 'asc');
        $data = $this->db->get();
        return $data->result();
    }
    
    function save_position($values)
    {        
        $data = array('text_id' => $values['text_id'],
            'long_text' => $values['long_text'],
            'league_id' => $this->leagueid,
            'nfl_position_id_list' => $values['league_positions'],
            'max_roster' => $values['max_roster'],
            'min_roster' => $values['min_roster'],
            'max_start' => $values['max_start'],
            'min_start' => $values['min_start']);
        
        if(isset($values['id']))
        {
            $this->db->where('id', $values['id']);
            $this->db->update('position',$data);
        }
        else
          $this->db->insert('position',$data);
        
        //$id = $this->db->insert_id();
        
        /*
        foreach(explode(',',$values['league_positions']) as $nfl_pos_id)
        {
            $data = array('nfl_position_id' => $nfl_pos_id, 'league_position_id' => $id);
            $this->db->insert('position_lookup',$data);
        }
         * 
         */
    }
    
    function position_exists($text_id)
    {
        $data = $this->db->select('id')
                ->from('position')
                ->where('text_id', $text_id)
                ->where('league_id', $this->leagueid)
                ->get();
        if ($data->num_rows != 0)
            return true;
        return false;
    }
    
    function delete_position($posid)
    {
        $this->db->where('id', $posid)
                ->where('league_id', $this->leagueid)
                ->delete('position');
    }
    
    function get_league_nfl_position_id_array()
    {
        $data = $this->db->select('position.nfl_position_id_list')
                ->from('position')
                ->where('position.league_id', $this->leagueid)
                ->get();
        $pos_list = array();
       
        foreach ($data->result() as $posrow)
            $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));
        return $pos_list;
    }
}