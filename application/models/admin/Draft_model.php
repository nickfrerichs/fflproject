<?php

class Draft_model extends MY_Model
{

    function get_league_teams_data()
    {
        return $this->db->select('team.id as team_id, team_name')
            ->from('team')->where('league_id',$this->leagueid)->where('active',1)
            ->get()->result();
    }

    function create_draft_order($order, $rounds, $reverse, $trades)
    {
    	$this->db->delete('draft_order',array('year' => $this->current_year));
        $settings = $this->get_draft_settings();
        if ($settings->trade_draft_picks && $this->future_year_exists() && $trades)
        {
            $future_picks = $this->db->select('original_owner_team_id, pick_owner_team_id, round')->from('draft_future')
                ->where('league_id',$this->leagueid)->where('year',$this->current_year)->get()->result();
            $lookup = array();
            foreach($future_picks as $f)
                $lookup[$f->round][$f->original_owner_team_id] = $f->pick_owner_team_id;

        }

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
                $set_team_id = $team_id;
                if (isset($lookup) && count($lookup) > 0 && isset($lookup[$i][$team_id]))
                    $set_team_id = $lookup[$i][$team_id];
    			$batch[] = array('league_id' => $this->leagueid,
    						   'team_id' => $set_team_id,
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
        
        // Set draft_end to 0
        $this->db->where('league_id',$this->leagueid)->update('league_settings',array('draft_end'=> 0));
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
    	return $this->db->select('draft_order.id, round, pick, overall_pick')
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
        return $this->db->select('draft_time_limit, draft_start_time, scheduled_draft_start_time, trade_draft_picks')->from('league_settings')
            ->where('league_id',$this->leagueid)->get()->row();
    }

    function reset_auto_start()
    {
        # Temporarily disabling autostart
        $this->db->where('league_id',$this->leagueid)->update('league_settings',array('draft_start_time' => 0));
        return;

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

        // Temporarily disabling autostart
        $this->db->where('league_id',$this->leagueid)->update('league_settings',$data);
        return 0;

        if ($s->draft_start_time == 0)
        {
            $data['draft_start_time'] = $s->scheduled_draft_start_time;
        }
        $this->db->where('league_id',$this->leagueid)->update('league_settings',$data);

        if ($data['draft_start_time'] == 0)
            return 0;
        return 1;
    }

    function get_future_pick_years_array()
    {
        $result = array();
        $data = $this->db->select('distinct(year) as year')->from('draft_future')->where('league_id',$this->leagueid)->where('year>',$this->current_year)
            ->order_by('year','asc')->get()->result();

        foreach($data as $d)
        {
            $result[] = $d->year;
        }
        return $result;
    }

    function get_default_num_rounds()
    {
        $recent_year = $this->db->select('max(year) as year')->from('draft_future')->where('league_id',$this->leagueid)->get()->row()->year;
        if ($recent_year != "")
        {
            return $this->db->select('max(round) as round')->from('draft_future')->where('league_id',$this->leagueid)
                ->where('year',$recent_year)->get()->row()->round;
        }
        else
        {
            return 10;
        }
    }

    function create_future_year($year, $rounds)
    {
        $teams = $this->get_league_teams_data();
        $batch = array();
        foreach(range(1,$rounds) as $i)
        {
            foreach($teams as $t)
            {
                $data = array('league_id' => $this->leagueid,
                              'original_owner_team_id' => $t->team_id,
                              'pick_owner_team_id' => $t->team_id,
                              'round' => $i,
                              'year' => $year);
                $batch[] = $data;
            }
        }
        $this->db->insert_batch('draft_future',$batch);
    }

    function future_year_exists($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $count = $this->db->from('draft_future')->where('league_id',$this->leagueid)->where('year',$year)->count_all_results();
        if($count == 0)
            return False;
        return True;
    }

    function get_future_picks_data($year)
    {
        return $this->db->select('org_team.team_name as org_team_name, round')
            ->select('pick_team.team_name as pick_team_name')
            ->from('draft_future')
            ->join('team as org_team','original_owner_team_id = org_team.id')
            ->join('team as pick_team','pick_owner_team_id = pick_team.id')
            ->where('draft_future.league_id',$this->leagueid)
            ->where('draft_future.year',$year)
            ->order_by('round','asc')->get()->result();
    }

    function delete_draft_pick($id)
    {
        $year = $this->db->select('year')->from('draft_order')->where('id',$id)->where('league_id',$this->leagueid)
            ->get()->row()->year;

        $this->db->where('id',$id)->where('league_id',$this->leagueid)
            ->delete('draft_order');


        // Reorder the picks
        $picks = $this->db->select('id,round,pick,overall_pick,actual_pick')->from('draft_order')
            ->where('league_id',$this->leagueid)->where('year',$year)
            ->order_by('round','asc')->order_by('pick','asc')->get()->result();

        $update_batch = array();
        $rnd = 0;
        $pick = 1;
        $overall = 1;
        foreach($picks as $p)
        {
            if ($rnd != $p->round)
            {
                $rnd = $p->round;
                $pick = 1;
            }
            $update_batch[] = array('id' => $p->id,
                                    'round' => $rnd,
                                    'pick' => $pick,
                                    'overall_pick' => $overall,
                                    'actual_pick' => $overall);
            $overall++;
            $pick++;

        }

        $this->db->update_batch('draft_order',$update_batch,'id');

    }

}
