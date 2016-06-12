
<?php if($p['player']): ?>

<tr id="player-<?=$p['player']->player_id?>" data-teamplayid="0" data-playerplayid="0" data-team="<?=$p['teamclass']?>">

    <td class="player-profile">
        <div style="float:left;">
            <?php if($p['player']->photo): ?>
                <img src="<?=site_url('images/'.$p['player']->photo)?>" style="width:40px;">
            <?php endif;?>
        </div>
        <div style="float:left;">
            <div>
                <a href="#" class="xxs-text-6em text-xxs stat-popup" data-type="player" data-id="<?=$p['player']->player_id?>"><?=$p['player']->short_name?></a>
            </div>
            <div class="xxs-text-6em text-xxs">
                <?=$p['player']->club_id?> - <?=$p['player']->nfl_pos?> <?php if($p['player']->number){echo "#".$p['player']->number;}?>
            </div>
        </div>
    </td>

    <td class='player-status sm-livescore-status livescore-font-size'>
    </td>
    <td id="player-<?=$p['player']->player_id?>-score" class="player-score livescore-font-size text-right" >-</td>

</tr>
<?php else: ?>
    <tr <?php if($view == 'live'){echo 'class="livescore-hide-player"';}?>><td colspan=3 class="player-profile">
        <div style="float:left;">
            <img src="<?=site_url('images/nfl/JAC.png')?>" style="width:40px;">
        </div>
    </td></tr>
<?php endif;?>
