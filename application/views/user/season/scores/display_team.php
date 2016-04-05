
<div class="row">
	<h4>
	<div class="col-xs-9"><?=$team['team_name']?></div>
	<div class="text-right col-xs-3 teampoints_<?=$team['id']?>"><?=$team['points']?></div>
	</h4>
</div>
<table class="table table-condensed table-striped table-border">
<?php foreach ($team['players'] as $player): ?>
	<?php if (isset($player['points'])){$points = $player['points'];}else{$points = '-';}?>
	<?php if(stripos($player['pos'],'_D')===false){$postype='off_';}else{$postype='def_';}?>
<tr>
	<td class="col-xs-2">
		<?=$player['pos']?>
	</td>
	 <td class="col-xs-3">
		<a href="<?=site_url('league/players/id/'.$player['id'].'/'.$year)?>"><?=$player['name']?></a>
	</td>
	<td class="col-xs-5 <?=$postype.$player['club_id']?> inactive text-center">
		<?=$gsis[$player['club_id']]['match']?>
	</td>
	<td class = "col-xs-2 points_<?=$player['id']?> active_<?=$player['club_id']?> text-right text-l5">
		<?=$points?>
	</td>
</tr>
<?php endforeach; ?>
</table>
