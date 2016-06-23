<?php

class Trade_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->leagueid = $this->session->userdata('league_id');
    }

    function get_league_teams_data()
    {
    	return $this->db->select('id, team_name')->from('team')
    		->where('league_id',$this->leagueid)
    		->where('active',1)
    		->get()->result();
    }

    function get_roster_data($team_id = 'this')
    {
    	if ($team_id == 'this')
    		$team_id = $this->teamid;
    	return $this->db->select('player.first_name, player.last_name, player.short_name, player.id as player_id')
    		->select('nfl_team.club_id, nfl_position.text_id as pos')
    		->select('sum(fantasy_statistic.points)')
    		->from('roster')->join('player','player.id = roster.player_id')
    		->join('nfl_team','nfl_team.id = player.nfl_team_id')
    		->join('fantasy_statistic','fantasy_statistic.player_id = player.id and fantasy_statistic.year = '.$this->current_year,'left')
    		->join('nfl_position','nfl_position.id = player.nfl_position_id')
    		->where('roster.team_id',$team_id)
    		->group_by('player.id')->order_by('nfl_position.display_order','asc')
    		->get()->result();
    }

    function get_open_trades()
    {
        return $this->db->select('trade.id as trade_id, UNIX_TIMESTAMP(trade.expires) as expires')
            ->select('team1_id, team2_id, team1.team_name as team1_name, team2.team_name as team2_name')
            ->from('trade')
            ->join('team as team1','team1.id = team1_id')
            ->join('team as team2','team2.id = team2_id')
            ->where('trade.league_id',$this->leagueid)
            ->where('completed',0)
            ->where('canceled',0)
            ->where('trade.expires >','NOW()',false)
            ->where('(team1_id = '.$this->teamid.' or team2_id='.$this->teamid.')')
            ->get()->result();
    }

    function get_trade_players_data($trade_id, $team_id)
    {
        return $this->db->select('player.first_name, player.last_name, player.id as player_id')
            ->select('nfl_position.short_text as pos')
            ->select('nfl_team.club_id')
            ->from('trade_player')
            ->join('player','trade_player.player_id = player.id')
            ->join('nfl_position','nfl_position.id = player.nfl_position_id')
            ->join('nfl_team','nfl_team.id = player.nfl_team_id')
            ->where('trade_player.trade_id',$trade_id)
            ->where('trade_player.team_id',$team_id)
            ->get()->result();
    }

    function player_ownership_ok($trade_id)
    {
        $rows = $this->db->select('team_id, player_id')->from('trade_player')
            ->where('trade_id',$trade_id)->get()->result();

        foreach($rows as $row)
        {
            $num = $this->db->from('roster')->where('team_id',$row->team_id)->where('player_id',$row->player_id)
                ->count_all_results();
            if ($num < 1)
                return False;
        }

        return True;

    }

    function trade_position_over_limit($trade_id, $team_id = 0)
    {
        // This is pretty complicated

        $team_ids = $this->db->select('team1_id, team2_id')->from('trade')->where('id',$trade_id)->get()->row();

        $team1_pos_array = array();
        $team2_pos_array = array();

        $pos_year = $this->common_model->league_position_year();
        $positions = $this->db->select('id, nfl_position_id_list, max_roster, text_id')->from('position')->where('league_id',$this->leagueid)
            ->where('position.year',$pos_year)->get()->result();

        // $team1_roster & $team2_roster
        $team1_adds = $this->db->select('nfl_position_id as pos_id')->from('trade_player')
            ->join('player','player.id = trade_player.player_id')
            ->where('trade_id',$trade_id)->where('team_id',$team_ids->team2_id)->get()->result();

        $team2_adds = $this->db->select('nfl_position_id as pos_id')->from('trade_player')
            ->join('player','player.id = trade_player.player_id')
            ->where('trade_id',$trade_id)->where('team_id',$team_ids->team1_id)->get()->result();

        foreach (range(1,2) as $i)
        {
            ${'team'.$i.'_roster'} = $this->db->select('nfl_position.id as pos_id')->from('roster')
                ->join('player','roster.player_id = player.id')->join('nfl_position','nfl_position.id = player.nfl_position_id')
                ->where('roster.team_id',$team_ids->{'team'.$i.'_id'})
                ->get()->result();

            if ($i == 1) {$j = 2;} else{$j=1;}

            foreach($positions as $pos)
            {
                // Current roster
                foreach(${'team'.$i.'_roster'} as $player)
                {
                    if (in_array($player->pos_id, explode(',',$pos->nfl_position_id_list)))
                    {
                        if (!isset(${'team'.$i.'_pos_array'}[$pos->id]))
                            ${'team'.$i.'_pos_array'}[$pos->id] = 0;
                        ${'team'.$i.'_pos_array'}[$pos->id]++;
                    }
                }
                // Plus player who team1 will be adding
                foreach(${'team'.$i.'_adds'} as $add)
                {
                    if (in_array($add->pos_id, explode(',',$pos->nfl_position_id_list)))
                    {
                        if (!isset(${'team'.$i.'_pos_array'}[$pos->id]))
                            ${'team'.$i.'_pos_array'}[$pos->id] = 0;
                        ${'team'.$i.'_pos_array'}[$pos->id]++;
                    }
                }
                // Minus players who will be removed
                foreach(${'team'.$j.'_adds'} as $remove)
                {
                    if (in_array($remove->pos_id, explode(',',$pos->nfl_position_id_list)))
                    {
                        if (!isset(${'team'.$i.'_pos_array'}[$pos->id]))
                            ${'team'.$i.'_pos_array'}[$pos->id] = 0;
                        ${'team'.$i.'_pos_array'}[$pos->id]--;
                    }
                }
            }
        }

        $max = array();
        foreach ($positions as $pos)
            $max[$pos->id] = $pos->max_roster;

        // It's important that team2 be checked first and make room
        foreach($team2_pos_array as $posid => $t)
        {
            
            if ($max[$posid] == -1)
                continue;
            // Return $team2 id, it's over the limit
            if($t > $max[$posid])
                return $team_ids->team2_id;
        }

        foreach($team1_pos_array as $posid => $t)
        {
            if ($max[$posid] == -1)
                continue;
            // Return $team1 id, it's over the limit
            if($t > $max[$posid])
                return $team_ids->team1_id;
        }



        // Made it here, no teams would be over position limits
        return False;
        // print_r($team1_pos_array);
        // print_r($team2_pos_array);
        // print_r($max);


    }

    function trade_roster_over_limit($trade_id, $team_id = 0)
    {

        // False if no one is over the Limit
        // ID of team over limit is returned
        // If team_id specified, return true if team is over the limit
        $this->load->model('common/common_model');
        $max = $this->common_model->get_roster_max();

        // There is no roster max for this league... everything goes.
        if ($max == -1)
            return False;

        $row = $this->db->select('team1_id, team2_id')->from('trade')->where('id',$trade_id)->get()->row();

        $team1_add_num = $this->db->from('trade_player')->where('trade_id',$trade_id)->where('team_id',$row->team2_id)->count_all_results();
        $team2_add_num = $this->db->from('trade_player')->where('trade_id',$trade_id)->where('team_id',$row->team1_id)->count_all_results();

        // No teamid, so both teams must be under the limit.
        if ($team_id == 0)
        {
            $team1_num = $this->db->from('roster')->where('team_id',$row->team1_id)->count_all_results();
            $team2_num = $this->db->from('roster')->where('team_id',$row->team2_id)->count_all_results();

            // Team 2 would be over the limit, want to check this team before team1
            if (($team2_num + ($team2_add_num - $team1_add_num) > $max))
                return $row->team2_id; // someone is over the limit

            // Team 1 would be over the limit
            if (($team1_num + ($team1_add_num - $team2_add_num) > $max))
                return $row->team1_id;

        }

        // Neither team would be over the limit if trade is completed.
        return False;

        // I think the rest of this can go away.
        // if ($team_id == $row->team1_id)
        //     $add_num = $team1_add_num - $team2_add_num;
        // elseif ($team_id == $row->team2_id)
        //     $add_num = $team2_add_num - $team1_add_num;
        // else
        //     return False; // Teamid isn't part of the trade.

        // $current_num = $this->db->from('roster')->where('team_id',$team_id)->count_all_results();

        // if ($current_num + $add_num > $max)
        //     return False; // Over the limit
        // else
        //     return True; // Not over the limit
    }

    function accept_trade_offer($trade_id)
    {
        $players = $this->db->select('player_id, team_id')->from('trade_player')->where('trade_id',$trade_id)->get()->result();
        $teams = $this->db->select('team1_id, team2_id')->from('trade')->where('id',$trade_id)->get()->row();

        foreach($players as $p)
        {
            if ($p->team_id == $teams->team1_id)
            {
                $newteamid = $teams->team2_id;
                $oldteamid = $teams->team1_id;
            }
            else
            {
                $newteamid = $teams->team1_id;
                $oldteamid = $teams->team2_id;
            }
            // Delete player from old team
            $this->load->model('common/common_model');
            $gamestart = $this->common_model->player_game_start_time($p->player_id);

            // Delete from starter table
            $this->db->where('league_id',$this->leagueid)->where('team_id',$oldteamid)->where('player_id',$p->player_id);
            if ($gamestart > time()) // if game is in the future, include this week
                $this->db->where('week >=',$this->current_week);
            else
                $this->db->where('week >',$this->current_week);
            $this->db->delete('starter');

            // Delete from roster table
            $this->db->delete('roster', array('league_id'=>$this->leagueid, 'team_id'=>$oldteamid, 'player_id'=>$p->player_id));

            // Add player to new team
            $this->db->insert('roster', array('league_id'=>$this->leagueid,'team_id'=>$newteamid,'player_id'=>$p->player_id, 'starting_position_id'=>0));
        }
        // Mark trade as completed
        $this->db->where('id',$trade_id)->update('trade',array('completed'=>1, 'completed_date' => t_mysql()));

        // Send email
        $this->send_trade_email_notice($trade_id, "Trade Accepted");
    }

    function decline_trade_offer($trade_id)
    {
        $this->db->where('id',$trade_id)->update('trade',array('canceled'=>1, 'completed_date' => t_mysql()));
        $this->send_trade_email_notice($trade_id, "Trade Declined");
    }

    function valid_trade_action($tradeid,$action)
    {
        $trade = $this->db->select('team1_id, team2_id')->from('trade')
            ->where('id',$tradeid)->where('completed',0)->where('canceled',0)->where('expires >','NOW()')
            ->get()->row();

        if (count($trade) == 0)
            return False;

        if ($action == "decline" || $action == "accept")
        {
            if ($trade->team2_id == $this->teamid)
                return True;
        }
    }

    function add_trade($team1_id, $team2_id, $team1_players, $team2_players, $trade_expire)
    {
        $data['league_id'] = $this->leagueid;
        $data['team1_id'] = $team1_id;
        $data['team2_id'] = $team2_id;
        $data['expires'] = date("Y-m-d H:i:s",$trade_expire);
        $data['year'] = $this->current_year;
        $this->db->insert('trade',$data);

        $trade_id = $this->db->insert_id();
        foreach ($team1_players as $p)
        {
            $players_batch[] = array('trade_id' => $trade_id,
                                     'player_id' => $p,
                                     'team_id' => $team1_id);
        }
        foreach ($team2_players as $p)
        {
            $players_batch[] = array('trade_id' => $trade_id,
                                     'player_id' => $p,
                                     'team_id' => $team2_id);
        }
        $this->db->insert_batch('trade_player', $players_batch);

        $this->send_trade_email_notice($trade_id,"Trade Proposed");

    }
    function trades_open()
    {
        $deadline_open = $this->db->select('count(league_id) as count')->from('league_settings')->where('league_id',$this->leagueid)
                ->where('trade_deadline >','CURRENT_TIMESTAMP()')->get()->row()->count;
        if ($deadline_open > 0)
            return True;
        return False;

    }

    function reverse_offer($tradeid)
    {
        $row = $this->db->select('team1_id, team2_id')->from('trade')->where('id',$tradeid)->get()->row();
        $this->db->where('id',$tradeid);
        $this->db->update('trade',array('team1_id' => $row->team2_id,'team2_id' => $row->team1_id));
        $this->send_trade_email_notice($tradeid, "Trade Pending");
    }

    function send_trade_email_notice($tradeid, $subject)
    {
        $data = $this->db->select('team1_id, team2_id')
            ->from('trade')->where('trade.id',$tradeid)->get()->row();

        $proposed = $this->get_trade_players_data($tradeid, $data->team1_id);
        $requested = $this->get_trade_players_data($tradeid, $data->team2_id);

        $this->load->model('common/common_model');
        $team1data = $this->common_model->team_info($data->team1_id);
        $team2data = $this->common_model->team_info($data->team2_id);


        if ($subject == "Trade Proposed")
        {
            $body = "New proposal from ".$team1data->team_name."\n\n";
        }

        if ($subject == "Trade Accepted")
        {
            $body = "Trade Accepted by ".$team2data->team_name."\n\n";
        }

        if ($subject == "Trade Pending")
        {
            $body = "Trade accepted, pending ".$team2data->team_name." clears enough roster spots.\n\n";
        }

        if ($subject == "Trade Declined")
        {
            $body = "Trade declined by ".$team2data->team_name."\n\n";
        }

        $body .= "Players offered by ".$team1data->team_name.' ('.$team1data->first_name.' '.$team1data->last_name."):\n";
        foreach($proposed as $p)
        {
            $body.=$p->first_name.' '.$p->last_name.' ('.$p->pos.' - '.$p->club_id.")\n";
        }
        $body .= "\nPlayers requested from ".$team2data->team_name.' ('.$team2data->first_name.' '.$team2data->last_name."):\n";
        foreach($requested as $p)
        {
            $body.=$p->first_name.' '.$p->last_name.' ('.$p->pos.' - '.$p->club_id.")\n";
        }

        $this->load->library('email');
        $this->email->from('ff@mylanparty.net');
        $this->email->to($team2data->owner_email);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();

        $this->load->library('email');
        $this->email->from('ff@mylanparty.net');
        $this->email->to($team1data->owner_email);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();

    }

    function get_trade_log_array()
    {
        $result = array();
        $data = $this->db->select('player.first_name, player.last_name, player.id as player_id')
            ->select('team1.team_name as team1_name, team2.team_name as team2_name, team1.id as team1_id, team2.id as team2_id')
            ->select('trade.id as trade_id')
            ->select('UNIX_TIMESTAMP(trade.completed_date) as completed_date')
            ->select('trade_player.team_id as old_team_id')
            ->from('trade_player')->join('player','player.id = trade_player.player_id')
            ->join('trade','trade.league_id = '.$this->leagueid.' and trade.year = '.$this->current_year.' and trade.id = trade_player.trade_id')
            ->join('team as team1','team1.id = trade.team1_id')
            ->join('team as team2','team2.id = trade.team2_id')
            ->where('completed',1)
            ->order_by('completed_date','desc')
            ->get()->result();

        foreach($data as $row)
        {
            $result[$row->trade_id]['team1']['team_name'] = $row->team1_name;
            $result[$row->trade_id]['team2']['team_name'] = $row->team2_name;
            $result[$row->trade_id]['team1']['team_id'] = $row->team1_id;
            $result[$row->trade_id]['team2']['team_id'] = $row->team2_id;
            $result[$row->trade_id]['completed_date'] = $row->completed_date;
            if ($row->old_team_id == $row->team1_id)
                $thisteam = "team1";
            else
                $thisteam = "team2";
            $player = array('first_name' => $row->first_name,
                            'last_name' => $row->last_name,
                            'player_id' => $row->player_id);
            $result[$row->trade_id][$thisteam]['players'][] = $player;

        }

        return $result;
    }
}
