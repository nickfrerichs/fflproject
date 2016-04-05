<?php foreach ($roster as $r): ?>
	<button class="btn btn-default ww-btn" type="button" value="<?=$r->player_id?>">
		<input type="radio" name="drop-player" value="<?=$r->player_id?>" autocomplete="off">
		<div><strong><div class="drop-player-name"><?=$r->first_name.' '.$r->last_name?></strong></div>
		<div><?=$r->club_id.' - '.$r->pos?></div>
	</button>
<?php endforeach; ?>