<div class="row">
	<div class="columns">
		<?php if(count($log) > 0): ?>
			<?php //print_r($log); ?>
			<table>
				<thead>
					<th>Date</th><th></th><th colspan=2>Trade details</th>
				</thead>
			</tbody>
			<?php foreach((array)$log as $l): ?>
				<tr>
					<td style="font-size:.8em"><?=date("n/j g:i a",$l['completed_date'])?> </td>
					<td><i class="fi-check"></i></td>

						<?php foreach($l['teams'] as $team): ?>
							<td>
							<div class="columns small-12"><?=$team['team_name']?> receives</div>
							<?php foreach($team['players'] as $player): ?>
									<div class="columns small-12" style="font-style:italic"><?=$player->first_name.' '.$player->last_name?></div>
							<?php endforeach;?>
							<?php foreach($team['picks'] as $p): ?>
									<div class="columns small-12" style="font-style:italic">Year: <?=$p->year.', Round: '.$p->round?></div>
							<?php endforeach;?>
						</td>
						<?php endforeach;?>

				</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
		<?php endif;?>
	</div>
</div>
