<div class="row">
	<div class="columns">
		
		<table>
			<thead>
				<th>Date</th><th></th><th colspan=2>Trade details</th>
			</thead>
		</tbody>
		<?php foreach($log as $l): ?>
			<tr>
				<td style="font-size:.8em"><?=date("n/j g:i a",$l['completed_date'])?> </td>
				<td><i class="fi-check"></i></td>
				<td>
					<div class="columns small-12">Team: <?=$l['team1']['team_name']?></div>
					<?php foreach($l['team1']['players'] as $p): ?>
						<div class="columns small-12" style="font-style:italic"><?=$p['first_name'].' '.$p['last_name']?></div>
					<?php endforeach;?>
				</td>
				<td>
					<div class="columns small-12">Team: <?=$l['team2']['team_name']?></div>
					<?php foreach($l['team2']['players'] as $p): ?>
						<div class="columns small-12" style="font-style:italic"><?=$p['first_name'].' '.$p['last_name']?></div>
					<?php endforeach;?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>
	</div>
</div>
