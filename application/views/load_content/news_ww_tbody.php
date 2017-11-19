
<?php if (count($result) > 0): ?>
    <?php foreach($result as $i=>$w): ?>
        <tr>
        <td><?=date("M j g:i a",$w->transaction_date)?></td>
        <td><?=$w->team_name?></td>
        <td><?=$w->pickup_short_name?> <?=$w->pickup_pos?> <?=$w->pickup_club_id?></td>
        <td><?=$w->drop_short_name?> <?=$w->drop_pos?> <?=$w->drop_club_id?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td style='font-style:italic' colspan=4>Nothing to report</td></tr>
<?php endif;?>