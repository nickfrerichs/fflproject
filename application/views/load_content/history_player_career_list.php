<?php foreach($players as $num => $p): ?>
            <tr>
                <td><?=$num+1?></td>
                <td><?=$p->first_name." ".$p->last_name?></td>
                <td><?=$p->position?></td>
                <td><?=number_format($p->avg_points,1)?></td>
                <td><?=$p->total_points?></td>
                <td><?=$p->games?></td>
            </tr>
        <?php endforeach;?>