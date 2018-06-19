
<?php //print_r($bench); print_r($matchups);?>
<?php foreach($bench as $b): ?>
    <tr>
        <td>
            <div>
                <?php if(strlen($b['data']->first_name.$b['data']->last_name) > 12){$name = $b['data']->short_name; }
                      else{$name = $b['data']->first_name." ".$b['data']->last_name;} ?>
                <?php if($b['data']->keeper){echo "<strong>";} ?>
                <a href="#" class="stat-popup" data-type="player" data-id="<?=$b['data']->player_id?>"><?=$name?></a>
                <?php if($b['data']->keeper){echo "</strong>";} ?>
                <?php if($b['data']->injured):?>
                    <?php $this->load->view('common/injury_icon.php',array('short_text' => $b['data']->injury_short_text,
                                                                           'injury' => $b['data']->injury,
                                                                           'text_id' => $b['data']->injury_text_id,
                                                                           'week'   => $b['data']->injury_week));?>
  
                <?php endif;?>
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
         <td class="has-text-centered">
            <div class="column">
                <span class="is-hidden-mobile">Week</span> <?=$byeweeks[$b['data']->club_id]?>
            </div>
        </td>
        <td class="has-text-centered">
            <div class="column">
                <?=$b['data']->points?>
            </div>
        </td>
        <td>
  
            <div class="column has-text-centered">
                <?php if ($matchups[$b['data']->club_id]['time'] > time() || $matchups[$b['data']->club_id]['time'] == '' || $this->session->userdata('debug_week')): ?>
                    <?php if (isset($b['can_start'])): ?>
                        <?php foreach ($b['can_start'] as $pos_id => $can_pos): ?>
                                    <button class="button is-small is-link roster-start-btn" value="<?=$b['data']->player_id?>_<?=$pos_id?>">
                                        <?=$can_pos?>
                                    </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        Full
                    <?php endif; ?>
                <?php endif;?>
            </div>

        </td>
    </tr>

<?php endforeach;?>

