<?php foreach($roster as $r): ?>
    <tr>
        <td><?=$r->nfl_pos_text_id?></td>
        <td><?=$r->club_id?></td>
        <td><?=$r->first_name.' '.$r->last_name?></td>
        <td>
            <div class="switch">
              <input class="switch-input keeper-toggle" id="keeper-<?=$r->player_id?>" type="checkbox" <?php if($r->keeper){echo "checked";}?>>
              <label class="switch-paddle" for="keeper-<?=$r->player_id?>">
              </label>
            </div>
        </td>

    </tr>
<?php endforeach;?>
