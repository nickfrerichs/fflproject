<?php //print_r($players); ?>
<?php foreach ($players as $p): ?>
	<tr>
		<td>
			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> <span class="text-s2"></span></div>
		</td>
		<td>
			<div><?=$p->club_id.' - '.$p->position?></div>
		</td>
		<td>
			<span>Week <?=$byeweeks[$p->club_id]?></span>
		</td>
		<td>
			<?=$p->actual_pick?>
		</td>
		<td class="hide-for-extra-small">
			Rd: <?=$p->round?> Pick: <?=$p->pick?>
		</td>

	</tr>
<?php endforeach; ?>
