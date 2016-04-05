<?php //print_r($years); ?>
<?php $this->load->view('template/modals/stat_popup'); ?>
<div class="container">
	<h3>NFL Players</h3>
	<hr>
	<br>

	<h4>All Players</h4>
	<div class="form-inline">
		<input type="input" class="form-control player-list-text-input" data-for="main-list" placeholder="Player search">
		<select data-for="main-list" class="form-control player-list-position-select">
			<option value="0">All</option>
			<?php foreach($positions as $pos): ?>
				<option value="<?=$pos->id?>"><?=$pos->text_id?></option>
			<?php endforeach;?>
		</select>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-condensed table-striped" >
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
				<div class="form-inline">
					<button data-for="main-list" class="btn btn-default player-list-prev">Prev</button>
					<button data-for="main-list" class="btn btn-default player-list-next">Next</button>
				</div>
			</div>
		</div>
	</div>


<br>
<br>
<h4>Records</h4>
<hr>
	<!-- Best Week -->
	<div class="row">
		<div class="col-md-6">
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
		<div class="col-md-6">
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
<script>

$(updatePlayerList("main-list"));
$(updatePlayerList("best-week"));
$(updatePlayerList("avg-week"));

// PLAYER SEARCH JS FUNCTIONS
//
// REQUIRED:
// 		tbody with ID to identify the unique list (ex: your-tbody-id)
//			data-url="http://mysite.com/player_search/ajax_url"  -- specify url to post to
//		corresponding element named *-data to store/pass values through ajax, ex: your-tbody-id-data
//
// OPTIONAL:
//		class="player-list-text-input" data-for="your-tbody-id"
//		class="player-list-position-select" data-for="your-tbody-id"
//		class="player-list-a-sort" data-for="your-tbody-id" data-order="asc" data-by="club_id"
//		class="player-list-total" data-for="your-tbody-id"
//		class="player-list-prev" data-for="your-tbody-id"
//		class="player-list-next" data-for="your-tbody-id"

$(".player-list-next, .player-list-prev").on('click',function(e){
	e.preventDefault();
	tbody = $(this).data('for');
	if($(this).hasClass('player-list-next'))
	{$("#"+tbody+"-data").data('page',$("#"+tbody+"-data").data('page')+1);}
	else
	{$("#"+tbody+"-data").data('page',$("#"+tbody+"-data").data('page')-1);}
	updatePlayerList(tbody);
});

$(".player-list-text-input").on("input",function(event){
	var playerSearchTimer;
	clearTimeout(playerSearchTimer);
	var delay = 500;
	var tbody = $(this).data('for');
	playerSearchTimer = setTimeout(function(){
		resetPlayerSearch(tbody,0,0);
		updatePlayerList(tbody);
	},delay);
});

$(".player-list-position-select").on("change",function(){
	var tbody = $(this).data('for');
	resetPlayerSearch(tbody,1,0);
	resetPlayerSort(tbody);
	updatePlayerList(tbody);
});

$(".player-list-year-select").on("change",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);

});

$(".player-list-starter-select").on("change",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);
});

$(".player-list-custom-select").on("change",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);
});

// player sort using links: by, order  -- stored in tbody-player-list.data('by') and ('order')
$(".player-list-a-sort").on('click', function(e){
	var tbody = $(this).data('for');
	// If we've already sorted on this "by", toggle the order
	if ($("#"+tbody).data('by') == $(this).data('by'))
		{$("#"+tbody).data('order', $("#"+tbody).data('order') == 'asc' ? 'desc' : 'asc');}
	else
		{$("#"+tbody).data('order','asc');}
	$("#"+tbody).data('by',$(this).data('by'));

	updatePlayerList(tbody);
	e.preventDefault();
});

function updatePlayerList(tbody)
{
	var page = $('#'+tbody+'-data').data('page');
	var pos = $('.player-list-position-select[data-for="'+tbody+'"]').val();
	var year = $('.player-list-year-select[data-for="'+tbody+'"]').val();
	var starter = $('.player-list-starter-select[data-for="'+tbody+'"]').val();
	var custom = $('.player-list-custom-select[data-for="'+tbody+'"]').val();
	var by = $("#"+tbody).data('by');
	var order = $("#"+tbody).data('order');
	var search = $('.player-list-text-input[data-for="'+tbody+'"]').val();
	var per_page = $("#"+tbody+"-data").data('perpage');
	var url = $("#"+tbody).data('url');
	//resetPlayerPage(tbody);
	$.post(url, {'page':page, 'pos':pos, 'by':by, 'order':order, 'search' : search, 'per_page': per_page,
	 			 'year':year, 'starter':starter, 'custom':custom}, function(data){
		$("#"+tbody).html(data);

		// Display count currently on screen (1-10 of 500)
		var pagelow = ($("#"+tbody+"-data").data('page') * $("#"+tbody+"-data").data('perpage'));
		var pagehigh = pagelow+$("#"+tbody+"-data").data('perpage');
		var total = $("#"+tbody+"-data").data('total');
		if (pagehigh > total){pagehigh = total;}

		$(".player-list-total[data-for='"+tbody+"']").text((pagelow+1)+" - "+pagehigh+" of "+total);

		// Disable prev button if first page
		if($("#"+tbody+"-data").data('page') < 1)
		{$('.player-list-prev[data-for="'+tbody+'"]').attr('disabled',true);}
		else{$('.player-list-prev[data-for="'+tbody+'"]').attr('disabled',false);}

		// Disable next button if last page
		if(pagehigh+1 > total)
		{$('.player-list-next[data-for="'+tbody+'"]').attr('disabled',true);}
		else{$('.player-list-next[data-for="'+tbody+'"]').attr('disabled',false);}
	})
}


function resetPlayerSearch(tbody,text,pos)
{
	if (text == undefined){text = true;}
	if (pos == undefined){pos = true;}
	if (text == true){$('.player-list-text-input[data-for="'+tbody+'"]').val('');}
	if (pos == true){$('.player-list-position-select[data-for="'+tbody+'"]').val(0);}
	$("#"+tbody+"-data").data('page',0);
}

function resetPlayerSort(tbody)
{
	$("#"+tbody).data('by','last_name');
	$("#"+tbody).data('order','asc');
}

function resetPlayerPage(tbody) // This is not being used right now
{$("#"+tbody+"-data").data('page',0);}

</script>
