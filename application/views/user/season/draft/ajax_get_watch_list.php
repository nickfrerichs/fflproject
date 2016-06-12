<?php if(count($players) == 0):?>
	<tr><td class="text-center" style="padding-top:160px; padding-bottom:160px;">You aren't watching any players</td></tr>
<?php else: ?>
<?php foreach ($players as $key => $p): ?>
	<?php if (array_key_exists($key+1, $players)){$dn_id = $players[$key+1]->id;}else{$dn_id = "";} ?>
	<?php if (array_key_exists($key-1, $players)){$up_id = $players[$key-1]->id;}else{$up_id = "";} ?>

	<?php if($key == 0 && array_key_exists($key+1, $players)): ?>
		<tr class="aup-<?=$p->id?> adown-<?=$dn_id?>">
	<?php elseif(array_key_exists($key+1, $players) && $key+1 == count($players)): ?>
		<tr class="adown-<?=$p->id?> aup-<?=$up_id?>">
	<?php else:?>
		<tr class="aup-<?=$p->id?> adown-<?=$p->id?> aup-<?=$up_id?> adown-<?=$dn_id?>">
	<?php endif;?>
		<td class="text-center">
			<?=$p->order?>
		</td>
		<td>
			<div>
				<?php if($key != 0): ?>
				<a href="#" class="btn-draft up-test" data-value="up_<?=$p->id?>"><i class="fi-arrow-up" style="font-size:1.2em"></i></a>
				<?php else: ?>
					<span class=""></span>
				<?php endif;?>
			</div>

			<div>
				<?php if(count($players) != $key+1):?>
				<a href="#" class="btn-draft" data-value="down_<?=$p->id?>"><i class="fi-arrow-down" style="font-size:1.2em"></i></a>
				<?php else: ?>
					<span class=""></span>
				<?php endif;?>
			</div>
		</td>

		<?php if (count($players) == 1): ?>
			<td>
		<?php elseif($key == 0): ?>
			<td class="up-<?=$p->id?> down-<?=$players[$key+1]->id?>">
		<?php elseif($key+1 == count($players)): ?>
			<td class="down-<?=$p->id?> up-<?=$players[$key-1]->id?>">
		<?php else:?>
			<td class="up-<?=$p->id?> down-<?=$p->id?> up-<?=$players[$key-1]->id?> down-<?=$players[$key+1]->id?>">
		<?php endif;?>

			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong></div>
			<div><?=$p->club_id.' - '.$p->position?></div>
		</td>
		<td>
			<button class="button tiny btn-draft" data-value="watch_<?=$p->id?>">
				Unwatch
			</button>
		</td>
		<td>

			<?php if($draft_team_id == $team_id && !$paused): ?>
			<button class="button tiny btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="button tiny btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>

		</td>

	</tr>

<?php endforeach; ?>
<tr id="watch-list-data" class="hide" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$total_players?>">
</tr>
<?php endif;?>

<script>
$(".reload-foundation").foundation();
</script>
