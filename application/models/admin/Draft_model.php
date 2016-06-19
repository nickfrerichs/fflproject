<?php

class Draft_model extends MY_Model
{

    function get_league_teams_data()
    {
        return $this->db->select('team.id as team_id, team_name')
            ->from('team')->where('league_id',$this->leagueid)->where('active',1)
            ->get()->result();
    }

    function create_draft_order($order, $rounds, $reverse)
    {
    	$this->db->delete('draft_order',array('year' => $this->current_year));

        // Remove any dropdowns that were left blank
        foreach($order as $key => $val)
        {
            if ($val == 0)
                unset($order[$key]);
        }

    	$overall = 1;
    	$batch = array();
    	for($i=1; $i<=$rounds; $i++)
    	{
    		foreach ($order as $pick => $team_id)
    		{
                if ($team_id == 0)
                    continue;
    			$batch[] = array('league_id' => $this->leagueid,
    						   'team_id' => $team_id,
    						   'round' => $i,
    						   'pick' => $pick+1,
    						   'overall_pick' => $overall,
                               'actual_pick' => $overall,
    						   'year' => $this->current_year);
    			$overall++;
    		}
    		if ($reverse)
    			$order = array_reverse($order);
    	}

    	$this->db->insert_batch('draft_order', $batch);
    }

    function get_draft_order_count()
    {
    	return $this->db->select('count(id) as num')->from('draft_order')
    		->where('league_id',$this->leagueid)->where('year',$this->current_year)
    		->get()->row()->num;
    }

    function get_num_rounds()
    {
    	return $this->db->select('count(distinct(round)) as num')->from('draft_order')
    		->where('league_id',$this->leagueid)->where('year',$this->current_year)
    		->get()->row()->num;
    }

    function get_draft_round_data($round)
    {
    	return $this->db->select('round, pick, overall_pick')
    		->select('team.team_name, owner.first_name, owner.last_name')
    		->select('player.first_name as p_first_name, player.last_name as p_last_name, player.id as p_id')
    		->from('draft_order')
    		->join('team', 'team.id = draft_order.team_id')
    		->join('player','player.id = draft_order.player_id','left')
    		->join('owner', 'owner.id = team.owner_id')
    		->where('draft_order.league_id',$this->leagueid)
    		->where('draft_order.year',$this->current_year)
    		->where('draft_order.round',$round)
    		->order_by('draft_order.overall_pick','asc')
    		->get()->result();
    }

    function save_draft_options($draft_time, $limit)
    {
        $data = array();
        if ($draft_time != false)
        {
            $data['scheduled_draft_start_time'] = $draft_time;
        }

        if ($limit != false)
            $data['draft_time_limit'] = $limit;

        $data['draft_paused'] = 0;
        $data['draft_update_key'] = 0;
        $data['draft_team_id'] = 0;
        $data['draft_pick_id'] = 0;

        if($this->db->select('id')->from('league_settings')->where('league_id',$this->leagueid)->get()->num_rows() > 0)
        {
            $this->db->where('league_id',$this->leagueid);
            $this->db->update('league_settings',$data);

        }
        else
        {
            $data['league_id'] = $this->leagueid;
            $this->db->insert('league_settings', $data());
        }

        if ($draft_time != false)
            $this->reset_auto_start();
    }

    function set_draft_deadlines()
    {
        $settings = $this->db->select('UNIX_TIMESTAMP(draft_start_time) as start_time, draft_time_limit')->from('league_settings')
            ->where('league_id',$this->leagueid)->get()->row();

        $picks = $this->db->select('id,deadline')->from('draft_order')
            ->where('league_id',$this->leagueid)->where('player_id',0)
            ->order_by('overall_pick','asc')
            ->get()->result();

        $deadline = $settings->start_time + $settings->draft_time_limit;
        foreach ($picks as $pick)
        {
            $this->db->where('id',$pick->id);
            $this->db->update('draft_order',array('deadline' => $this->t($deadline)));
            $deadline+=$settings->draft_time_limit;
        }

    }

    function t($unixtimestamp)
    {
        return date("Y-m-d H:i:s", $unixtimestamp);
    }


    function get_draft_settings()
    {
        return $this->db->select('draft_time_limit, draft_start_time, scheduled_draft_start_time')->from('league_settings')
            ->where('league_id',$this->leagueid)->get()->row();
    }

    function reset_auto_start()
    {
        $s = $this->get_draft_settings();
        if ($s->draft_start_time > 0)
        {
            $this->db->where('league_id',$this->leagueid)->update('league_settings',array('draft_start_time' => $s->scheduled_draft_start_time));
        }
    }

    function toggle_auto_start()
    {
        $s = $this->get_draft_settings();
        $data = array('draft_start_time' => 0);
        if ($s->draft_start_time == 0)
        {
            $data['draft_start_time'] = $s->scheduled_draft_start_time;
        }
        $this->db->where('league_id',$this->leagueid)->update('league_settings',$data);

        if ($data['draft_start_time'] == 0)
            return 0;
        return 1;
    }




}
