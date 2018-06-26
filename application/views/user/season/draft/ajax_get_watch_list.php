<?php if(count($players) == 0):?>
	<tr><td class="text-center" style="padding-top:160px; padding-bottom:160px;">You aren't watching any players</td></tr>
<?php else: ?>
<?php foreach ($players as $key => $p): ?>
	<tr class="watch-avail-<?=$p->id?>">
		<td class="text-center">
			<?=$p->order?>
		</td>
		<td>
			<!-- <div>
				<?php //if($key != 0 || ($this->in_page!=""&&$this->in_page!=0)): ?>
				<a href="#" class="btn-draft" data-value="up_<?=$p->id?>"><i class="fi-arrow-up" style="font-size:1.2em"></i></a>
				<?php //else: ?>
					<span class=""></span>
				<?php //endif;?>
			</div>

			<div>
				<?php //if(count($players) != $key+1 || $this->in_page != floor($total_players/$this->per_page)):?>
				<a href="#" class="btn-draft" data-value="down_<?=$p->id?>"><i class="fi-arrow-down" style="font-size:1.2em"></i></a>
				<?php //else: ?>
					<span class=""></span>
				<?php //endif;?>
			</div> -->
		</td>

		<td>
			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong></div>
			<div><?=$p->club_id.' - '.$p->position?> <span style="font-size: .8em">(bye <?=$byeweeks[$p->club_id]?>)</span></div>
		</td>
		<td>
			<button class="button is-small is-link btn-draft" data-value="watch_<?=$p->id?>">
				Unwatch
			</button>
		</td>
		<td>

			<?php //if($draft_team_id == $team_id && !$paused): ?>
			<button class="button is-small is-link btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php //else: ?>
			<button class="button is-small is-link btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php //endif; ?>

		</td>

	</tr>

<?php endforeach; ?>
<!-- <tr id="watch-list-data" class="hide" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$total_players?>">
</tr> -->
<?php endif;?>

<script>
//$(".reload-foundation").foundation();
</script>
