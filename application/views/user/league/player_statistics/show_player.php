<?php //print_r($player);?>
<div class="row">
    <div class="columns">
        <div class="row">
            <div class="columns small-2">
                <?php if($player->photo):?>
                    <img src="<?=site_url('images/'.$player->photo)?>" class="table-border" style="width:125px;">
                <?php endif;?>
            </div>
            <div class="columns small-10">
                <h4><a target="_blank" href="<?=$player->profile_url?>">
                    <?=$player->first_name.' '.$player->last_name?> - <?=$player->pos?> <?php if($player->number){echo "#".$player->number;}?>
                </a></h4>
                <h5>NFL Team: <?=$player->club_id?></h5>
                <h5>
                    <?php if($player->team_id):?>
                        <a href="<?=site_url('league/teams/view/'.$player->team_id)?>">Fantasy Team: <?=$player->team_name?></a>
                    <?php else: ?>
                        Fantasy Team: <?=$player->team_name?>
                    <?php endif;?>
                </h5>
                <h6>Draft Round: <?=$player->round?><?php if($player->draft_pick){echo ' (#'.$player->draft_pick.' overall)';}?></h6>
            </div>
        </div>
        <div class="row">
            <div class="columns">
                <table class="table table-condensed table-striped">
                    <th>Week</th>
                    <th>Opponent</th>
                    <th>Total</th>
                    <?php foreach($cats as $cat): ?>
                    <th><?=$cat?></th>
                    <?php endforeach;?>
                <?php foreach($stats as $week => $w): ?>
                    <?php if ($week == $this->session->userdata('current_week')): ?>
                        <tr style="background-color:#E0ECF8">
                    <?php else:?>
                        <tr>
                    <?php endif;?>
                        <td><b><?=$week ?></b></td>
                        <td><?=$w['opp']?></td>
                        <td>
                            <b><?=$w['total']['value']?></b>
                        </td>
                        <?php foreach($cats as $id => $cat): ?>
                        <td title="Points: <?=$w[$id]['points']?>">
                            <?=$w[$id]['value']?>
                        </td>
                        <?php endforeach; ?>

                    </tr>
                <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
