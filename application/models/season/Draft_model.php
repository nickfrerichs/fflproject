<?php

class Draft_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->ownerid = $this->session->userdata('owner_id');
    }

    function get_current_pick_data()
    {
    	$pick_id = $this->db->select('draft_pick_id')->from('league_settings')
    		->where('league_id',$this->leagueid)->get()->row()->draft_pick_id;

    	return $this->db->select('draft_order.id as pick_id,UNIX_TIMESTAMP(deadline) as deadline,actual_pick,round,pick')
    		->select('team.team_name, team.logo, team.id as team_id')
            ->select('owner.first_name as owner')
    		->from('draft_order')
    		->join('team','team.id = draft_order.team_id')
            ->join('owner','owner.id = team.owner_id')
    		->where('draft_order.league_id',$this->leagueid)
    		->where('player_id',0)
    		->where('draft_order.id',$pick_id)
            ->where('year',$this->current_year)
    		->get()->row();
    }

    function draft_paused()
    {
        $val = $this->db->select('draft_paused')->from('league_settings')->where('league_settings.league_id',$this->leagueid)
            ->get()->row()->draft_paused;
        if ($val > 0)
            return $val;
        return False;
    }

    function start()
    {
        $data = array('draft_start_time' => $this->t(time()));
        $this->db->where('league_id',$this->leagueid);
        $this->db->update('league_settings',$data);
    }

    function pause()
    {
        if(!$this->draft_paused())
        {
            $settings = $this->db->select('draft_update_key')->from('league_settings')
                ->where('league_id',$this->leagueid)->get()->row();

            $currenttime = time();

            $timeleft = $settings->draft_update_key - $currenttime;

            $data = array('draft_paused' => $timeleft);
            $this->db->where('league_id',$this->leagueid);
            $this->db->update('league_settings',$data);
        }
    }

    function unpause()
    {
        if($this->draft_paused())
        {
            $settings = $this->db->select('draft_paused,draft_pick_id')->from('league_settings')->where('league_id',$this->leagueid)
                ->get()->row();

            $time_left = $settings->draft_paused;

            // Update draft_pick_id deadline with time()+time_left
            $data = array('deadline' => $this->t(time()+$time_left));

            $this->db->where('id',$settings->draft_pick_id);
            $this->db->update('draft_order',$data);

            // Recalculate order (this updates league_settings)
            $this->adjust_pick_deadlines($settings->draft_pick_id);

            // Update draft_paused setting and set new updateKey
            $data = array('draft_paused' => 0);
            $this->db->where('league_id',$this->leagueid);
            $this->db->update('league_settings',$data);
        }
    }

    function undo_last_pick()
    {
        $last_pick = $this->db->select('id, player_id, team_id')->from('draft_order')
            ->where('league_id',$this->leagueid)->where('player_id !=',0)->where('year',$this->current_year)
            ->order_by('overall_pick','desc')->limit(1)->get()->row();

        if (count($last_pick) == 0)
            return false;

        $settings = $this->db->select('draft_time_limit, draft_update_key')
            ->from('league_settings')->where('league_id',$this->leagueid)->get()->row();

        // Remove player from teams roster
        $this->db->where('team_id',$last_pick->team_id)->where('player_id',$last_pick->player_id);
        $this->db->delete('roster');

        // update draft_order at pick_id, set player_id to 0
        $this->db->where('id',$last_pick->id);
        $this->db->update('draft_order',array('player_id' => 0));

        // update league_settings
        $update_key = (time()+$settings->draft_time_limit);
        //$update_key = $this->adjust_pick_deadlines($last_pick->id);
        $this->db->where('league_id',(int)$this->leagueid);
        $this->db->update('league_settings',array('draft_update_key' => $update_key,
                                                  'draft_team_id' => $last_pick->team_id,
                                                  'draft_pick_id' => $last_pick->id,
                                                  'draft_paused' => $settings->draft_time_limit));

        // adjust pick deadlines
        // picks are recalculated when unpaused


    }

    function is_teams_pick()
    {
        $pick_id = $this->db->select('draft_team_id')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->draft_team_id;

        if ($pick_id == $this->teamid)
            return True;
        return False;
    }

    // This function is for clients to check if the draft has progressed or not.
    function get_update_key()
    {
    	$settings = $this->db->select('draft_update_key, draft_time_limit, draft_pick_id')
            ->select('draft_paused, UNIX_TIMESTAMP(draft_start_time) as draft_start_time')->from('league_settings')
    		->where('league_id',$this->leagueid)->get()->row();

        $return_vals = array('p' => 0, 'k' => $settings->draft_update_key);

        // Draft hasn't started yet, or there is no set time to start.
        if (($settings->draft_start_time == 0) || ($settings->draft_start_time > time()))
            return $return_vals;

        if ($settings->draft_paused)
        {
   
            $return_vals['p'] = 1;
            $return_vals['k'] = $settings->draft_update_key;
            //return -1;
            return $return_vals;
        }

    	$current_time = time();

    	// The current pick deadline has passed, update draft key, return it
    	if ($settings->draft_update_key < $current_time)
    	{
            // This is a new draft, calculate initial picks.
            if ($settings->draft_update_key == 0)
                $this->adjust_pick_deadlines();

            $picks_left = $this->db->select('id')->from('draft_order')->where('league_id',$this->leagueid)
                ->where('player_id',0)
                ->where('year',$this->current_year)
                ->get()->num_rows();

            $current_pick = $this->db->select('id, overall_pick')->from('draft_order')->where('id',$settings->draft_pick_id)->get()->row();
            $current_overall_pick = 0;
            if ($current_pick)
                $current_overall_pick = $current_pick->overall_pick;

            // Get the pick that comes after the current_time, this is important in case more than 1 person missed their
            // pick and no one was viewing the draft page.  Makes us look like we kept track.
    		$row = $this->db->select('id, player_id, UNIX_TIMESTAMP(deadline) as deadline, team_id')->from('draft_order')
    			->where('league_id',$this->leagueid)->where('deadline >', $this->t($current_time))
                ->where('year',$this->current_year)->where('player_id',0)
                ->where('overall_pick>',$current_overall_pick)
                ->order_by('actual_pick','asc')->get()->row();
            // Found a pick with a deadline in the future, this is the "current pick".
            // Adjust the order and put this pick ahead of anyone who missed their pick.
            if (isset($row->deadline))
            {
                $newkey = $row->deadline;
                $this->adjust_pick_deadlines($row->id);
            }
    		elseif($picks_left > 0) // end of draft but still skipped picks left, adjust deadlines, get new key
            {
                $newkey = $this->adjust_pick_deadlines();
                $row = $this->db->select('id, player_id, UNIX_TIMESTAMP(deadline) as deadline, team_id')->from('draft_order')
                    ->where('league_id',$this->leagueid)->where('deadline >', $this->t($current_time))
                    ->where('year',$this->current_year)->where('player_id',0)
                    ->order_by('actual_pick','asc')->get()->row();
            }
            else
            {
                $newkey = 0; // Draft must be over
                $data = array('draft_update_key' => 0, 'draft_team_id' => 0, 'draft_pick_id' => 0, 'draft_end' => $this->current_year);
            }
            if($newkey != 0)
                $data = array('draft_update_key' => $newkey, 'draft_team_id' => $row->team_id, 'draft_pick_id' => $row->id);

    		$this->db->where('league_id',$this->leagueid);
    		$this->db->update('league_settings',$data);

            $return_vals['k'] = $newkey;
    		return $return_vals;

    	}
    	else
    		return $return_vals; // Still on same draft pick, just return the current update key

    }

    function t($unixtimestamp)
    {
        return date("Y-m-d H:i:s", $unixtimestamp);
    }

    function get_available_players_data($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='', $hide_drafted=false)
    {
        $pos_list = $this->common_model->league_nfl_position_id_array();
        if (count($pos_list) < 1)
            $pos_list = array(-1);
        $owned_list = $this->get_owned_players_array();
        $watch_list = $this->get_watch_players_array();

        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id, player.first_name, player.last_name')
                ->select('nfl_position.text_id as position')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('team.team_name')
                ->select('draft_watch.id as watched')
                ->select('IFNULL(draft_player_rank.rank_order,999) as rank',false)
                ->from('player')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
                ->join('draft_player_rank','draft_player_rank.player_id = player.id','left')
                ->join('roster','player.id = roster.player_id and roster.league_id = '.$this->leagueid,'left')
                ->join('team','team.id = roster.team_id and team.league_id = '.$this->leagueid,'left')
                ->join('draft_watch','draft_watch.player_id = player.id and draft_watch.team_id = '.$this->teamid,'left')
                ->where_in('nfl_position_id', $pos_list)
                ->where('player.active', 1);
        if ($hide_drafted == true)
                $this->db->where('team.id',null);
        if ($search != '')
            $this->db->where('(`last_name` like "%'.$search.'%" or `first_name` like "%'.$search.'%")',NULL,FALSE);
        if (($nfl_pos != 0) && (is_numeric($nfl_pos)))
            $this->db->where('nfl_position.id', $nfl_pos);
        $this->db->group_by('player.id')
                ->order_by($order_by[0],$order_by[1])
                ->order_by('player.id','asc')
                ->limit($limit, $start);
        $data = $this->db->get();

        $returndata['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $returndata['result'] = $data->result();
        return $returndata;
    }

    // function get_league_nfl_position_id_array()
    // {
    //     $data = $this->db->select('position.nfl_position_id_list')
    //             ->from('position')
    //             ->where('position.league_id', $this->leagueid)
    //             ->get();
    //     $pos_list = array();
    //
    //     foreach ($data->result() as $posrow)
    //         $pos_list = array_merge($pos_list,explode(',',$posrow->nfl_position_id_list));
    //     return $pos_list;
    // }

    function get_watch_players_array()
    {
        $watch = array();
        $data = $this->db->select('player_id')->from('draft_watch')->where('team_id',$this->teamid)->get()->result();
        foreach ($data as $row)
            $watch[] = $row->player_id;
        return $watch;
    }

    function get_owned_players_array()
    {
        $owned = array();
        $data = $this->db->select('player_id')->from('roster')->where('league_id',$this->leagueid)->get()->result();
        foreach($data as $row)
            $owned[] = $row->player_id;
        return $owned;
    }

    function toggle_watch_player($player_id)
    {
        $id = $this->db->select('id')->from('draft_watch')->where('team_id',$this->teamid)->where('player_id',$player_id)
            ->get()->row();

        if (isset($id->id))
        {
            $this->db->delete('draft_watch', array('id' => $id->id));
            $this->order_watch_list();
        }
        else
        {
            $temp = $this->db->select('order')->from('draft_watch')->where('team_id',$this->teamid)
                ->order_by('order','desc')->get()->row();
            if (count($temp) == 0)
                $count = 1;
            else
                $count = $temp->order+1;
            $this->db->insert('draft_watch', array('league_id' => $this->leagueid,
                                                   'team_id' => $this->teamid,
                                                   'player_id' => $player_id,
                                                   'order' => $count));
        }
    }

    function order_watch_list($team_id = 0, $player_id=0, $direction='')
    {
        if ($team_id == 0)
            $team_id = $this->teamid;
        $rows = $this->db->select('id')->from('draft_watch')->where('team_id',$team_id)
            ->order_by('order','asc')->get()->result();
        $batch_update = array();
        foreach($rows as $count => $p)
        {
            $batch_update[] = array(
                'id'    => $p->id,
                'order' => $count+1);
        }
        if (!empty($batch_update))
                $this->db->update_batch('draft_watch',$batch_update,'id');
    }

    function watch_player_order_change($player_id, $updn)
    {
        $p_order = $this->db->select('order')->from('draft_watch')->where('team_id',$this->teamid)
            ->where('player_id',$player_id)->get()->row()->order;
        $total_watched = $this->db->select('id')->from('draft_watch')->where('team_id',$this->teamid)
            ->count_all_results();
        // Make sure it's not the first or last player moving off the chart.
        if(($p_order == 1 && $updn == 'up') || ($p_order == $total_watched && $updn == 'down'))
            return;
        else
        {
            if($updn == 'up')
            {
                // Move $p_order-1 player down
                $w_id = $this->db->select('id')->from('draft_watch')->where('team_id',$this->teamid)
                    ->where('order <',$p_order)->order_by('order','desc')->get()->row()->id;
                $data = array('order'=> $p_order);
                $this->db->where('team_id',$this->teamid)->where('id',$w_id);
                $this->db->update('draft_watch',$data);

                // Move $p_order player up
                $data = array('order' => $p_order-1);
                $this->db->where('team_id',$this->teamid)->where('player_id',$player_id);
                $this->db->update('draft_watch',$data);
            }

            if($updn == 'down')
            {
                // Move $p_order+1 player up
                $w_id = $this->db->select('id')->from('draft_watch')->where('team_id',$this->teamid)
                    ->where('order >',$p_order)->order_by('order','asc')->get()->row()->id;
                $data = array('order'=> $p_order);
                $this->db->where('team_id',$this->teamid)->where('id',$w_id);
                $this->db->update('draft_watch',$data);

                // Move $p_order player up
                $data = array('order'=> $p_order+1);
                $this->db->where('team_id',$this->teamid)->where('player_id',$player_id);
                $this->db->update('draft_watch',$data);
            }
        }



    }

    function get_watch_list($limit = 100000, $start = 0, $pos = 0)
    {
        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);
        $this->db->select('player.id, player.first_name, player.last_name')
                ->select('nfl_position.text_id as position')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('draft_watch.order')
                ->select('IFNULL(draft_player_rank.rank_order,999) as rank',false)
                ->from('draft_watch')
                ->join('player', 'player.id = draft_watch.player_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
                ->join('draft_player_rank','draft_player_rank.player_id = player.id','left')
                ->where('draft_watch.team_id',$this->teamid);
        if (($pos != 0) && (is_numeric($pos)))
            $this->db->where('nfl_position.id', $pos);
        $data = $this->db->limit($limit, $start)->order_by('draft_watch.order','asc')->get();

        $returndata['count'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;
        $returndata['result'] = $data->result();

        return $returndata;


    }

    function get_myteam()
    {
        return $this->db->select('player.id, player.first_name, player.last_name')
                ->select('nfl_position.text_id as position')
                ->select('IFNULL(nfl_team.club_id,"FA") as club_id',false)
                ->select('draft_order.actual_pick, draft_order.pick, draft_order.round')
                ->from('roster')
                ->join('draft_order','draft_order.player_id = roster.player_id and draft_order.league_id = roster.league_id'.
                ' and draft_order.year = '.$this->current_year,'left')
                ->join('player', 'player.id = roster.player_id')
                ->join('nfl_team', 'nfl_team.id = player.nfl_team_id','left')
                ->join('nfl_position', 'nfl_position.id = player.nfl_position_id','left')
                ->where('roster.team_id',$this->teamid)
                ->order_by('actual_pick','asc')
                ->get()->result();
    }

    function get_draft_team_id()
    {
        return $this->db->select('draft_team_id')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row()->draft_team_id;
    }

    function get_recent_picks_data()
    {

        return $this->db->select('draft_order.actual_pick, draft_order.round, draft_order.pick')
            ->select('player.id as player_id, player.first_name, player.last_name')
            ->select('nfl_position.text_id as position')
            ->select('nfl_team.club_id')
            ->select('team.team_name')
            ->select('owner.first_name as owner')
            ->from('draft_order')
            ->join('player','player.id = draft_order.player_id')
            ->join('nfl_position','player.nfl_position_id = nfl_position.id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id','left')
            ->join('team','team.id = draft_order.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->where('draft_order.league_id',$this->leagueid)
            ->where('draft_order.player_id !=',0)
            ->where('draft_order.year',$this->current_year)
            ->order_by('draft_order.actual_pick','desc')
            ->limit(500)->get()->result();

    }
    function get_upcoming_picks_data($include_current=false)
    {
        $data = $this->db->select('draft_order.actual_pick, draft_order.round, draft_order.pick, draft_order.id as pick_id')
            ->select('team.team_name')
            ->select('owner.first_name as owner')
            ->from('draft_order')
            ->join('team','team.id = draft_order.team_id')
            ->join('owner','owner.id = team.owner_id')
            ->where('draft_order.league_id',$this->leagueid)
            ->where('draft_order.player_id',0)
            ->where('draft_order.year',$this->current_year)
            ->order_by('draft_order.deadline','asc')
            ->limit(3)->get()->result();
        if ($include_current == false)
            unset($data[0]);

        return array_reverse($data);
    }

    function player_available($player_id)
    {
        $row = $this->db->select('id')->from('roster')->where('league_id',$this->leagueid)
            ->where('player_id',$player_id)->get()->row();
        if (count($row) > 0)
            return False;
        return True;
    }

    function draft_player($player_id, $admin = false)
    {
        // Get some settings / variables.
        $row = $this->db->select('draft_pick_id, draft_time_limit, draft_team_id')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row();
        $pickid = $row->draft_pick_id;
        $pick_time = $row->draft_time_limit;
        if($admin)
            $draft_team_id = $row->draft_team_id;
        else
            $draft_team_id = $this->teamid;

        // This gets the next "actual_pick" number
        $row = $this->db->select('actual_pick')->from('draft_order')->where('league_id',$this->leagueid)
            ->where('player_id !=',0)->where('year',$this->current_year)->order_by('actual_pick','desc')->limit(1)->get()->row();

        if (isset($row->actual_pick))
            $actual_pick = $row->actual_pick +1;
        else
            $actual_pick = 1;

        // Add player to roster
        $this->db->insert('roster', array('league_id' => $this->leagueid,
                                          'team_id' => $draft_team_id,
                                          'player_id' => $player_id,
                                          'starting_position_id' => 0));

        // Remove player from any watch lists, then reorder those teams watch lists.
        $watchteams = $this->db->select('team_id')->from('draft_watch')->where('league_id',$this->leagueid)->where('player_id',$player_id)
            ->get()->result();
        $this->db->delete('draft_watch',array('player_id' => $player_id, 'league_id' => $this->leagueid));
        foreach ($watchteams as $team)
        {
            $this->order_watch_list($team->team_id);
        }

        // Update draft_order table, store this player_id for this pick
        $this->db->where('id',$pickid);
        $this->db->update('draft_order',array('player_id' => $player_id, 'actual_pick' => $actual_pick));

        // Added this in order to determine if the draft is over or not, might be overkill if there is another
        // way to determine the end of draft here and send draft_end in legaue_settings table here
        $this->get_update_key();

        $this->adjust_pick_deadlines();

    }
    
    function adjust_pick_deadlines($pick_id = 0)
    {
        /* ---------------------------------------------------
        This function is called in these situations:
        1. get_update_key() - if update_key is 0, indicates a new draft, deadlines must be calculated
        2. get_update_key() - if last pick of the draft is reached and there are skipped picks left
        3. unpause($pick_id) - on unpause, recalculate all deadlines that occur after passed in pick_id
        4. draft_player() - after player draft, recalculate all deadlines starting with lowest overall_pick
        ---------------------------------------------------
        */

        // Get pick_time, draft_paused
        $settings = $this->db->select('draft_time_limit, draft_paused')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row();

        //if ($settings->draft_paused > 0)
        //    return;

        $pick_time = $settings->draft_time_limit;

        // Get the last actual pick number
        $actual_pick = 1;
        $row = $this->db->select('actual_pick')->from('draft_order')
            ->where('league_id',$this->leagueid)->where('year',$this->current_year)->where('player_id !=',0)
            ->order_by('actual_pick','desc')->limit(1)->get()->row();

        if (isset($row->actual_pick))
            $actual_pick = $row->actual_pick+1;


        // Get all remaining picks
        //$picks = $this->db->select('id, team_id')->from('draft_order')->where('league_id',$this->leagueid)
        //    ->where('player_id',0)->where('year',$this->current_year)->order_by('deadline','asc')
        //    ->order_by('overall_pick','asc')->get()->result();

        // Get all remaining picks to get current pick, if pick_id was passed, that's the current pick



        if($pick_id == 0)   //Either end of draft with skipped picks, or draft pick made.
        {                   //No pick passed in, used lowest deadline and all picks that follow.
            $picks = $this->db->select('id, team_id')->from('draft_order')->where('league_id',$this->leagueid)
                ->where('player_id',0)->where('year',$this->current_year)
                ->order_by('overall_pick','asc')->get()->result();
            if(!empty($picks))
                $nextpick = $picks[0];
            $new_deadline = time() + $pick_time;
        }
        else // Next pick was passed in, use that and only select picks that come after it to reorder
        {    //
            $nextpick = $this->db->select('id, team_id, overall_pick')->from('draft_order')->where('id',$pick_id)->get()->row();
            $picks = $this->db->select('id, team_id')->from('draft_order')->where('league_id',$this->leagueid)
                ->where('player_id',0)->where('id!=',$pick_id)->where('overall_pick>',$nextpick->overall_pick)->where('year',$this->current_year)
                ->order_by('deadline','asc')->get()->result();

            $skipped_picks = $this->db->select('id, team_id')->from('draft_order')->where('league_id',$this->leagueid)
            ->where('player_id',0)->where('id!=',$pick_id)->where('overall_pick<',$nextpick->overall_pick)->where('year',$this->current_year)
            ->order_by('overall_pick','desc')->get()->result();
            
            if ($settings->draft_paused > 0)
                $new_deadline = time() + $settings->draft_paused;
            else
                $new_deadline = time() + $pick_time;

            foreach($skipped_picks as $pick)
                array_unshift($picks,$pick);
            array_unshift($picks,$nextpick);
        }

        // Use next pick to get values for league_settings
        if(count($picks) > 0)
        {
            //$new_deadline = time() + $pick_time;
            $new_key = $new_deadline;
            $new_pick_id = $nextpick->id;
            $new_pick_team_id = $nextpick->team_id;
        }
        else
        {
            $new_deadline = 0;
            $new_pick_id = 0;
            $new_pick_team_id = 0;
            $new_key = 0;
        }

        // Update league_settings
        // I think this should be removed, league settings should only be updated by the get_draft_key function.
        // Maybe not, seems like more trouble to not have it here.

        $this->db->where('league_id',$this->leagueid);
        $this->db->update('league_settings', array('draft_update_key' => $new_deadline,
                                               'draft_team_id' => $new_pick_team_id,
                                               'draft_pick_id' => $new_pick_id));



        // Update deadlines for all remaining picks
        $update_batch = array();
        foreach($picks as $pick)
        {
            $update_batch[] = array('id' => $pick->id,
                                    'deadline' => $this->t($new_deadline),
                                    'actual_pick' => $actual_pick);
            $new_deadline+=$pick_time;
            $actual_pick++;
        }

        // Need to handle case when there are no remaining picks
        if (count($update_batch)>0)
            $this->db->update_batch('draft_order',$update_batch,'id');
        // $this->db->where('id',$pick->id);
        // $this->db->update('draft_order',array('deadline' => $this->t($new_deadline),'actual_pick' => $actual_pick));

        return $new_key; //return in case being called by get_update_key
    }

    function get_scheduled_start_time()
    {
        return $this->db->select('UNIX_TIMESTAMP(scheduled_draft_start_time) as draft_start_time')
            ->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->draft_start_time;
    }

    function get_settings()
    {
        return $this->db->select('UNIX_TIMESTAMP(scheduled_draft_start_time) as scheduled_draft_start_time')
            ->select('UNIX_TIMESTAMP(draft_start_time) as draft_start_time')
            ->select('draft_pick_id, draft_update_key, draft_paused, draft_team_id, draft_end')
            ->from('league_settings')->where('league_id',$this->leagueid)->get()->row();
    }

    function get_draft_years_array()
    {
        $data = array();
        $years = $this->db->select('distinct(year) as year')->from('draft_order')->where('league_id',$this->leagueid)
            ->order_by('year','desc')->get()->result();
        foreach($years as $y)
        {
            $data[] = $y->year;
        }
        return $data;
    }

    function get_draft_results($year)
    {
        return $this->db->select('team.team_name, round, pick, overall_pick, owner.first_name as owner_first, owner.last_name as owner_last')
            ->select('player.first_name, player.last_name, nfl_position.short_text as pos, nfl_team.club_id, player.short_name')
            ->from('draft_order')
            ->join('team','team.id = draft_order.team_id')
            ->join('player','player.id = draft_order.player_id')
            ->join('owner','team.owner_id = owner.id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id')
            ->where('draft_order.league_id',$this->leagueid)
            ->where('draft_order.year',$year)
            ->order_by('overall_pick','asc')
            ->get()->result();
    }

    function clear_watch_list()
    {
        $this->db->where('team_id',$this->teamid)->delete('draft_watch');
    }

    function reset_player_ranks()
    {
        $this->db->where('team_id',$this->teamid)->delete('draft_watch');
        $league_pos = $this->common_model->league_nfl_position_id_array();

        $players = $this->db->select('player.id')
            ->from('player')
            ->join('draft_player_rank','draft_player_rank.player_id = player.id')
            ->join('roster','roster.player_id = player.id and roster.league_id='.$this->leagueid,'left')
            ->where_in('nfl_position_id',$league_pos)
            ->where('roster.player_id is NULL',null,false)
            ->order_by('draft_player_rank.rank','asc')
            ->get()->result();

        $insert_batch = array();
        $order = 1;
        foreach ($players as $p)
        {
            $insert_batch[] = array('league_id' => $this->leagueid,
                                    'team_id'   => $this->teamid,
                                    'player_id' => $p->id,
                                    'order'     => $order);
            $order++;
        }
        if (!empty($insert_batch))
            $this->db->insert_batch('draft_watch',$insert_batch);
        
    }

}
