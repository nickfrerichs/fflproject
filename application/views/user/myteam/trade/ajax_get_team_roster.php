<table class="table table-striped table-border text-center table-condensed">
	<thead><th class="text-center">Pos</th><th class="text-center">Player</th><th></th></thead>
<tbody>
<?php foreach ($team_roster as $r): ?>
	<tr>
		<td>
			<?=$r->pos?>
		</td>
		<td>
	        <div>
            <?php if(strlen($r->first_name.$r->last_name) > 12){$name = $r->short_name; }
                      else{$name = $r->first_name." ".$r->last_name;} ?>
		            <a href="#" class="stat-popup" data-type="player" data-id="<?=$r->player_id?>"><?=$name?></a> - <?=$r->club_id?>
		    </div>
		</td>
		<td>
			<button class="btn btn-default request-btn btn-sm" type="button" value="<?=$r->player_id?>" data-name="<?=$name?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
		</td>



	</tr>
<?php endforeach; ?>
</tbody>
</table>
