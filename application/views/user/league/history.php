<div class="row">
	<div class="columns small-12 callout">

		<h6 style="display:inline-block">Past Seasons: 		</h6>
		<?php foreach($years as $i => $y): ?>
			<?php if ($y->year == $this->session->userdata('current_year')){continue;}?>
			<a href="#"><?=$y->year?></a>
			<?php if ($i+1 != count($years)):?>
				|
			<?php endif;?>
		<?php endforeach; ?>
		<h6><a href="<?=site_url('league/history/player_records')?>">Player Records</a></h6>
		<h6><a href="#">Team Records</a></h6>
	</div>
</div>
