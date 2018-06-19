<?php foreach ($roster as $p): ?>
	<tr class="drop-player" data-drop-id="<?=$p->id?>" data-drop-name="<?=$p->first_name.' '.$p->last_name?>">
		<td>
		<?php if(strlen($p->first_name.$p->last_name) > 12){$name = $p->short_name; }
			else{$name = $p->first_name." ".$p->last_name;} ?>
		<a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$name?></a>
		</td>
		<td>
		<?=$p->position?> - <?=$p->club_id?>
		</td>
		<td class="has-text-centered">
			<button class="drop-player button is-small is-link" data-drop-id="<?=$p->id?>" data-drop-name="<?=$p->first_name.' '.$p->last_name?>">Drop</button>
	</td>
	</tr>
<?php endforeach; ?>
<?php if(count($roster) < $roster_max || $roster_max == -1): ?>
	<tr class="drop-player" data-drop-id="0" data-drop-name="No One">
	<td colspan=2>
		No One
		</td>
		<td class="has-text-centered">
		<button class="drop-player button is-link is-small" data-drop-id="0" data-drop-name="No One">Drop no one</button>
	</td>
	</tr>
<?php endif;?>
