<?php

class End_season_model extends MY_Model
{
	function get_real_year()
	{
		return $this->db->select('max(year) as y')->from('nfl_schedule')->where('gt',$this->session->userdata('week_type'))->get()->row()->y;
	}

	function is_finished()
	{
		// Could add another check to see if we've reached the end of the fantasy league schedule.
		// Or you could check if the NFL post season has started.
		if ($this->get_real_year() > $this->session->userdata('current_year'))
			return True;
		return False;

	}
}
?>