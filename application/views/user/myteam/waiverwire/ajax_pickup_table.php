<?php foreach ($players as $p): ?>
	<tr class="pickup-player" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">
		<!--
		<td>
		<button class="btn btn-default pickup-player btn-xs" data-pickup-id="<?=$p->id?>"
			data-pickup-name="<?=$p->first_name.' '.$p->last_name?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
		</td>
	-->
			<td><?=$p->position?></td>
			 <?php if(strlen($p->first_name.$p->last_name) > 12){$name = $p->short_name; }
                  else{$name = $p->first_name." ".$p->last_name;} ?>
			<td>
            	<a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$name?></a>
        	</td>
            <td>
            <?=$p->club_id?>
        	</td>
		        </div>
		        <div>

		        </div>
		    </td>
			<td class="text-xxs">
				<div><?=$matchups[$p->club_id]['opp']?></div>
				<?php if($matchups[$p->club_id]['time'] != ""): ?>
					<?php if(date("D",$matchups[$p->club_id]['time']) == "Sun"): ?>
						<div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
					<?php else: ?>
						<div><?=date("D g:i",$matchups[$p->club_id]['time'])?></div>
					<?php endif; ?>
				<?php endif;?>
			</td>
			<td><?=$p->points?></td>
			<td>
				<?php if ($p->clear_time)
				{
					$remaining = $p->clear_time - time();
					$hr = (int)($remaining / (60*60));
					$min = (int)(($remaining - $hr*(60*60)) / 60);
					$sec = (int)(($remaining - $hr*(60*60) - $min*60));
				}
				?>
				<?php if($p->clear_time): ?>
					Waivers clear in <?=$hr?>h:<?=$min?>m:<?=$sec?>s
				<?php else: ?>
					<button class="player-pickup btn btn-small btn-default" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">Pickup</button>
				<?php endif;?>
			</td>


	</tr>
<?php endforeach; ?>

<tr><td colspan="6">
<?php $high = (($page+1)*$per_page); ?>
<?php $low = $high-($per_page-1); ?>
<?php if($high > $total_players){$high = $total_players;}?>
<span id="count-current"><?=$low?> - <?=$high?> of </span><span id="count-total"><?=$total_players?></span>
</td>
</tr>
