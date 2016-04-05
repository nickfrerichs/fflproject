<tr>
    <!--
    <td style="font-size:1.5em;">
        <!-- <?=$p['pos_text']?>
    </td>
    -->

    <?php if($p['player']->nfl_pos_type == 1 || ($p['player']->nfl_pos_type == 3))
            {$team_class = $p['player']->club_id.'_d';}
          else {$team_class = $p['player']->club_id.'_o';}
          $pid = $p['player']->player_id;?>


    <td>
        <?php if($p['player']): ?>
            <div style="display:inline-block;">
                <img src="<?=site_url('images/players/'.$p['player']->photo)?>" style="width:35px;">
            </div>
            <div style="display:inline-block;">
                <div>
                    <a class="xxs-text-6em text-xxs" href="<?=site_url('league/players/id/'.$p['player']->player_id)?>"><?=$p['player']->short_name?></a>
                </div>
                <div class="xxs-text-6em text-xxs">
                    <?=$p['player']->club_id?> - <?=$p['player']->nfl_pos?> <?php if($p['player']->number){echo "#".$p['player']->number;}?>
                </div>
            </div>
        <?php endif; ?>
    </td>
    <td class='livescore-status sm-livescore-status'>
        <p class="p-<?=$pid?> <?=$team_class?> ls-status" data-playid="0" data-playerid="<?=$pid?>">
            -
        </p>
    </td>
    <td style="font-size:1.2em;" class="pscore-<?=$pid?> ls-pscore <?=$team_class?>" >-</td>
</tr>
