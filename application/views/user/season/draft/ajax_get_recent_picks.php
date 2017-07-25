<?php //This file is no Longer needed?? ?>
<?php if (!empty($current_pick)): ?>
<tr id="recent-top-row" data-pickid="<?=$current_pick->pick_id?>">
	<td>#<?=$current_pick->actual_pick?></td>
	<td class="pick-round-font"><?=$current_pick->round?>-<?=$current_pick->pick?> </td>
	<td>???</td>
	<td><?=$current_pick->team_name?></td>
	<td><?=$current_pick->owner?></td>

</tr>
<?php endif;?>
<?php foreach($picks as $pick): ?>
<tr>
	<td>
		#<?=$pick->actual_pick?>
	</td>
	<td class="pick-round-font">
		<?=$pick->round?>-<?=$pick->pick?>
	</td>
	<td>
		<div><?=$pick->first_name.' '.$pick->last_name?> (<?=$pick->club_id.' - '.$pick->position?>)</div>
	</td>
	<td>
		<?=$pick->team_name?>
	</td>
	<td>
		<?=$pick->owner?>
	</td>
</tr>
<?php endforeach; ?>
