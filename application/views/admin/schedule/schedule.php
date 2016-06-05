<?php //print_r($schedule); ?>


	<div class="row">
		<div class="columns small-12">
			<a href="<?=site_url('admin/schedule/edit')?>">Manage schedule</a> <br>
			<a href="<?=site_url('admin/schedule/create')?>">Create schedule from template</a> <br>
		</div>
	</div>
	<br>
	<div class="row">
		<?php foreach ($schedule as $week_num => $week): ?>
		<div class="columns medium-6 small-12">
			<div><strong>Week <?=$week_num?></strong></div>
			<table class="">
			    <th>Home</th><th></th><th>Away</th><th>Game Type</th>
			    <?php foreach ($week as $game):?>

			    <tr>
			        <td><?=$game['home']?></td>
			        <td>at</td>
			        <td><?=$game['away']?></td>
			        <td><?=$game['type']?></td>
			    </tr>
			    <?php endforeach; ?>
			</table>
		</div>
		<?php endforeach; ?>
	</div>
