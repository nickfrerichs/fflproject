<?php

class End_season_model extends MY_Model
{
	function get_real_year()
	{
		$this->db->select('max(year) as y')->from('nfl_schedule');
		if($this->session->userdata('week_type'))
			$this->db->where('gt',$this->session->userdata('week_type'));
		return $this->db->get()->row()->y;
	}

	function is_finished()
	{
		// Could add another check to see if we've reached the end of the fantasy league schedule.
		// Or you could check if the NFL post season has started.
		if ($this->get_real_year() > $this->session->userdata('current_year'))
			return True;
		return False;

	}

	function clear_rosters($year)
	{
		// Clear rosters except for keepers
		$sql = 'delete roster from roster left join team_keeper on team_keeper.team_id = roster.team_id '.
			   'and team_keeper.player_id = roster.player_id and team_keeper.year='.$year.' '.
			   'where roster.league_id = '.$this->leagueid.' and team_keeper.id IS NULL';
		$this->db->query($sql);
	}

	function copy_keepers($current_season,$next_season)
	{
		$result = $this->db->select('team_id, player_id')->from('team_keeper')->where('league_id',$this->leagueid)
			->where('year',$current_season)->get()->result();
		$data = array();
		foreach($result as $k)
		{
			$data[] = array(
				'team_id' => $k->team_id,
				'player_id' => $k->player_id,
				'league_id' => $this->leagueid,
				'year' => $next_season
			);
		}
		if (count($data)>0)
			$this->db->insert_batch('team_keeper',$data);

	}

	function enable_offseason()
	{
		$this->db->where('league_id',$this->leagueid);
		$this->db->update('league_settings',array('offseason' => 1));
	}

	function set_season_year($year)
	{
		$this->db->where('id', $this->leagueid);
		$this->db->update('league',array('season_year' => $year));
	}

	function clear_draft_order($year)
	{
		// Clear draft_order and draft_watch
		$this->db->where('league_id', $this->leagueid)->where('year',$year)->delete('draft_order');
		$this->db->where('league_id', $this->leagueid)->delete('draft_watch');

		// Update league_settings
		$this->db->where('league_id',$this->leagueid);
		$this->db->update('league_settings',array('draft_end' => $year-1,
												  'draft_start_time' => '0000-00-00 00:00:00',
												  'scheduled_draft_start_time' => '0000-00-00 00:00:00',
												  'draft_update_key' => 0,
												  'draft_team_id' => 0,
												  'draft_pick_id' => 0,
												  'draft_paused' => 0));
	}

	function clear_player_transactions($year)
	{
		// waiver_wire_log
		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('waiver_wire_log');

		// trade_pick
		$sql = 'delete trade_pick from trade_pick join trade on trade.id = trade_pick.trade_id '.
			   'where trade.league_id = '.$this->leagueid.' and trade.year = '.$year;
		$this->db->query($sql);

		// trade_player
		$sql = 'delete trade_player from trade_player join trade on trade.id = trade_player.trade_id '.
			   'where trade.league_id = '.$this->leagueid.' and trade.year = '.$year;
		$this->db->query($sql);

		// trade
		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('trade');
	}

	function clear_schedule($year)
	{
		// schedule_result
		$sql = 'delete schedule_result from schedule_result join schedule on schedule.id = schedule_result.schedule_id '.
			   'where schedule.league_id = '.$this->leagueid.' and schedule.year = '.$year;
		$this->db->query($sql);

		// schedule
		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('schedule');

		// money_list
		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('money_list');
	}

	function clear_scores($year)
	{
		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('starter');

		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('fantasy_statistic_week');

		$this->db->where('league_id',$this->leagueid)->where('year',$year)->delete('fantasy_statistic');
	}
}
?>
