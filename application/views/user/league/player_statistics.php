<?php //print_r($years); ?>
<?php $this->load->view('template/modals/stat_popup'); ?>

<div class="row callout">
	<div class="columns">
		<div class="row">
			<div class="columns">
				<h5>All Players</h5>
			</div>
		</div>

		<div class="row">
			<div class="columns medium-4 large-3 small-12">
				<input type="text" class="player-list-text-input" data-for="main-list" placeholder="Player search">
				<select data-for="main-list" class="player-list-position-select">
					<option value="0">All</option>
					<?php foreach($positions as $pos): ?>
						<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<div class="row">
			<div class="columns">
				<table class="table-condensed" >
					<thead>
						<th><a href="#" data-order="asc" data-for="main-list" data-by="last_name" class="player-list-a-sort">Name</a></th>
						<th><a href="#" data-order="asc" data-for="main-list" data-by="position" class="player-list-a-sort">Position</a></th>
						<th><a href="#" data-order="asc" data-for="main-list" data-by="club_id" class="player-list-a-sort">NFL Team</a></th>
						<th>Wk <?=$this->session->userdata('current_week')?> Opp.</th>
						<th><a href="#" data-order="asc" data-for="main-list" data-by="points" class="player-list-a-sort">Points</a></th>
						<th><Team</th>
					</thead>
					<tbody id="main-list" data-by="points" data-order="desc" data-url="<?=site_url('player_search/ajax_full_player_list')?>">

					</tbody>
				</table>
				<div class="player-list-total" data-for="main-list"></div>
				<div>
					<div class="row align-center">
			            <div class="columns text-right">
			                <ul class="pagination" role="navigation" aria-label="Pagination">
			                    <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="main-list">Previous</a></li>
			                </ul>
			            </div>
			            <div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
			                <div class="player-list-total" data-for="main-list"></div>
			                <br class="show-for-small-only">
			            </div>
			            <div class="columns text-left small-order-2 medium-order-3">
			                <ul class="pagination" role="navigation" aria-label="Pagination">
			                    <li class="pagination-next"><a href="#" class="player-list-next" data-for="main-list">Next</a></li>
			                </ul>
			            </div>
			        </div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="row callout">
	<div class="columns">
		<div class="row">
			<div class="columns">
				<h5>Records</h5>
			</div>
		</div>

		<!-- Best Week -->
		<div class="row">
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
	</div>
</div>
<script>

$(updatePlayerList("main-list"));
$(updatePlayerList("best-week"));
$(updatePlayerList("avg-week"));



</script>
