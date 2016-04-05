<?php //print_r($schedule); ?>

<div class="container">
	<div class="row">
		<div class="col-xs-12" style="padding:25px;">
			<a href="<?=site_url('admin/schedule/edit')?>">Edit schedule</a> <br>
			<a href="<?=site_url('admin/schedule/create')?>">Create schedule</a> <br>
			<a href="<?=site_url('admin/schedule/template')?>">Manage templates</a> <br>
			<a href="<?=site_url('admin/schedule/gametypes')?>">Manage Game Types</a> <br>
		</div>
	</div>

	<div class="row">
		<?php foreach ($schedule as $week_num => $week): ?>
		<div class="col-md-6">
			<div><strong>Week <?=$week_num?></strong></div>
			<table class="table table-condensed">
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

</div>
   