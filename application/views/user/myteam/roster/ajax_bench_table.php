
<?php foreach($bench as $b): ?>
    <tr>
        <td>
            <div>
                <?php if(strlen($b['data']->first_name.$b['data']->last_name) > 12){$name = $b['data']->short_name; }
                      else{$name = $b['data']->first_name." ".$b['data']->last_name;} ?>
                <?php if($b['data']->keeper){echo "<strong>";} ?>
                <a href="#" class="stat-popup" data-type="player" data-id="<?=$b['data']->player_id?>"><?=$name?></a>
                <?php if($b['data']->keeper){echo "</strong>";} ?>
            </div>
            <div>
                <?=$b['data']->pos_text.' - '.$b['data']->club_id?>
            </div>
         </td>
         <td>
             <div><?=$matchups[$b['data']->club_id]['opp']?></div>
             <?php if($matchups[$b['data']->club_id]['time'] != ""):?>
                 <?php if(date("D",$matchups[$b['data']->club_id]['time']) == "Sun"): ?>
                     <div><?=date("D g:i",$matchups[$b['data']->club_id]['time'])?></div>
                 <?php else: ?>
                     <div><?=date("D g:i",$matchups[$b['data']->club_id]['time'])?></div>
                 <?php endif; ?>
            <?php endif;?>
         </td>
        <td class="hide-for-extra-small text-center">
            <?=$b['data']->points?>
        </td>
        <td>
            <div class="row align-center">
                <div class="columns text-center">
                    <?php if ($matchups[$b['data']->club_id]['time'] > time() || $matchups[$b['data']->club_id]['time'] == '' || 1==1): ?>
                        <?php if (isset($b['can_start'])): ?>
                            <?php foreach ($b['can_start'] as $pos_id => $can_pos): ?>
                                        <button class="button small roster-start-btn" value="<?=$b['data']->player_id?>_<?=$pos_id?>">
                                            <?=$can_pos?>
                                        </button>
                            <?php endforeach; ?>
                        <?php else: ?>
                            Full
                        <?php endif; ?>
                    <?php endif;?>
                </div>
            </div>
        </td>
    </tr>

<?php endforeach;?>
