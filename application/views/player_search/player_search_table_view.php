<div class="row">
	<div class="btn-group col-xs-12" data-toggle="buttons" >

	<?php foreach ($players as $p): ?>
		<button class="btn btn-default ww-btn" type="button">
			<input type="radio" name="selected-player" value="<?=$p->id?>" autocomplete="off">
			<div><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong> <span class="text-s2">(<?=$p->points?> points)</span></div>
			<div><?=$p->club_id.' - '.$p->position?></div>
		</button>
	<?php endforeach; ?>
	</div>
</div>
<div class="row">
	<div class="btn-group btn-group-justified col-xs-12" data-toggle="buttons">
		<div class="btn-group btn-group-lg">
			<button id="prev" class="btn btn-default page-btn" type="button" value="<?=$page-1?>">
			Previous
			</button>
		</div>
		<div class="btn-group btn-group-lg">
			<button id="next" class="btn btn-default page-btn" type="button" value="<?=$page+1?>">
			Next
			</button>
		</div>
	</div>
</div>

