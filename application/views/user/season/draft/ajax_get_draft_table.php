<?php foreach ($players as $p): ?>
	<tr>
		<td>
			<?=$p->rank?>
		</td>
		<td>
			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> </div>
			<!-- <div class="show-for-small-only"><strong><span class="selected-player-name"><?=$p->first_name[0].'.'.$p->last_name?></span></strong></div> -->
		</td>
		<td>
			<?=$p->club_id?>
		</td>
		<td>
			<?=$p->position?>
		</td><td>
			<span style="font-size: .7em">(bye <?=$byeweeks[$p->club_id]?>)</span></div>
		</td>
		<?php if($p->team_name == ""): ?>
		<td style="vertical-align:middle" class="draft-avail-<?=$p->id?>">

			<?php if($p->watched == ""): ?>
			<!-- <a href="#" class="btn-draft glyphicon glyphicon-eye-open" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>"></a> -->
			<!-- <a href="#" class="btn-draft" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">watch</a> -->
			<button class="button is-small is-link btn-draft" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">Watch</button>
			<?php else:?>
				<span style='font-size:.8em;color:#AAAAAA'>Watching</span>
			<?php endif; ?>


			<?php if(($draft_team_id == $team_id && !$paused) || $admin_pick): ?>
			<button class="button btn-draft is-small is-link" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="button btn-draft is-small is-link" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>

		</td>
		<?php else: ?>
		<td class="vert"><?=$p->team_name?></td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>




