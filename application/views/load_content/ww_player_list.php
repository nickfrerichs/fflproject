<?php foreach($players as $p):?>
    <?php if ($p->clear_time)
    {
        $remaining = $p->clear_time - time();
        $hr = (int)($remaining / (60*60));
        $min = (int)(($remaining - $hr*(60*60)) / 60);
        $sec = (int)(($remaining - $hr*(60*60) - $min*60));
    }
    ?>
    <tr class="pickup-player" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">
        <td><?=$p->position?></td>
        <td>
            <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a>
        <?php if($p->injured)
        {
            $this->load->view('common/injury_icon',array('short_text' => $p->injury_short_text,
                                                                    'injury' => $p->injury,
                                                                    'text_id' => $p->injury_text_id,
                                                                    'week'  => $p->injury_week));
                                                                    
        }
        ?>
        </td>
        <td><?=$p->club_id?></td>
        <td class="hide-for-extra-small"><?=$matchups[$p->club_id]['opp']?></td>
        <td><span class="hide-for-small-only">Week </span><?=$byeweeks[$p->club_id]?></td>
        <td><?=$p->points?></td>
        <td class="text-center" style="width:17%">
            <?php if($p->clear_time): ?>
            <button <?=($p->requested ? "disabled" : "")?> class="player-pickup button is-link is-small" data-clear="no" data-pickup-id="<?=$p->id?>"
                data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">Pickup (<?=$hr?>h:<?=$min?>m)</button>
            <?php else: ?>
                <button class="player-pickup button is-link is-small" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">Pickup</button>
            <?php endif;?>
        </td>
    </tr>
<?php endforeach; ?>

