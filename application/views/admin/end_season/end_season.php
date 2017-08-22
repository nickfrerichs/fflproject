<div class="row">
	<div class="columns">
		<div class="row callout">
			<div class="columns">
				<?php if($season_appears_finished): ?>
				<div class="row align-center">
					<div class="columns callout success small-10 medium-6 text-center">
						<h5><?=$this->session->userdata('current_year')?> Season has Ended</h5>
												<strong>Current NFL Season:</strong> <?=$real_year?>
						<br>
						<strong>Current League Season:</strong> <?=$this->session->userdata('current_year')?>
						<div class="text-center">
							<a href="<?=site_url('admin/end_season/reset_current_season')?>" class="button small">Reset <?=$this->session->userdata('current_year')?> Season</a>
						</div>
					</div>

				</div>
				<?php else:?>
				<div class="row align-center">
					<div class="columns callout alert small-10 medium-6 text-center">
						<h5><?=$this->session->userdata('current_year')?> Season in progress</h5>
												<strong>Current NFL Season:</strong> <?=$real_year?>
						<br>
						<strong>Current League Season:</strong> <?=$this->session->userdata('current_year')?>
					</div>
				</div>
				<?php endif;?>
				<div class="row align-center">
					<div class="columns callout small-10 medium-6">
						<?php if($season_appears_finished): ?>
							<div class="text-center"><h5>Begin <?=$real_year?> Season</h5></div>
							<br>
							<div>
								Beginning a new season will:
								<ul>
									<li>Automatically enable "Offseason"/read-only mode for owners</li>
									<li>Clear all team rosters (except players designated as keepers)</li>
									<li>Archive: draft, waiver wire, scores, results, schedule, etc</li>
									<li>Not change: Scoring definitions, position definitions, owners/team names</li>
								</ul>
							</div>
							<div class="text-center">
								<a href="<?=site_url('admin/end_season/start_next_season')?>" class="button small">Ready to Begin <?=$real_year?> Season</a>
							</div>
						<?php else:?>
							<div class="text-center"><h5>Reset Season</h5></div>
							<br>
							<div>
								Resetting the season will:
								<ul>
									<li>Automatically enable "Offseason"/read-only mode for owners</li>
									<li>Clear all team rosters (except players designated as keepers)</li>
									<li><b>Delete</b>: draft, waiver wire, scores, results, schedule, etc</li>
									<li>Not change: Scoring definitions, position definitions, owners/team names</li>
								</ul>
							</div>
							<div class="text-center">
								<a href="<?=site_url('admin/end_season/reset_current_season')?>" class="button small">Reset <?=$real_year?> Season</a>
							</div>
						<?php endif;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
