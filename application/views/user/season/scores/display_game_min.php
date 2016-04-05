
	<div class="col-xs-12 col-sm-6">
		<?php $this->load->view('user/season/scores/display_team_min',
						array('team' => $game['home'])); ?>

	</div>

	<div class="col-xs-12 col-sm-6">
		<?php $this->load->view('user/season/scores/display_team_min',
				array('team' => $game['away'])); ?>
	</div>
