<?php foreach ($roster as $p): ?>
	<tr class="drop-player" data-drop-id="<?=$p->id?>" data-drop-name="<?=$p->first_name.' '.$p->last_name?>">
        <!--
		<td>
			<button class="btn btn-default drop-player" data-drop-id="<?=$p->id?>"
                data-drop-name="<?=$p->first_name.' '.$p->last_name?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
		</td>
    -->
		<td>
	        <?php if(strlen($p->first_name.$p->last_name) > 12){$name = $p->short_name; }
	              else{$name = $p->first_name." ".$p->last_name;} ?>
	        <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$name?></a>
		</td>
		<td>
	        <?=$p->position?> - <?=$p->club_id?>
		</td>
		<td>
			<button class="drop-player btn btn-default" data-drop-id="<?=$p->id?>" data-drop-name="<?=$p->first_name.' '.$p->last_name?>">Drop</button>
    	</td>
	</tr>
<?php endforeach; ?>
<?php if(count($roster) < $roster_max): ?>
<tr class="drop-player" data-drop-id="0" data-drop-name="No One">
    <!--
    <td>
    <button class="btn btn-default drop-player" data-drop-id="0"

                data-drop-name="No One"><span class="glyphicon glyphicon-remove" aria-hidden="true"></button>
    </td>
    -->
    <td colspan=2>
        No One
	</td>
	<td>
	<button class="drop-player btn btn-default" data-drop-id="0" data-drop-name="No One">Drop no one</button>
    </td>
</tr>
<?php endif;?>
