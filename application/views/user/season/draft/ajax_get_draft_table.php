<?php foreach ($players as $p): ?>
	<tr>
		<td>
			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> <span class="text-s2"></span></div>
			<div><?=$p->club_id.' - '.$p->position?></div>
		</td>
		<?php if($p->team_name == ""): ?>
		<td style="vertical-align:middle">
			<?php if($p->watched == ""): ?>
			<!-- <a href="#" class="btn-draft glyphicon glyphicon-eye-open" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>"></a> -->
			<!-- <a href="#" class="btn-draft" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">watch</a> -->
			<button class="btn btn-default btn-draft" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">Watch</button>
			<?php else:?>
				<span style='font-size:.8em;color:#AAAAAA'>Watching</span>
			<?php endif; ?>

		</td>
		<td>
			<?php if($draft_team_id == $team_id && !$paused): ?>
			<button class="btn btn-primary btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="btn btn-default btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>
		</td>
		<?php else: ?>
		<td colspan="2" class="vert"><?=$p->team_name?></td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>
<?php $high = (($page+1)*$per_page); ?>
<?php $low = $high-($per_page-1); ?>
<?php if($high > $total_players){$high = $total_players;}?>
<tr>
	<td colspan="3">
	<span id="count-current"><?=$low?> - <?=$high?> of </span><span id="count-total"><?=$total_players?></span>
	</td>
</tr>
