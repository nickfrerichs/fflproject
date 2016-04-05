
<?php $this->load->view('user/season/scores/nav',
        array('years' => $years, 'year' =>$year, 'week' => $week));?>

<div class="container">
<div class="page-heading text-center">Week <?=$week?></div>

<?php if (count($schedule) > 0): ?>

<table class="table light-bg table-condensed table-striped text-l2">
	<thead>
	</thead>
	<tbody>
	<?php foreach($schedule as $s): ?>
	<tr>
		<td class="text-center"><?=$s->home_name?></td>
		<td class="text-center">vs</td>
		<td class="text-center"><?=$s->away_name?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
	<div class="page-heading text-center">No games scheduled.</div>
<?php endif; ?>
</div>
