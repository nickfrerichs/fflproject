
<?php foreach($players as $p):?>
    <tr>
        <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a></td>
        <td><?=$p->position?></td>
        <td><?=$p->club_id?></td>
        <?php if($p->team_name): ?>
        <td><?=$p->team_name?></td>
        <?php else: ?>
            <td><button class="button is-small is-link add-button" data-id="<?=$p->id?>" data-name = "<?=$p->first_name.' '.$p->last_name?>">add</button></td>
        <?php endif;?>
    </tr>
<?php endforeach; ?>
<!-- <tr id="main-list-data" data-page="<?=$in_page?>" data-perpage="<?=$per_page?>" data-total="<?=$total?>">
</tr> -->
