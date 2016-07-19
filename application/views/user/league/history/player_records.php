<div class="row">
	<div class="columns small-12">
		<h5>League History</h5>
	</div>
</div>




	<!-- Player Records -->
	<div class="row callout">
		<div class="columns small-12">
			<h5>Player Records</h5>
		</div>
		<div class="columns medium-6 small-12">
			<div class="col-xs-3">
				<span style="font-size:1.1em">Best Week </span>
			</div>
			<div class="col-xs-9">
				<div class="form-inline text-left">

					<select data-for="best-week" class="form-control player-list-position-select">
						<option value="0">All Pos.</option>
						<?php foreach($positions as $pos): ?>
							<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
						<?php endforeach;?>
					</select>
					<select class="form-control player-list-year-select" data-for="best-week">
						<option value="0">All Years</option>
						<?php foreach($years as $y): ?>
							<option value="<?=$y->year?>"><?=$y->year?></option>
						<?php endforeach;?>
					</select>
					<select class="form-control player-list-starter-select" data-for="best-week">
						<option value="all" selected>All Players</option>
						<option value="starter">Starters</option>
						<option value="bench">Bench</option>
					</select>

				</div>
			</div>
			<table class="table table-condensed table-striped">
				<thead>
					<th></th><th>Name</th><th>Pos.</th><th>Points</th><th>Week</th><th>Year</th><th>Owner</th>
				</thead>
				<tbody id="best-week" data-url="<?=site_url('player_search/ajax_best_week')?>">
				</tbody>
			</table>
		</div>

		<!-- Week Avg -->
		<div class="columns medium-6 small-12">
			<div class="col-xs-3">
				<span style="font-size:1.1em">Week Avg</span>
			</div>
			<div class="col-xs-9">
				<div class="form-inline text-left">

					<select data-for="avg-week" class="form-control player-list-position-select">
						<option value="0">All Pos.</option>
						<?php foreach($positions as $pos): ?>
							<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
						<?php endforeach;?>
					</select>
					<select class="form-control player-list-year-select" data-for="avg-week">
						<option value="0">All Years</option>
						<?php foreach($years as $y): ?>
							<option value="<?=$y->year?>"><?=$y->year?></option>
						<?php endforeach;?>
					</select>
					<select class="form-control player-list-starter-select" data-for="avg-week">
						<option value="all" selected>All Players</option>
						<option value="starter">Starters</option>
						<option value="bench">Bench</option>
					</select>

				</div>
			</div>
			<table class="table table-condensed table-striped">
				<thead>
					<th></th><th>Name</th><th>Pos.</th><th>Points</th><th>Games</th><th>Year</th>
				</thead>
				<tbody id="avg-week" data-url="<?=site_url('player_search/ajax_avg_week')?>">
				</tbody>
			</table>
		</div>

	</div>


<script>
$(updatePlayerList("best-week"));
$(updatePlayerList("avg-week"));
</script>
