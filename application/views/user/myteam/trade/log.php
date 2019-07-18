<div class="section">
	<div class="container">
		<div class="title">Trades - <?=$this->session->userdata('current_year')?> Season</div>
		<?php if(count($log) > 0): ?>
		<div class="f-scrollbar">
			<table class="table is-fullwidth is-striped f-min-width-small is-size-7-mobile">
				<thead>
					<th>Date</th><th colspan=2>Trade details</th>
				</thead>
				<tbody>
				<?php foreach((array)$log as $l): ?>
					<tr>
						<td style="font-size:.8em"><?=date("n/j g:i a",$l['completed_date'])?> </td>
						<?php foreach($l['teams'] as $team): ?>
						<td>
							<div><?=$team['team_name']?> receives</div>
							<?php foreach($team['players'] as $player): ?>
									<div style="font-style:italic"><?=$player->first_name.' '.$player->last_name?></div>
							<?php endforeach;?>
							<?php foreach($team['picks'] as $p): ?>
									<div style="font-style:italic">Year: <?=$p->year.', Round: '.$p->round?></div>
							<?php endforeach;?>
						</td>
						<?php endforeach;?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else:?>
			<div class="is-size-6">No Trades to report</div>
		<?php endif;?>
	</div>
</div>
