<?php if (count($messages) == 0): ?>
	<tr><td>No Messages</td></tr>
<?php else: ?>

<?php foreach($messages as $m): ?>
	<tr id="<?=$m->id?>" style="cursor:pointer;" class="<?php if($m->read==false){echo 'unread';}?>">
		<td><?=$m->first_name.' '.$m->last_name?></td>
		<td><?=$m->subject?></td>
		<td><?=date("D, M j - g:i a",$m->unix_date)?></td>
	</tr>
<?php endforeach; ?>
<?php endif; ?>
