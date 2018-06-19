
<?php foreach($starters as $lea_pos_id => $starting_pos): ?>
    <?php if(!array_key_exists('players',$starting_pos)): ?>
        <?php continue; ?>
    <?php endif;?>
<?php foreach($starting_pos['players'] as $p): ?>
<?php if($p != null): ?>
    <tr>
        <td>
        <div class="column has-text-centered">
            <?=$starting_pos['pos']?>
        </div>
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
            <?=$matchups[$p->club_id]['opp']?>
            <?php if ($matchups[$p->club_id]['time'] > time() || $matchups[$p->club_id]['time'] != ''): ?>
                <?php if(date("D",$matchups[$p->club_id]['time']) == "Sun"): ?>
                    <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                <?php else: ?>
                    <div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
                <?php endif; ?>
            <?php endif;?>
        </td>
        <td>
            <div class="column has-text-centered">
                <span class="is-hidden-mobile"> Week </span><?=$byeweeks[$p->club_id]?>
            </div>
        </td>
        <td class="is-hidden-mobile">
        <div class="column">
            <?=$p->points?>
            </div>
        </td>

        <td>
            <div class="column has-text-centered">
            <?php if($matchups[$p->club_id]['time'] > time() || $matchups[$p->club_id]['time'] == '' || $this->session->userdata('debug_week')): ?>
                <button class="button is-small is-link roster-sit-btn" value="<?=$p->player_id?>">
                        Sit
                </button>
            <?php endif; ?>
            </div>
            
        </td>
    </tr>
<?php else: ?>
    <tr>
        <td>
            <div class="column has-text-centered">
            <?=$starting_pos['pos']?>
            </div>
        </td>
        <td>
        <div class="column">
            <i>Vacant</i>
        </div>
        </td>
        <td>
            <div class="column">-</div>
        </td>
        <td class="is-hidden-mobile">
            <div class="column">-</div>
        </td>
        <td class="has-text-centered">
            <div class="column">-</div>
        </td>
        <td class="has-text-centered">
            <div class="column">-</div>
        </td>
    </tr>
<?php endif; ?>
<?php endforeach; ?>
<?php endforeach; ?>


