<div class="section">


	<?php if($season_appears_finished): ?>
	<div class="columns">
		<div class="column is-6">
			<div class="is-size-5"><?=$this->session->userdata('current_year')?> Season has Ended</div>
									<strong>Current NFL Season:</strong> <?=$real_year?>
			<br>
			<strong>Current League Season:</strong> <?=$this->session->userdata('current_year')?>
			<div class="text-center">
				<a href="<?=site_url('admin/end_season/reset_current_season')?>" class="button small is-link">Reset <?=$this->session->userdata('current_year')?> Season</a>
			</div>
		</div>
	</div>
	<?php else:?>
	<div class="columns">
		<div class="column is-6">
			<div class="is-size-5"><?=$this->session->userdata('current_year')?> Season in progress</div>
									<strong>Current NFL Season:</strong> <?=$real_year?>
			<br>
			<strong>Current League Season:</strong> <?=$this->session->userdata('current_year')?>
		</div>
	</div>
	<?php endif;?>
	<div class="columns">
		<div class="column is-6">
			<?php if($season_appears_finished): ?>
				<div class="text-center"><div class="is-size-5">Begin <?=$real_year?> Season</div></div>
				<br>
				<div class="content">
					Beginning a new season will:
					<ul >
						<li>Automatically enable "Offseason"/read-only mode for owners</li>
						<li>Clear all team rosters (except players designated as keepers)</li>
						<li>Archive: draft, waiver wire, scores, results, schedule, etc</li>
						<li>Not change: Scoring definitions, position definitions, owners/team names</li>
					</ul>
				</div>
				<div class="text-center">
					<a href="<?=site_url('admin/end_season/start_next_season')?>" class="button small is-link">Ready to Begin <?=$real_year?> Season</a>
				</div>
			<?php else:?>
				<div class="is-size-5">Reset Season</div>
				<br>
				<div class="content">
					Resetting the season will:
					<ul>
						<li>Automatically enable "Offseason"/read-only mode for owners</li>
						<li>Clear all team rosters (except players designated as keepers)</li>
						<li><b>Delete</b>: draft, waiver wire, scores, results, schedule, etc</li>
						<li>Not change: Scoring definitions, position definitions, owners/team names</li>
					</ul>
				</div>
				<div class="text-center">
					<a href="<?=site_url('admin/end_season/reset_current_season')?>" class="button is-small is-link">Reset <?=$real_year?> Season</a>
				</div>
			<?php endif;?>
		</div>
	</div>


</div>
