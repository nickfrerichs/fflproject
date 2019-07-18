<div class="f-scrollbar">
    <table class="table is-fullwidth f-min-width-small is-size-7-mobile">
        <thead>
            <th class="text-center">Pos</th><th class="text-center">Player</th><th></th>
        </thead>
        <tbody>
            <?php foreach ($roster as $r): ?>
            <tr>
                <td><?=$r->pos?></td>
                <td>
                <div>
                        <?php if(strlen($r->first_name.$r->last_name) > 12){$name = $r->short_name; }
                                else{$name = $r->first_name." ".$r->last_name;} ?>
                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$r->player_id?>"><?=$name?></a> - <?=$r->club_id?>
                </div>
                </td>
                <td>
                    <button class="button offer-btn is-small is-link" value="<?=$r->player_id?>" data-name="<?=$name?>">Select</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>