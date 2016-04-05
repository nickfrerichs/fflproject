<?php //print_r($starters)?>
<?php $this->load->view('template/modals/stat_popup'); ?>
<style>
.team-info{
    font-size:1.2em;
}
.team-bar{
    background: #1A4C80;
    margin: 0 auto;
    margin-top:20px;
    border:1px solid #bbb;
    background:#003380;
    -moz-box-shadow:0 1px 1px rgba(0,0,0,0.2);
    -webkit-box-shadow:0 1px 1px rgba(0,0,0,0.2);
    box-shadow:0 1px 1px rgba(0,0,0,0.2);
    height:7px;
}
</style>

<div class="container">

    <div class="row">
        <div class="col-sm-4 text-center">
            <h3><?=$team->long_name?></h3>
            <img class="med-logo team-logo" src="<?=$logo?>">
        </div>
        <div class="col-sm-8">
            <br>
            <br>
            <div class="team-bar"></div>
            <table class="table team-info">
                <tbody >
                    <tr>
                        <td>Owner: </td><td><?=$team->first_name.' '.$team->last_name?></td>
                    </tr>
                    <?php if($team->division_name): ?>
                    <tr>
                        <td>Division:</td>
                        <td><?=$team->division_name?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Record: </td><td><?=$record->wins?>-<?=$record->losses?>-<?=$record->ties?></td>
                    </tr>
                    <tr>
                        <td>Win %: </td><td><?=str_replace('0.0','.0',number_format($record->winpct,3))?></td>
                    </tr>
                    <tr>
                        <td>Points: </td><td><?=$record->points?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
    <div class="team-bar"></div>
    <br>
    <div class="row">
        <h3 class="text-center">Schedule</h3>
        <hr>
        <?php
              $cols = array();
              $cols[] = array_slice($schedule, 0, count($schedule) / 2);
              $cols[] = array_slice($schedule, count($schedule) / 2);
              ?>
        <?php foreach ($cols as $col):?>
            <div class="col-sm-6">
        <table class="table table-condensed, table-striped table-border" style="font-size:.8em">
            <thead>
                <th>Week</th>
                <th>Opponent</th>
                <th>Result</th>
            </thead>
            <tbody>
        <?php foreach($col as $key => $s): ?>
            <?php if($s->week == $this->session->userdata('current_week')):?>
                <tr style="background-color:#E0ECF8">
                <?php else:?>
                    <tr>
                <?php endif;?>

                <td><?=$s->week?></td>
                <?php if($s->home_id != $team_id): ?>
                    <td><a href="<?=site_url('league/teams/view/'.$s->home_id)?>">@<?=$s->home_name?></a></td>
                    <?php if($s->away_win == '1'):?>
                        <td>Win (<?=$s->away_score?> - <?=$s->home_score?>)</td>
                    <?php elseif ($s->away_win === '0'): ?>
                        <td>Loss (<?=$s->away_score?> - <?=$s->home_score?>)</td>
                    <?php else:?>
                        <td></td>
                    <?php endif;?>
                <?php else: ?>
                    <td><a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a></td>
                    <?php if($s->home_win == '1'):?>
                        <td>Win (<?=$s->home_score?> - <?=$s->away_score?>)</td>
                    <?php elseif ($s->home_win === '0'): ?>
                        <td>Loss (<?=$s->home_score?> - <?=$s->away_score?>)</td>
                    <?php else:?>
                        <td></td>
                    <?php endif;?>
                <?php endif;?>
            </tr>

        <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endforeach;?>
    </div>
    <br>
    <div class="row">
        <div class="col-sm-6">
                        <h3 class="text-center">Starters</h3>
                        <hr>

        <table class="table table-condensed table-striped">

            <thead>
                <th>Pos</th>
                <th>Name</th>
                <th>NFL Team</th>
                <th>Opp</th>
                <th>Points</th>
            </thead>
            <tbody>
                <?php foreach($starters as $p): ?>
                    <tr>
                        <td><?=$p['pos_text']?></td>
                        <?php if($p['player']):?>
                        <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p['player']->player_id?>"><?=$p['player']->first_name.' '.$p['player']->last_name?></a></td>
                        <td><?=$p['player']->club_id?></td>
                        <td class="text-xxs">
                            <div><?=$matchups[$p['player']->club_id]['opp']?></div>
                            <?php if($matchups[$p['player']->club_id]['time'] != ""):?>
                                <?php if(date("D",$matchups[$p['player']->club_id]['time']) == "Sun"): ?>
                                    <div><?=date("D g:i",$matchups[$p['player']->club_id]['time'])?></div>
                                <?php else: ?>
                                    <div><?=date("D g:i",$matchups[$p['player']->club_id]['time'])?></div>
                                <?php endif; ?>
                            <?php endif;?>
                        </td>
                        <td><?=$p['player']->points?></td>

                        <?php else: ?>
                            <td>-</td><td>-</td><td>-</td>
                        <?php endif;?>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        </div>
        <div class="col-sm-6">
            <h3 class="text-center">Bench</h3>
            <hr>
            <table class="table table-condensed table-striped">
                <thead>
                    <th>Pos</th>
                    <th>Name</th>
                    <th>NFL Team</th>
                    <th>Opp</th>
                    <th>Points</th>
                </thead>
                <tbody>
                    <?php foreach($bench as $p): ?>
                        <tr>
                            <td><?=$p->pos_text?></td>
                            <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p->player_id?>"><?=$p->first_name.' '.$p->last_name?></a></td>
                            <td><?=$p->club_id?></td>
                            <td class="text-xxs">
                                <div><?=$matchups[$p->club_id]['opp']?></div>
                                <?php if($matchups[$p->club_id]['time'] != ""):?>
                                    <?php if(date("D",$matchups[$p->club_id]['time']) == "Sun"): ?>
                                        <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                                    <?php else: ?>
                                        <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                                    <?php endif; ?>
                                <?php endif;?>
                            </td>
                            <td><?=$p->points?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>
