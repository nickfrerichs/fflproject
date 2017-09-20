
<?php foreach($starters as $lea_pos_id => $starting_pos): ?>
    <?php if(!array_key_exists('players',$starting_pos)): ?>
        <?php continue; ?>
    <?php endif;?>
<?php foreach($starting_pos['players'] as $p): ?>
<?php if($p != null): ?>
    <tr>
        <td>
            <?=$starting_pos['pos']?>
        </td>
        <td>
        <div>
                <?php if(strlen($p->first_name.$p->last_name) > 12){$name = $p->short_name; }
                      else{$name = $p->first_name." ".$p->last_name;} ?>
            <?php if($p->keeper){echo "<strong>";} ?>
            <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->player_id?>"><?=$name?></a>
            <?php if($p->keeper){echo "</strong>";} ?>

            <?php if($p->injured):?>
                    <?php $this->load->view('common/injury_icon.php',array('short_text' => $p->injury_short_text,
                                                                           'injury' => $p->injury,
                                                                           'text_id' => $p->injury_text_id,
                                                                           'week' => $p->injury_week));?>
  
                <?php endif;?>
        </div>
        <div>
            <?=$p->pos_text?> - <?=$p->club_id?>
        </div>
        </td>
        <td>
            <div><?=$matchups[$p->club_id]['opp']?></div>
            <?php if ($matchups[$p->club_id]['time'] > time() || $matchups[$p->club_id]['time'] != ''): ?>
                <?php if(date("D",$matchups[$p->club_id]['time']) == "Sun"): ?>
                    <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                <?php else: ?>
                    <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                <?php endif; ?>
            <?php endif;?>
        </td>
        <td>
            <span class="hide-for-extra-small">Week </span><?=$byeweeks[$p->club_id]?>
        </td>
        <td class="hide-for-extra-small text-center">
            <?=$p->points?>
        </td>

        <td class="text-center">
            <?php if($matchups[$p->club_id]['time'] > time() || $matchups[$p->club_id]['time'] == ''): ?>
                <button class="button small roster-sit-btn" value="<?=$p->player_id?>">
                    <div>
                        Sit
                    </div>
                </button>
            <?php endif; ?>

        </td>
    </tr>
<?php else: ?>
    <tr>
        <td>
            <?=$starting_pos['pos']?>
        </td>
        <td>
        <div>
            <i>Vacant</i>
        </div>
        </td>
        <td>-</td>
        <td class="hide-for-extra-small">-</td><td class="text-center">-</td><td class="text-center">-</td>
    </tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>

<script>
$(".has-tip").foundation();
</script>
