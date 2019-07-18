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
            ->where('trade.expires > NOW()')
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

    function get_trade_picks_data($trade_id, $team_id)
    {
        return $this->db->select('year, round')->from('trade_pick')->where('trade_id',$trade_id)
            ->where('team_id',$team_id)->get()->result();
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
            ->where('position.year',$pos_year)->order_by('max_roster','asc')->get()->result();

        // $team1_roster & $team2_roster
        $team1_adds = $this->db->select('nfl_position_id as pos_id, player.first_name, player.last_name')->from('trade_player')
            ->join('player','player.id = trade_player.player_id')
            ->where('trade_id',$trade_id)->where('team_id',$team_ids->team2_id)->get()->result();

        $team2_adds = $this->db->select('nfl_position_id as pos_id, player.first_name, player.last_name')->from('trade_player')
            ->join('player','player.id = trade_player.player_id')
            ->where('trade_id',$trade_id)->where('team_id',$team_ids->team1_id)->get()->result();

        // Important that team 2 be checked first.
        foreach (range(2,1) as $i)
        {
            // Get the nfl_position_id of the teams roster.
            ${'team'.$i.'_roster'} = $this->db->select('nfl_position.id as pos_id')->from('roster')
                ->join('player','roster.player_id = player.id')->join('nfl_position','nfl_position.id = player.nfl_position_id')
                ->where('roster.team_id',$team_ids->{'team'.$i.'_id'})
                ->get()->result();

            // Make an array of the pos_ids for all players if the trade goes through.
            $pos_ids = array();
            foreach(${'team'.$i.'_roster'} as $p)
                $pos_ids[] = $p->pos_id;

            if ($i == 1) {$j = 2;} else{$j=1;}
            // Plus players who will be added by trade
            foreach(${'team'.$i.'_adds'} as $add)
                $pos_ids[] = $add->pos_id;

            // Minus player who will be removed by the Trade
            foreach(${'team'.$j.'_adds'} as $remove)
            {
                foreach($pos_ids as $key => $pos_id)
                {
                    if ($pos_id == $remove->pos_id)
                    {
                        unset($pos_ids[$key]);
                        break;
                    }
                }
            }


            $lea_pos_array = array();
            foreach($positions as $lea_pos)
                $lea_pos_array[] = array('spots' => $lea_pos->max_roster, 'id_list' => $lea_pos->nfl_position_id_list, 'text_id' => $lea_pos->text_id);


            foreach($pos_ids as $key => $pos_id)
            {
                $ok = false;
                foreach($lea_pos_array as $pos_key => $lea_pos)
                {
                    if(in_array($pos_id, explode(',',$lea_pos['id_list'])))
                    {
                        if ($lea_pos['spots'] == -1)
                        {
                            $ok = true;
                            break;
                        }
                        if ($lea_pos['spots'] == 0)
                            continue;
                        if ($lea_pos['spots'] > 0)
                        {
                            $lea_pos_array[$pos_key]['spots']--;
                            $ok = true;
                            break;
                        }
                    }
                }
                if (!$ok)
                {
                    return array('team_id' => $team_ids->{'team'.$i.'_id'},
                                 'nfl_pos_id' => $pos_id);
                }

            }
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
        $picks = $this->db->select('draft_order_id, draft_future_id, year, round, team_id')
            ->from('trade_pick')->where('trade_id',$trade_id)->get()->result();
        $draft_end = $this->db->select('draft_end')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->draft_end;

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
            // Drop from old team
            $this->common_model->drop_player($p->player_id, $oldteamid);

            // Add player to new team
            $this->common_noauth_model->add_player($p->player_id, $newteamid, $this->leagueid);
        }

        foreach($picks as $p)
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

            if ($p->draft_order_id > 0)
            {
                $this->db->where('id',$p->draft_order_id);
                $this->db->update('draft_order',array('team_id' => $newteamid));
            }
            else
            {
                $this->db->where('id',$p->draft_future_id);
                $this->db->update('draft_future',array('pick_owner_team_id' => $newteamid));
            }
        }

        // Mark trade as completed
        $this->db->where('id',$trade_id)->update('trade',array('completed'=>1, 'completed_date' => t_mysql()));

        // Send email
        $this->send_trade_email_notice($trade_id, "Trade Accepted");
    }

    function decline_trade_offer($trade_id)
    {
        $this->db->select('team1_id, team2_id')->from('trade')->where('id',$trade_id)->get()->row();
        $this->db->where('id',$trade_id)->update('trade',array('canceled'=>1, 'completed_date' => t_mysql()));
        

        $row = $this->db->select('team1_id, team2_id')->from('trade')->where('id',$trade_id)->get()->row();
        if ($row->team1_id == $this->teamid)
        {
            $this->send_trade_email_notice($trade_id, "Trade Canceled");
            return "Trade Canceled";
        }
        if ($row->team2_id == $this->teamid)
        {
            $this->send_trade_email_notice($trade_id, "Trade Declined");
            return "Trade Declined";
        }
    }

    function valid_trade_action($tradeid,$action)
    {
        $trade = $this->db->select('team1_id, team2_id')->from('trade')
            ->where('id',$tradeid)->where('completed',0)->where('canceled',0)->where('expires > NOW()')
            ->get()->row();

        if (count($trade) == 0)
            return False;

        if ($action == "decline" || $action == "accept")
        {
            if ($trade->team2_id == $this->teamid || $trade->team1_id == $this->teamid)
                return True;
        }
    }

    function add_trade($team1_id, $team2_id, $team1_players, $team2_players, $team1_picks, $team2_picks, $trade_expire)
    {
        // First create an entry in the trade table.
        $data['league_id'] = $this->leagueid;
        $data['team1_id'] = $team1_id;
        $data['team2_id'] = $team2_id;
        $data['expires'] = date("Y-m-d H:i:s",$trade_expire);
        $data['year'] = $this->current_year;
        $this->db->insert('trade',$data);
        $players_batch = array();
        $picks_batch = array();
        // Add the players, if any
        $trade_id = $this->db->insert_id();
        if(is_array($team1_players))
        {
            foreach ($team1_players as $p)
            {
                $players_batch[] = array('trade_id' => $trade_id,
                                         'player_id' => $p,
                                         'team_id' => $team1_id);
            }
        }
        if(is_array($team2_players))
        {
            foreach ($team2_players as $p)
            {
                $players_batch[] = array('trade_id' => $trade_id,
                                         'player_id' => $p,
                                         'team_id' => $team2_id);
            }
        }
        if (count($players_batch) > 0)
            $this->db->insert_batch('trade_player', $players_batch);

        // Add picks, if any
        if(is_array($team1_picks))
        {
            foreach ($team1_picks as $p)
            {
                $pick_array = explode('-',$p);
                $idkey = 'draft_order_id';
                if ($pick_array[4] == "true")
                    $idkey = 'draft_future_id';
                $data = array('trade_id' => $trade_id,
                                       'round' => $pick_array[3],
                                       'year' => $pick_array[2],
                                       $idkey => $pick_array[1],
                                       'team_id' => $team1_id);
                $this->db->insert('trade_pick',$data);
            }
        }
        if(is_array($team2_picks))
        {
            foreach ($team2_picks as $p)
            {
                $pick_array = explode('-',$p);
                $idkey = 'draft_order_id';
                if ($pick_array[4] == "true")
                    $idkey = 'draft_future_id';
                $data = array('trade_id' => $trade_id,
                                       'round' => $pick_array[3],
                                       'year' => $pick_array[2],
                                       $idkey => $pick_array[1],
                                       'team_id' => $team2_id);
                $this->db->insert('trade_pick',$data);
            }
        }

        $this->send_trade_email_notice($trade_id,"Trade Proposed");

    }
    function trades_open()
    {
        $deadline_open = $this->db->select('count(league_id) as count')->from('league_settings')->where('league_id',$this->leagueid)
                ->where('trade_deadline > CURRENT_TIMESTAMP()')->get()->row()->count;
        if ($deadline_open > 0)
            return True;
        return False;

    }

    function get_settings_array()
    {
        $data = array();
        $league_settings = $this->db->select('trade_draft_picks')->from('league_settings')->where('league_id',$this->leagueid)->get()->row();
        $data['trade_draft_picks'] = $league_settings->trade_draft_picks;

        return $data;
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

        $proposed_players = $this->get_trade_players_data($tradeid, $data->team1_id);
        $requested_players = $this->get_trade_players_data($tradeid, $data->team2_id);
        $proposed_picks = $this->get_trade_picks_data($tradeid, $data->team1_id);
        $requested_picks = $this->get_trade_picks_data($tradeid, $data->team2_id);

        $this->load->model('common/common_model');
        $team1data = $this->common_model->team_info($data->team1_id);
        $team2data = $this->common_model->team_info($data->team2_id);

        $body = "<h3>";
        if ($subject == "Trade Proposed")
        {
            $body .= "New proposal from ".$team1data->team_name."<br><br>";
        }

        if ($subject == "Trade Accepted")
        {
            $body .= "Trade Accepted by ".$team2data->team_name."<br><br>";
        }

        if ($subject == "Trade Pending")
        {
            $body .= "Trade accepted, pending ".$team2data->team_name." clears enough roster spots.<br><br>";
        }

        if ($subject == "Trade Declined")
        {
            $body .= "Trade declined by ".$team2data->team_name."<br><br>";
        }

        if ($subject == "Trade Canceled")
        {
            $body .= "Trade canceled by ".$team1data->team_name."<br><br>";
        }
        $body .= "</h3>";
        if (count($proposed_players) > 0)
        {
            $body .= "<b>Players offered by ".$team1data->team_name.' ('.$team1data->first_name.' '.$team1data->last_name."):</b><ul>";
            foreach($proposed_players as $p)
            {
                $body.="<li>".$p->first_name.' '.$p->last_name.' ('.$p->pos.' - '.$p->club_id.")</li>";
            }
            $body.="</ul>";
        }

        if (count($proposed_picks) > 0)
        {
            $body .= "<br><b>Picks offered by ".$team1data->team_name.' ('.$team1data->first_name.' '.$team1data->last_name."):</b><ul>";
            foreach($proposed_picks as $p)
            {
                $body.='<li>Year: '.$p->year.', Round: '.$p->round."</li>";
            }
            $body.="</ul>";
        }

        if (count($requested_players) > 0)
        {
            $body .= "<br><b>Players requested from ".$team2data->team_name.' ('.$team2data->first_name.' '.$team2data->last_name."):</b><ul>";
            foreach($requested_players as $p)
            {
                $body.="<li>".$p->first_name.' '.$p->last_name.' ('.$p->pos.' - '.$p->club_id.")</li>";
            }
            $body.="</ul>";
        }

        if (count($requested_picks) > 0)
        {
            $body .= "<br><b>Picks requested from ".$team2data->team_name.' ('.$team2data->first_name.' '.$team2data->last_name."):</b><ul>";
            foreach($requested_picks as $p)
            {
                $body.='<li>Year: '.$p->year.', Round: '.$p->round."</li>";
            }
            $body.="</ul>";
        }

        $this->config->load('fflproject');
        $this->load->library('email');
        $this->email->from($this->config->item('fflp_email_reply_to'), $this->config->item('fflp_email_site_title'));
        $this->email->to($team2data->owner_email);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();

        $this->load->library('email');
        $this->email->from($this->config->item('fflp_email_reply_to'), $this->config->item('fflp_email_site_title'));
        $this->email->to($team1data->owner_email);
        $this->email->subject($subject);
        $this->email->message($body);
        $this->email->send();

    }


    function get_trade_log_array($year=0, $limit=100000, $start=0, $days=0)
    {
        if ($year == 0)
            $year = $this->current_year;

        $result = array();

        $this->db->select('SQL_CALC_FOUND_ROWS null as rows',FALSE);

        $this->db->from('trade')->where('completed',1)->where('trade.year',$year)->where('league_id',$this->leagueid)->get()->result();
        $result['total'] = $this->db->query('SELECT FOUND_ROWS() count;')->row()->count;

        $this->db->select('trade.id, UNIX_TIMESTAMP(trade.completed_date) as completed_date, team1_id, team2_id')
            ->select('t1.team_name as team1_name, t2.team_name as team2_name')
            ->from('trade')
            ->join('team as t1','t1.id = team1_id')
            ->join('team as t2','t2.id = team2_id')
            ->where('trade.league_id',$this->leagueid)->where('year',$this->current_year)->where('completed',1);
            if ($days != 0)
                $this->db->where('trade.completed_date > date_sub(now(), INTERVAL '.$days.' day)');
        $trades = $this->db->order_by('completed_date','desc')->limit($limit,$start)->get()->result();



        $result['log'] = array();
        foreach($trades as $t)
        {
            $result['log'][$t->id]['completed_date'] = $t->completed_date;
            $result['log'][$t->id]['teams'][$t->team2_id]['players'] = array();
            $result['log'][$t->id]['teams'][$t->team2_id]['picks'] = array();
            $result['log'][$t->id]['teams'][$t->team1_id]['players'] = array();
            $result['log'][$t->id]['teams'][$t->team1_id]['picks'] = array();
            $result['log'][$t->id]['teams'][$t->team2_id]['team_name'] = $t->team1_name;
            $result['log'][$t->id]['teams'][$t->team1_id]['team_name'] = $t->team2_name;

            $players = $this->db->select('first_name, last_name, player.id as player_id, team_name, team.id as team_id')
                ->from('trade_player')->join('player','player.id = trade_player.player_id')
                ->join('team','team.id = trade_player.team_id')
                ->where('trade_player.trade_id',$t->id)->get()->result();

            foreach($players as $p)
            {
                $result['log'][$t->id]['teams'][$p->team_id]['players'][] = $p;
            }

            $picks = $this->db->select('round, year, team_id')->from('trade_pick')->where('trade_id',$t->id)->get()->result();

            foreach($picks as $p)
                $result['log'][$t->id]['teams'][$p->team_id]['picks'][] = $p;
        }
        return $result;
    }

    function get_future_pick_years_array()
    {
        $result = array();
        $data = $this->db->select('distinct(year) as year')->from('draft_future')->where('league_id',$this->leagueid)
            ->where('year>',$this->current_year)->get()->result();
        foreach($data as $d)
        {
            $result[] = $d->year;
        }
        $default = $this->get_default_draft_trade_year();
        if ($default)
        {
            if (!in_array($default,$result))
                array_unshift($result,$default);
        }
        return $result;
    }

    function get_default_draft_trade_year($teamid = 0)
    {
        if ($teamid == 0)
            $teamid = $this->teamid;
        $draft_end = $this->db->select('draft_end')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->draft_end;
        if ($draft_end < $this->current_year)
            return $this->current_year;
        return $this->db->select('min(year) as year')->from('draft_future')->where('pick_owner_team_id',$teamid)->where('league_id',$this->leagueid)
            ->where('year > ',$this->current_year)->get()->row()->year;

    }

    function get_available_picks_data($year=0, $teamid=0)
    {
        $data = array();
        if ($year == 0)
            $year = $this->current_year;
        if ($teamid == 0)
            $teamid = $this->teamid;

        $draft_end = $this->db->select('draft_end')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->draft_end;

        // If we want this year and the draft hasn't ended and there are picks in draft_order table....
        if ($year == $this->current_year && $draft_end < $this->current_year)
        {
            $data['picks'] = $this->db->select('id,round,pick,overall_pick')->from('draft_order')->where('team_id',$teamid)->where('player_id',0)
                ->where('year',$year)->order_by('overall_pick','asc')->get()->result();
            if(count($data['picks']) > 0)
            {
                $data['future'] = False;
                return $data;
            }
        }

        $data['picks'] = $this->db->select('id,round, 0 as pick')->from('draft_future')->where('pick_owner_team_id',$teamid)->where('year',$year)
            ->order_by('round','asc')->get()->result();

        $data['future'] = True;
        return $data;

    }

    function players_on_roster($playerid_array, $teamid)
    {
        foreach($playerid_array as $p)
        {
            $num = $this->db->from('roster')->where('player_id',$p)->where('team_id',$teamid)->count_all_results();
            if ($num <= 0)
                return False;
        }
        return True;
    }

    function team_pick_available($id, $year, $round, $future, $teamid)
    {
        $draft_end = $this->db->select('draft_end')->from('league_settings')->where('league_id',$this->leagueid)->get()->row()->draft_end;

        // Picks in the past aren't avaialble
        if ($year <= $draft_end)
            return False;

        if ($year == $this->current_year && $draft_end < $this->current_year && $future=="false")
        {
            $num = $this->db->from('draft_order')->where('id',$id)->where('year',$year)->where('team_id',$teamid)
                ->where('round',$round)->count_all_results();
            if ($num > 0)
                return True;
            return False;
        }
        $num = $this->db->from('draft_future')->where('id',$id)->where('pick_owner_team_id',$teamid)->where('year',$year)
            ->where('round',$round)->count_all_results();
        if ($num > 0)
            return True;
        return False;
    }

    function pick_ownership_ok($tradeid)
    {
        $rows = $this->db->select('id, year, round, team_id, draft_order_id, draft_future_id')->from('trade_pick')
            ->where('trade_id',$tradeid)->get()->result();

        foreach($rows as $row)
        {
            $future = "false";
            $id = $row->draft_order_id;
            if ($row->draft_future_id > 0)
            {
                $future = "true";
                $id = $row->draft_future_id;
            }
            if (!$this->team_pick_available($id, $row->year, $row->round, $future, $row->team_id))
                return False;
        }
        return True;
    }

}
