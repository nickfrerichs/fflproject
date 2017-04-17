
	<div class="row callout">
		<div class="columns small-12" style="max-width:200px">
			<select id="selected-record">
				<option value="career">Career</option>
				<option value="best-week">Best Week</option>
			</select>
		</div>
	</div>

	<!-- Player Records -->
	<div class="row callout">

		<div id="best-week-div" class="columns small-12 hide stat-div">
			<div class="row">
				<div class="columns column small-12 medium-4">
					<select data-for="best-week" class="form-control player-list-position-select">
						<option value="0">All Pos.</option>
						<?php foreach($positions as $pos): ?>
							<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
						<?php endforeach;?>
					</select>
				</div>


				<div class="columns column small-12 medium-4">
					<select class="form-control player-list-year-select" data-for="best-week">
						<option value="0">All Years</option>
						<?php foreach($years as $y): ?>
							<option value="<?=$y->year?>"><?=$y->year?></option>
						<?php endforeach;?>
					</select>
				</div>

				<div class="columns column small-12 medium-4">
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
		<div id="career-div" class="columns hide small-12 stat-div">
			<div class="row">
				<div class="columns column small-12 medium-4">
					<select data-for="career" class="form-control player-list-position-select">
						<option value="0">All Pos.</option>
						<?php foreach($positions as $pos): ?>
							<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="columns column small-12 medium-4">
					<select class="form-control player-list-year-select" data-for="career">
						<option value="0">All Years</option>
						<?php foreach($years as $y): ?>
							<option value="<?=$y->year?>"><?=$y->year?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="columns column small-12 medium-4">
					<select class="form-control player-list-starter-select" data-for="career">
						<option value="all" selected>All Players</option>
						<option value="starter">Starters</option>
						<option value="bench">Bench</option>
					</select>
				</div>
			</div>


			<table class="table table-condensed table-striped">
				<thead>
					<th></th>
					<!-- <th><a href="#" data-order="asc" data-for="career" data-by="last_name" class="player-list-a-sort">Name</a></th> -->
					<th>Name</th>
					<th>Pos.</th>
					<th><a href="#" data-order="desc" data-for="career" data-by="avg_points" class="player-list-a-sort">Avg</a></th>
					<th><a href="#" data-order="desc" data-for="career" data-by="total_points" class="player-list-a-sort">Total</a></th>
					<th><a href="#" data-order="desc" data-for="career" data-by="games" class="player-list-a-sort">Games</a></th>
					<th>Year</th>
				</thead>
				<tbody id="career" data-by="avg_points" 
								   data-order="desc" 
								   data-url="<?=site_url('player_search/ajax_career')?>">
				</tbody>
			</table>
		</div>
	</div>


<script>
//$(updatePlayerList("best-week"));

$('#selected-record').on('change',function(){
	load_stat_div();
});

function load_stat_div()
{
	var stat = $('#selected-record').val();
	$('#'+stat+'-div').removeClass('hide');
	$('.stat-div:not(#'+stat+'-div)').addClass('hide');
	$(updatePlayerList(stat));
}

load_stat_div();
</script>
