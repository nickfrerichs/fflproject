<div class="btn-group col-xs-12" data-toggle="buttons" >

<?php foreach ($players as $p): ?>
	<button class="btn btn-default ww-btn" type="button">
		<input type="radio" name="drop" value="<?=$p->id?>" autocomplete="off">
		<div><strong><?=$p->first_name.' '.$p->last_name?></strong> <span class="text-s2">(<?=$p->points?> points)</span></div>
		<div><?=$p->club_id.' - '.$p->position?></div>
	</button>
<?php endforeach; ?>
</div>
<div class="btn-group" data-toggle="buttons" >

		<button id="prev" class="btn btn-default page-btn" type="button" value="<?=$page-1?>">
		Previous
		</button>

		<button id="next" class="btn btn-default page-btn" type="button" value="<?=$page+1?>">
		Next
		</button>
</div>
