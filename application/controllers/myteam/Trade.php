<?php

class Trade extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/trade_model');
        $this->bc['My Team'] = "";
        $this->bc['Trade'] = "";
    }

    function test()
    {
        $this->trade_model->trade_position_over_limit(4);
    }

    function index()
    {
        $data = array();
        if (!$this->trade_model->trades_open())
        {
            $this->user_view('user/myteam/trade/closed.php', $data);
            return;
        }

        $data['team_id'] = $this->teamid;

        $league_teams = $this->trade_model->get_league_teams_data();
        $data['team_options'] = array('0' => "Choose Team");
        $data['roster'] = $this->trade_model->get_roster_data();
        $data['team_id'] = $this->teamid;
        foreach ($league_teams as $t)
        {
            if ($t->id != $this->teamid)
                $data['team_options'][$t->id] = $t->team_name;
        }

        $this->load->helper('form');
    	$this->user_view('user/myteam/trade.php', $data);
    }

    function propose()
    {
        $data = array();
        $league_teams = $this->trade_model->get_league_teams_data();
        $data['team_options'] = array('0' => "Choose Team");
        $data['roster'] = $this->trade_model->get_roster_data();
        $data['team_id'] = $this->teamid;
        $data['team_name'] = $this->team_name;
        foreach ($league_teams as $t)
        {
            if ($t->id != $this->teamid)
                $data['team_options'][$t->id] = $t->team_name;
        }
        $this->load->helper('form');

        $this->bc['Trade'] = site_url('myteam/trade');
        $this->bc['Propose'] = "";

        $this->user_view('user/myteam/trade/propose.php', $data);

    }

    function ajax_get_team_roster()
    {
        $teamid = $this->input->post('team_id');
        $data['team_roster'] = $this->trade_model->get_roster_data($teamid);
        $this->load->view('user/myteam/trade/ajax_get_team_roster',$data);

    }

    function submit_trade_offer()
    {
        $team1_id = $this->teamid;
        $team2_id = $this->input->post('other_team');
        $team1_players = $this->input->post('offer');
        $team2_players = $this->input->post('request');
        $trade_expire = $this->input->post('trade_expire');
        if ((is_array($team1_players) && count($team1_players) > 0) && (is_array($team2_players) && count($team2_players) >0))
            $this->trade_model->add_trade($team1_id, $team2_id, $team1_players, $team2_players, $trade_expire);
    }

    function ajax_accept()
    {
        if (!$this->offseason)
        {
            $tradeid = $this->input->post('tradeid');
            $response = array('success' => False);
            // Need checks to make sure team is team2 in the trade table
            if ($this->trade_model->valid_trade_action($tradeid,'accept'))
            {
                $limit_teamid = $this->trade_model->trade_roster_over_limit($tradeid);

                
                if($limit_teamid == $this->session->userdata('team_id'))
                {
                    // Offering is over the roster limit, need to drop players
                    $response['msg'] = "You have too many players on your roster to accept the trade.  You must drop some players first.";
                }
                elseif($limit_teamid > 0) // The other team who made the offer must be over the limit, swich offer and send back.
                {
                    // Other team is over the roster limit, reverse trade
                    $this->trade_model->reverse_offer($tradeid);
                    $response['msg'] = "The offer has been accepted, pending the offering team having room on their roster to complete the trade.";
                }
                elseif($limit_teamid == False)
                {
                    // OK so far, check position limits
                    $limit_teamid = $this->trade_model->trade_position_over_limit($tradeid);
                
                    if($limit_teamid == $this->session->userdata('team_id'))
                    {
                        // Offering team is over a position limit
                        $response['msg'] = "You have too many players at this position.  You must be under the limit first.";
                    }
                    elseif($limit_teamid > 0)
                    {
                        // Other team is over a position limit, reverse trade
                        $this->trade_model->reverse_offer($tradeid);
                        $response['msg'] = "The offer has been accepted, pending the offering team having room at that position.";
                    }
                    elseif($limit_teamid == False) // No one is over the limit, process the transaction
                    {
                        // Everything appears to be OK as far as limits, check ownership and process trade
                        if ($this->trade_model->player_ownership_ok($tradeid))
                        {
                            $this->trade_model->accept_trade_offer($tradeid);
                            $response['success'] = true;
                            $response['msg'] = "Trade successfully processed!";
                        }
                        else
                        {
                            // This means a player involved in the trade is no longer owned by the original team when the trade was proposed.
                            $response['msg'] = "Oops, someone dropped a player invovled in this trade, it's no longer valid.";
                        }
                    }
                }
                echo json_encode($response);
            }
        }

    }

    function ajax_decline()
    {
        if (!$this->offseason)
        {
            $response = array('success' => false, 'msg' => '');
            $tradeid = $this->input->post('tradeid');
            if($this->trade_model->valid_trade_action($tradeid,'decline'))
            {
                $this->trade_model->decline_trade_offer($tradeid);
                $response['success'] = true;
            }
            echo json_encode($response);
        }
    }

    function load_open_trades()
    {
        $open_trades = array();
        $trades = $this->trade_model->get_open_trades();
        foreach ($trades as $t)
        {
            if ($t->team1_id == $this->teamid)
                $type = 'request';
            else
                $type = 'offer';
            $open_trades[$type][$t->trade_id]['trade'] = $t;
            $open_trades[$type][$t->trade_id]['team1_players'] = $this->trade_model->get_trade_players_data($t->trade_id,$t->team1_id);
            $open_trades[$type][$t->trade_id]['team2_players'] = $this->trade_model->get_trade_players_data($t->trade_id,$t->team2_id);
            $open_trades[$type][$t->trade_id]['expires_text'] = round(($t->expires - time()) / (60*60)) . " hours";
        }

        // Start the view
        ?>
        <?php if (count($open_trades) == 0): ?>
            <tr><td colspan="4" class="text-center">There no trades on the table.</td></tr>
        <?php else: ?>
            <?php foreach ($open_trades as $type => $trade): ?>
                <?php foreach($trade as $trade_id => $t): ?>
                <tr>
                    <td>
                        <div><strong>From <?=$t['trade']->team1_name?></strong></div>
                        <?php foreach($t['team1_players'] as $p): ?>
                            <div><?=$p->first_name.' '.$p->last_name?></div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <div><strong>From <?=$t['trade']->team2_name?></strong></div>
                        <?php foreach($t['team2_players'] as $p): ?>
                            <div><?=$p->first_name.' '.$p->last_name?></div>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?=$t['expires_text']?>
                    </td>
                    <td>
                    <?php if ($type == 'offer'): ?>

                        <button class="button accept-button small" value="<?=$trade_id?>">
                            Accept
                        </button>
                        <button class="button decline-button small" value="<?=$trade_id?>">
                            Decline
                        </button>

                    <?php else: ?>
                        Waiting for response
                    <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php
        // End the view
    }

    function log()
    {
        $data = array();
        $data['log'] = $this->trade_model->get_trade_log_array();

        $this->bc['Trade'] = site_url('myteam/trade');
        $this->bc['Log'] = "";
        $this->user_view('user/myteam/trade/log.php', $data);
    }
}
