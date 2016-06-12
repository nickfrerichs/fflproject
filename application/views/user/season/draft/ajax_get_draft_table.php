<?php foreach ($players as $p): ?>
	<tr>
		<td>
			<div class="hide-for-small-only"><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> </div>
			<div class="show-for-small-only"><strong><span class="selected-player-name"><?=$p->first_name[0].'.'.$p->last_name?></span></strong> </div>
			<div><?=$p->club_id?> - <?=$p->position?></div>
		</td>
		<?php if($p->team_name == ""): ?>
		<td style="vertical-align:middle">
			<?php if($p->watched == ""): ?>
			<!-- <a href="#" class="btn-draft glyphicon glyphicon-eye-open" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>"></a> -->
			<!-- <a href="#" class="btn-draft" style:"font-size:2em" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">watch</a> -->
			<button class="button tiny btn-draft" value="watch_<?=$p->id?>" data-value="watch_<?=$p->id?>">Watch</button>
			<?php else:?>
				<span style='font-size:.8em;color:#AAAAAA'>Watching</span>
			<?php endif; ?>

		</td>
		<td>
			<?php if($draft_team_id == $team_id && !$paused): ?>
			<button class="button btn-draft tiny" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="button btn-draft tiny" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>
		</td>
		<?php else: ?>
		<td colspan="2" class="vert"><?=$p->team_name?></td>
		<?php endif; ?>
	</tr>
<?php endforeach; ?>



<tr id="draft-list-data" class="hide" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$total_players?>">
</tr>
