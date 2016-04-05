<?php //print_r($weeks); ?>
<?php foreach($bench as $b): ?>
    <tr>
        <td>
            <div>
                <?php if(strlen($b['data']->first_name.$b['data']->last_name) > 12){$name = $b['data']->short_name; }
                      else{$name = $b['data']->first_name." ".$b['data']->last_name;} ?>

                <a href="#" class="stat-popup" data-type="player" data-id="<?=$b['data']->player_id?>"><?=$name?></a>
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
        <td class="hidden-xxs">
            <?=$b['data']->points?>
        </td>
        <td>
        <?php if ($matchups[$b['data']->club_id]['time'] > time() || $matchups[$b['data']->club_id]['time'] == '' || $this->session->userdata('league_id') == '5'): ?>
            <?php if (isset($b['can_start'])): ?>
                <?php foreach ($b['can_start'] as $pos_id => $can_pos): ?>
                    <button class="btn btn-default roster-start-btn" value="<?=$b['data']->player_id?>_<?=$pos_id?>">

                        <div class="">
                            <?=$can_pos?>
                        </div>
                    </button>
                <?php endforeach; ?>
            <?php else: ?>
                Full
            <?php endif; ?>
        <?php endif;?>
        </td>
    </tr>

<?php endforeach;?>
