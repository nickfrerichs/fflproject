<?php //print_r($players); ?>
<?php foreach($players as $p):?>
    <tr>
        <td><?=$p->last_name.", ".$p->first_name?></td>
        <td><?=$p->position?></td>
        <td><?=$p->points?></td>
        <td><?=$p->club_id?></td>
        <td><?=$p->team_name?></td>
    </tr>
<?php endforeach; ?>
<tr id="main-list-data" data-page="<?=$page?>" data-perpage="<?=$per_page?>" data-total="<?=$total?>">
</tr>
