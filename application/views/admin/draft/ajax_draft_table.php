<?php //print_r($draft_rounds); ?>
<?php foreach($draft_rounds as $r): ?>
<tr>
	<td>
		<?php if($r->p_id == ""): ?>
			<a class="delete-pick" data-id="<?=$r->id?>">Del</a>
		<?php endif;?>
	</td>
	<td><?=$r->pick?></td>
	<td><?=$r->overall_pick?></td>
	<td><?=$r->team_name?></td>
	<?php if($r->p_id == ""): ?>
	<td></td>
	<?php else: ?>
	<td><?=$r->p_first_name.' '.$r->p_last_name?></td>
	<?php endif; ?>
</tr>
<?php endforeach; ?>
