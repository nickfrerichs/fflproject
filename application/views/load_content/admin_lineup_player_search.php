
<?php foreach ($players as $p): ?>
    <tr>
        <td><?=$p->first_name?> <?=$p->last_name?></td>
        <td><?=$p->position?></td>
        <td><?=$p->club_id?></td>
        <td>
            <?php foreach($pos_lookup as $posid => $pl): ?>
                <?php if(in_array($p->nfl_position_id, explode(",",$pl['list']))): ?>
                    <button class="button is-small is-link admin-start-button" data-id="<?=$p->player_id?>" data-posid="<?=$posid?>"><?=$pl['pos_text']?></button>
                <?php endif;?>
            <?php endforeach;?>
        </td>
    </tr>
<?php endforeach;?>
