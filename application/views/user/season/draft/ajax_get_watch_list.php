<?php foreach ($players as $key => $p): ?>
	<?php if($key == 0): ?>
		<tr class="aup-<?=$p->id?> adown-<?=$players[$key+1]->id?>">
	<?php elseif($key+1 == count($players)): ?>
		<tr class="adown-<?=$p->id?> aup-<?=$players[$key-1]->id?>">
	<?php else:?>
		<tr class="aup-<?=$p->id?> adown-<?=$p->id?> aup-<?=$players[$key-1]->id?> adown-<?=$players[$key+1]->id?>">
	<?php endif;?>
		<td style="vertical-align:middle; font-size:1.3em">
			<?=$p->order?>
		</td>
		<td>

		<div>
			<?php if($key != 0): ?>
			<a href="#" class="glyphicon glyphicon-arrow-up btn-draft up-test" data-value="up_<?=$p->id?>"></a>
			<?php else: ?>
				<span class="glyphicon glyphicon-option-horizontal"></span>
			<?php endif;?>
		</div>

		<div>
			<?php if(count($players) != $key+1):?>
			<a href="#" class="glyphicon glyphicon-arrow-down btn-draft" data-value="down_<?=$p->id?>"></a>
			<?php else: ?>
				<span class="glyphicon glyphicon-option-horizontal"></span>
			<?php endif;?>
		</div>
		</td>
		<td style="vertical-align:middle">

			<a href="#" class="glyphicon glyphicon-remove btn-draft" data-value="watch_<?=$p->id?>"></a>

			<!--
			<button class="btn btn-default btn-draft" value="watch_<?=$p->id?>">
				<span class="glyphicon glyphicon-remove"></span>
			</button>
		-->
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

			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> <span class="text-s2"></span></div>
			<div><?=$p->club_id.' - '.$p->position?></div>
		</td>

		<td>

			<?php //echo $draft_team_id; ?>
			<?php if($draft_team_id == $team_id && !$paused): ?>
			<button class="btn btn-primary btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
			<?php else: ?>
			<button class="btn btn-default btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
			<?php endif; ?>
		</td>

	</tr>
<?php endforeach; ?>
