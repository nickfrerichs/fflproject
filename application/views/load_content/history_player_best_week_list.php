<?php foreach($players as $num => $p): ?>
    <tr>
        <td><?=$num+1?></td>
        <td><?=$p->first_name." ".$p->last_name?></td>
        <td><?=$p->position?></td>
        <td><?=$p->points?></td>
        <td><?=$p->week?></td>
        <td><?=$p->year?></td>
        <td><?=$p->owner_first_name.' '.$p->owner_last_name?></td>
    </tr>
<?php endforeach;?>