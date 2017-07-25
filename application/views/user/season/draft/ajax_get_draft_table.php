<?php foreach ($players as $p): ?>
	<tr>
		<td>
			<div class="hide-for-small-only"><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> </div>
			<div class="show-for-small-only"><strong><span class="selected-player-name"><?=$p->first_name[0].'.'.$p->last_name?></span></strong></div>
			<div><?=$p->club_id?> - <?=$p->position?> <span style="font-size: .7em">(bye <?=$byeweeks[$p->club_id]?>)</span></div>
		</td>
		<?php if($p->team_name == ""): ?>
		<td style="vertical-align:middle" class="draft-avail-<?=$p->id?>">
		<div class="row">
			<div class="columns">
			<?php if($p->watched == ""): ?>
			<!-- <a href="#" class="btn-draft glyphicon glyphicon-eye-open" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>"></a> -->
			<!-- <a href="#" class="btn-draft" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">watch</a> -->
			<button class="button tiny btn-draft" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">Watch</button>
			<?php else:?>
				<span style='font-size:.8em;color:#AAAAAA'>Watching</span>
			<?php endif; ?>
			</div>

			<div class="columns">

			<?php if(($draft_team_id == $team_id && !$paused) || $admin_pick): ?>
			<button class="button btn-draft tiny" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="button btn-draft tiny" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>
			</div>
		</div>
		</td>
		<?php else: ?>
		<td class="vert"><?=$p->team_name?></td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>



<tr id="draft-list-data" class="hide" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$total_players?>">
</tr>
