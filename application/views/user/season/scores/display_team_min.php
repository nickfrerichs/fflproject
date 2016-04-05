

<div class="row">
	<h4>
	<div class="col-xs-9"><?=$team['team_name']?></div>
	<div class="text-right col-xs-3 teampoints_<?=$team['id']?>"><?=$team['points']?></div>
	</h4>
</div>

<table class="table table-striped table-condensed table-border">
<?php foreach($team['players'] as $p): ?>
<?php if (isset($p['points'])){$points = $p['points'];}else{$points = '-';}?>
<tr>
	<td class="col-xs-2 text-l1">
		<?=$p['pos']?>
	</td>
	<td class="col-xs-8 text-l1">
		<a href="<?=site_url('league/players/id/'.$p['id'].'/'.$year)?>"><?=$p['name']?></a>
	</td>
	<td class="points_<?=$p['id']?> active_<?=$p['club_id']?> col-xs-2 text-right text-l1">
		<?=$points?>
	</td>
</tr>
<?php endforeach; ?>
</table>
