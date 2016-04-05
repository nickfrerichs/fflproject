<?php foreach($starters as $lea_pos_id => $starting_pos): ?>
<?php foreach($starting_pos['players'] as $p): ?>
<?php if($p != null): ?>
    <tr>
        <td class="vert">
            <h4><?=$starting_pos['pos']?></h4>
        </td>
        <td>
        <div>
                <?php if(strlen($p->first_name.$p->last_name) > 12){$name = $p->short_name; }
                      else{$name = $p->first_name." ".$p->last_name;} ?>
            <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->player_id?>"><?=$name?></a>
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
        <td class="hidden-xxs vert">
            <?=$p->points?>
        </td>

        <td class="vert">
            <?php if($matchups[$p->club_id]['time'] > time() || $matchups[$p->club_id]['time'] == '' || $this->session->userdata('league_id') == '5'): ?>
                <button class="btn btn-default roster-sit-btn" value="<?=$p->player_id?>">
                    <div class="">
                        Sit
                    </div>
                </button>
            <?php endif; ?>

        </td>
    </tr>
<?php else: ?>
    <tr>
        <td class="vert">
            <h4><?=$starting_pos['pos']?></h4>
        </td>
        <td>
        <div>
            <i>Vacant</i>
        </div>
        </td>
        <td class="hidden-xxs">-</td><td>-</td><td>-</td>
    </tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>
