<?php
class Quickstats extends MY_User_Controller{

// This controller presents the ajax data used by various jquery ajax/post functions.
// The views dislay content to be put between <tbody></tbody> tags.


    function __construct()
    {
        parent::__construct();
        $this->load->model('league/player_statistics_model');
    }

    function team()
    {
        $team_id = $this->input->post('id');
        $week = $this->input->post('week');
        $this->load->model('myteam/myteam_roster_model');
        $this->load->model('myteam/schedule_model');
        $this->load->model('league/teams_model');
        $this->load->model('myteam/myteam_settings_model');
        $data = array();
        $data['bench'] = $this->teams_model->get_bench_quickstats_data($team_id);
        $data['starters'] = $this->myteam_roster_model->get_starting_lineup_array($team_id);
        $data['schedule'] = $this->schedule_model->get_team_schedule($team_id);
        $data['team_id'] = $team_id;
        $data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
        $data['record'] = $this->myteam_roster_model->get_team_record_data($team_id);

        $data['team'] = $this->teams_model->get_team_data($team_id);
        if($data['team']->logo)
            $data['logo'] = $this->myteam_settings_model->get_logo_url($data['team']->team_id,'med');
        else
            $data['logo'] = $this->myteam_settings_model->get_default_logo_url('med');

        //Begin view
        ?>
        <h4><a href="<?=site_url('league/teams/view/'.$team_id)?>"><?=$data['team']->team_name?></a></h4>

        <div>
            <?php //print_r($data['matchups']); ?>
            <!-- <div class="table-responsive col-sm-6">
                Starters
                <table class="table">
                    <thead>
                        <th colspan=2>Player</th><th>Points</th>
                    </thead>
                    <tbody>
                        <?php foreach($data['starters'] as $p): ?>
                        <tr>
                            <td><?=$p['pos_text']?></td>
                            <td><?=$p['player']->first_name.' '.$p['player']->last_name?></td>
                            <td><?=$p['player']->points?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div> -->
            <div class="table-responsive">
                <h5>Bench - Week <?=$this->session->userdata('current_week')?></h5>
                <table class="table">
                    <thead>
                        <th>Pos</th><th>Player</th><th>Opp</th><th>Pts</th>
                    </thead>
                    <tbody>
                        <?php foreach($data['bench'] as $p): ?>
                        <tr>
                            <td><?=$p->pos_text?></td>
                            <td>
                                <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->player_id?>">
                                <?=$p->short_name?>
                            </a>
                            </td>
                            <td><?=$data['matchups'][$p->club_id]['opp']?></td>
                            <td><?=$p->points?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php // End view

    }

    function player()
    {
        $id = $this->input->post('id');
        $stats = $this->player_statistics_model->get_statistics_year_array($id);
        $player = $this->player_statistics_model->get_player_data($id);

        //Begin view
        ?>
        <div class="section">

            <div class="columns">
                <div class="column is-2-tablet">
                    <?php if($player->photo != ""): ?>
                        <img class="stat-popup-img" src="<?=site_url('images/'.$player->photo)?>">
                    <?php endif;?>
                </div>
                <div class="column is-10-tablet">
                    
                    <a href="<?=site_url('league/players/id/'.$player->player_id)?>" target="_blank"><?=$player->first_name.' '.$player->last_name?> - <?=$player->pos?> #<?=$player->number?></a>  
                    <br>NFL Team: <?=$player->club_id?>
                    <br>Fantasy Team: <?=$player->team_name?>
                    <br>Draft Round: <?=$player->round?> <?php if($player->round != "Undrafted"){echo " (#".$player->draft_pick." overall)";}?>
                </div>
            </div>
            <div class="fflp-overflow">
                <table class="table is-fullwidth is-narrow is-striped">
                    <thead>
                        <th>Week</th>
                        <th>Points</th>
                        <th>Opp</th>
                        <?php if (array_key_exists('cats',$stats[1])):?>
                            <?php foreach($stats[1]['cats'] as $s):?>
                                <th>
                                    <?php if (is_array($s) && array_key_exists('cat_text',$s)):?>
                                        <?=$s['cat_text']?>
                                    <?php endif;?>
                                </th>
                            <?php endforeach;?>
                        <?php endif;?>
                    </thead>
                    <tbody>
                        <?php foreach($stats as $week => $s): ?>
                            <?php //if($week > $this->current_week -3 && $week <= $this->current_week +1): ?>
                            <?php if($week <= $this->current_week +1): ?>
                                <?php if($week == $this->current_week): ?>
                                    <tr style="background-color:#E0ECF8">
                                <?php else: ?>
                                    <tr>
                                <?php endif;?>
                                <td style="font-weight:bold"><?=$week?></td>
                                <td><?=$s['total']['value']?></td>
                                <td>
                                <?=$s['opp']?>
                            </td>
                                <?php if (array_key_exists('cats',$stats['1'])): ?>
                                    <?php foreach($stats[1]['cats'] as $cat => $c):?>
                                        <td>
                                            <?=$stats[$week]['cats'][$cat]['value']?>
                                        </td>
                                    <?php endforeach;?>
                                <?php endif?>
                            </tr>
                            <?php endif;?>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>

        </div>
        <?php // end view
    }

    function player_news()
    {
        $id = $this->input->post('id');
        $news_item = $this->player_statistics_model->get_player_news_from_id($id);

        // Begin view
        ?>

        <?=$news_item->analysis?>

        <?php // end view
    }
}
