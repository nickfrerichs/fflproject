<?php foreach($teams as $num => $t): ?>
    <tr>
        <td><?=$num+1?></td>
        <td><div><?=$t->first_name." ".$t->last_name?></div><div style="font-size:.8em"><?=$t->team_name?></div></td>
        <td><?=str_replace('0.','.',number_format($t->win_pct,3))?></td>
        <td><?=number_format($t->avg_points,2)?> / <?=number_format($t->avg_opp_points,2)?> <span style="font-size:.7em"> <?=number_format($t->avg_diff,2)?></span></td>
        <td><?=$t->wins?>-<?=$t->losses?>-<?=$t->ties?></td>
        <td><?=$t->points?></td>
        <td><?=$t->opp_points?></td>

    </tr>
<?php endforeach;?>