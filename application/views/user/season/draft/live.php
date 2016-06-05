<style>
.draft-box{
	margin-bottom:40px;
}
.v-align{
height: 60px;
line-height: 60px;
}
</style>

<?php if ($this->session->userdata('offseason')): ?>
	<?php $this->load->view('user/offseason');?>
<?php else: ?>

<?php if($this->is_league_admin):?>
<div class="row callout">
	<div class="columns">
		<h5><?=$this->session->userdata('current_year')?> Draft</h5>
	</div>
	<div class="columns">

		<!-- <a data-toggle="myteam-panel">My Team</a>
		<a data-toggle="watch-panel">Prospects</a>
		<a data-toggle="search-panel">Player Search</a> -->

	</div>
	<div class="columns">
			<button class="button tiny" id="admin-pause-button">Start Draft</button>
			<button class="button tiny" id="admin-picks" data-on="">Pick for User</button>
			<button class="button tiny" disabled id="admin-undo">Undo Last Pick</button>
	</div>
</div>
<?php endif;?>



	<!-- Top row with Now Picking and Recent picks -->
<div class="row callout">
	<div class="columns medium-expand small-12 text-center">
		<div id="on-the-block">
		</div>
	</div>
	<div class="columns medium-9 hide-for-small-only">
		<div class="text-center"><h5><a href="<?=site_url('season/draft')?>">Recent Picks (make this open a modal)</a></h5></div>

			<table class="table-condensed">
				<thead>
					<th>Overall</th><th>Round</th><th>Player</th><th>Team</th><th>Owner</th>
				</thead>
				<tbody id="recent-picks">
				</tbody>
			</table>

	</div>
	<div class="columns small-12 text-center show-for-small-only">
			<a href="#" class="show-for-small-only">Recent Picks</a>
		</div>
</div>


<!-- Row with draft search, watch list -->
<div class="row callout">

	<div id="watch-panel" class="columns medium-6 small-12 draft-box" data-toggler data-animate="hinge-in-from-top spin-out">
		<h5 class="text-center">Prospects</h5>

		<!-- Position dropdown for watch list -->
		<div class="row align-center">
			<div class='columns small-12 medium-4'>
				<select id="watch-list-pos" data-for="watch-list" class="player-list-position-select">
						<option value="0">All</option>
					<?php foreach ($pos as $p): ?>
						<option value="<?=$p->id?>"><?=$p->text_id?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div>
			<table class="table-condensed">
				<thead>

				</thead>
				<tbody id="watch-list" data-url="<?=site_url('player_search/ajax_get_draft_watch_list')?>">
				</tbody>
			</table>
		</div>

		<div class="row align-center">
			<div class="columns text-right">
				<ul class="pagination" role="navigation" aria-label="Pagination">
					<li class="pagination-previous"><a href="#" class="player-list-prev" data-for="watch-list">Previous</a></li>
				</ul>
			</div>
			<div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
				<div class="player-list-total" data-for="watch-list"></div>
				<br class="show-for-small-only">
			</div>
			<div class="columns text-left small-order-2 medium-order-3">
				<ul class="pagination" role="navigation" aria-label="Pagination">
					<li class="pagination-next"><a href="#" class="player-list-next" data-for="watch-list">Next</a></li>
				</ul>
			</div>
		</div>
	</div>


	<div id="search-panel" class="columns medium-6 small-12 draft-box" data-toggler data-animate="hinge-in-from-top spin-out">
		<div class="row">
			<div class="text-center columns">
				<h5>Player Search</h5>
			</div>
		</div>

		<!-- Search options -->
		<div class="row align-center">
			<div class="search-group columns small-12 medium-8">
				<input type="text" class="player-list-text-input" data-for="draft-list" placeholder="Search">
			</div>

			<div class='sort-group columns small-12 medium-4'>
				<select data-for="draft-list" class="player-list-position-select">
						<option value="0">All</option>
					<?php foreach ($pos as $p): ?>
						<option value="<?=$p->id?>"><?=$p->text_id?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="row">
		    <div class="columns">
		        <table class="table-condensed">
		            <thead>
						<tr>
						<th>
							<a href="#" data-order="asc" data-for="draft-list" data-by="last_name" class="player-list-a-sort">Name</a> /
							<a href="#" data-order="asc" data-for="draft-list" data-by="club_id" class="player-list-a-sort">Team</a> /
							<a href="#" data-order="asc" data-for="draft-list" data-by="position" class="player-list-a-sort">Pos</a>
						</th>
						<th></th>
						<th></th>
						</tr>
		            </thead>
		            <tbody id="draft-list" data-by="last_name" data-order="desc" data-url="<?=site_url('player_search/ajax_draft_list')?>">
		            </tbody>
		        </table>
		    </div>
		</div>

		<div class="row align-center">
		    <div class="columns text-right">
		        <ul class="pagination" role="navigation" aria-label="Pagination">
		            <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="draft-list">Previous</a></li>
		        </ul>
		    </div>
		    <div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
		        <div class="player-list-total" data-for="draft-list"></div>
		        <br class="show-for-small-only">
		    </div>
		    <div class="columns text-left small-order-2 medium-order-3">
		        <ul class="pagination" role="navigation" aria-label="Pagination">
		            <li class="pagination-next"><a href="#" class="player-list-next" data-for="draft-list">Next</a></li>
		        </ul>
		    </div>
		</div>
	</div>
</div>

<div id="myteam-panel" class="row callout" data-toggler data-animate="hinge-in-from-top spin-out">
	<div class="columns draft-box">
		<div class="d-myteam-heading text-center"><h5>My Team</h5></div>
		<table class="text-center table-condensed">
			<thead>
				<th class="text-center">Player Name</th><th class="text-center">Team/Pos</th><th class="text-center">Pick</th><th class="text-center hide-for-small-only">Round</th>
			</thead>
			<tbody id="myteam-list">
			</tbody>
		</table>
	</div>
</div>


<div id="debug" class="text-center hidden"></div>

<?php endif; ?>

<script>
$(document).ready(function(){

	$.post("<?=site_url('season/draft/ajax_get_update_key')?>"); // in case of stale key, force update on load
	$(updatePlayerList("draft-list"));
	//$(updatePlayerList("watch-list"));
	loadWatchList();
	updateBlock();
	loadMyTeam();
	updateRecentPicks();


	// This doesnt work in IE, need to check for that and use ajax instead at a longer interval
	// Also, may want to add a variable to check if draft is live or not.
	var evtSource = new EventSource("<?=site_url('season/draft/stream_get_update_key')?>");
	evtSource.onmessage = function(e){
		if($("#debug").text() != e.data)
		{

			//loadPlayerList(getpage(),getpos(),getsort(),getsearch());
			$(updatePlayerList("draft-list"));
			loadWatchList();
			//$(updatePlayerList("watch-list"));
			updateRecentPicks();
			updateBlock();
			$("#debug").text(e.data);

		}

		$("#debug").text(e.data);
	}
	//setInterval(function(){
	//	getUpdateKey();
	//	console.log("getUpdateKey every 2000 ms");
	//	updateBlock();
	//}, 2000);

});


// Countdown timer

setInterval(function(){
	updateTimer();
},1000);

function updateTimer()
{
	var timer = $("#countdown").data('seconds');
	var paused = $("#countdown").data('paused');
	hr = parseInt(timer / 60 / 60);
	min = parseInt((timer-(hr*60*60)) / 60);
	sec = parseInt((timer-(hr*60*60)-(min*60)));
	var clocktext = min+":"+pad(sec);

	if (paused)
	{
		$("#countdown").text(clocktext+" (paused)");
	}
	else if (timer >= 0)
	{
		if(timer <= 10)
		{
			//$("#on-the-block").css('background-color','#DF0101');
			$("#countdown").css('color','#DF0101');
			$("#countdown").data('warning',true);
			flash($("#countdown"));
		}
		$("#countdown").text(clocktext);
		$("#countdown").data('seconds',(timer-1));
	}



}

// // Name search event
// var timer;
// $("#search-name").on("input",function(event){
// 	clearTimeout(timer);
// 	var delay = 500;
// 	timer = setTimeout(function(){
// 		loadPlayerList(1,getpos(),getsort(),getsearch());
// 	},delay);
// });

// Position & sort event
// $('.sort-group').on('change', function(){ loadPlayerList(1,getpos(),getsort(),getsearch()); });
//
// // Prev/Next button events
// $("#next").click(function(){
// 	var page = getpage();
// 	var next = page+2;
// 	var prev = page;
// 	if ((page)*<?=$per_page?> <= gettotal())
// 	{
// 		loadPlayerList(page+1,getpos(),getsort(),getsearch());
// 		$("#next").val(next.toString());
// 		$("#prev").val(prev.toString());
// 	}
//
// });
// $("#prev").click(function(){
// 	var next = parseInt($('#next').val())-1;
// 	var prev = parseInt($('#prev').val())-1;
// 	var page = getpage();
// 	if (page > 1)
// 	{
// 		loadPlayerList(page-1,getpos(),getsort(),getsearch());
// 		$("#next").val(next);
// 		$("#prev").val(prev);
// 	}
// });

// Watch and draft button events also addded Up and Down
$("#draft-list, #watch-list").on("click",".btn-draft",function(event){
	event.preventDefault();
	//var vals = $(this).val().split("_");
	if($("#admin-picks").data('on')){var admin_pick = true;}
	var vals = $(this).data('value').split("_");
	if(vals[0] == "watch")
	{url="<?=site_url('season/draft/toggle_watch_player')?>";}
	if(vals[0] == "draft")
	{url="<?=site_url('season/draft/draft_player')?>";}
	if(vals[0] == "up")
	{url="<?=site_url('season/draft/watch_player_up')?>";}
	if(vals[0] == "down")
	{url="<?=site_url('season/draft/watch_player_down')?>";}

	$.post(url,{'player_id':vals[1], 'admin_pick' : admin_pick}, function(data){
		console.log(data);
		if (vals[0] == "up" || vals[0] == "down")
		{
			//var e = "a[data-value='"+vals[0]+"_"+vals[1]+"']";
			e = "."+vals[0]+"-"+vals[1];
			loadWatchList(e);
			//$(updatePlayerList("watch-list"));
			return;
		}
		loadWatchList();

		//loadPlayerList(getpage(),getpos(),getsort(),getsearch(),true);
		$(updatePlayerList("draft-list"));
		if(vals[0] == "draft"){loadMyTeam();}
	});
})

// $("#watch-list-pos").on('change',function(){
// 	//loadWatchList();
// });

function loadWatchList()
{
	$(updatePlayerList("watch-list"));
}

function getUpdateKey()
{
	url ="<?=site_url('season/draft/ajax_get_update_key')?>";
	$.post(url,{},function(data){
		$("#debug").html(data);
	});
}

function updateBlock()
{
	url = "<?=site_url('season/draft/ajax_get_block_info')?>";
	$.post(url,{},function(data){
	//	$("#on-the-block").css('background-color','');
		$("#countdown").css('color','');
		$("#on-the-block").html(data);
		//flash($("#on-the-block"))
		<?php if ($this->is_league_admin)
		{
			echo "updateAdminButtons();";
		}
		?>
	});
}

function updateRecentPicks()
{
	var old_pick = $("#recent-top-row").data('pickid');
	url ="<?=site_url('season/draft/ajax_get_recent_picks')?>";
	$.post(url,{},function(data){

		$("#recent-picks").html(data);
		var new_pick = $("#recent-top-row").data('pickid');
		if (old_pick != new_pick){flash($("#recent-top-row"));}

	});
}

function flash(element, fadetime)
{
	if (fadetime === undefined)
	{fadetime = 100;}

	//element.animate({opacity:0});
	element.animate({opacity:0},0);
		setTimeout(function(){
			element.animate({opacity:1}, fadetime);
	},fadetime);
}

// function loadPlayerList(page, pos, sort, search, dontreset)
// {
// 		url = "<?=site_url('season/draft/ajax_get_draft_table')?>";
// 		$.post(url,{'page':page-1, 'sel_pos':pos, 'sel_sort':sort, 'search' : search }, function(data){
// 			$("#available-players").html(data);
//
// 			<?php if($this->is_admin)
// 			{
// 				echo 'if($("#admin-picks").data("on")) {$(".btn-draft:contains(\"Draft\")").attr("disabled",false);}';
// 			}
// 			?>
//
// 		});
// 		if (dontreset == false)
// 		{
// 			$("#next").val(2);
// 			$("#prev").val(0);
// 		}
// }

// function loadWatchList(selector_text)
// {
// 	var pos = $('#watch-list-pos').val();
// 	url ="<?=site_url('season/draft/ajax_get_watch_list')?>";
// 	$.post(url,{'pos':pos},function(data){
// 		$("#watch-list").html(data);
// 		if (typeof(selector_text) != "undefined")
// 		{
// 			flash($(selector_text),100);
// 		}
// 	});
// }

function loadMyTeam()
{
	url ="<?=site_url('season/draft/ajax_get_myteam')?>";
	$.post(url,{},function(data){
		$('#myteam-list').html(data);
	});
}



// function getpage(){return parseInt($('#next').val())-1;} // Which page are we on?
// function gettotal(){return parseInt($('#count-total').text());}
// function getpos(){return $("#search-pos").val();}
// function getsort(){return $("#search-sort").val();}
// function getsearch(){return $("#search-name").val();}

function pad(n) {
    return (n < 10) ? ("0" + n) : n;
}

</script>

<?php if($this->is_league_admin): // All the admin javascript?>
	<script>

	$("#admin-picks").on('click',function(){
		console.log($(".btn-draft").data('on'));
		if($("#admin-picks").data('on'))
		{
			$(".btn-draft:contains('Draft')").attr('disabled',true);
			$("#admin-picks").data('on',false);
			$("#admin-picks").text("Pick for User");
			console.log("disabled=true");
		}
		else
		{
			$(".btn-draft:contains('Draft')").attr('disabled',false);
			$("#admin-picks").data('on',true);
			$("#admin-picks").text("Cancel user Pick");
			console.log("disabled=false");
		}
	});

	$("#admin-undo").on('click',function(){
		var url = "<?=site_url('season/draft/undo_last_pick')?>";
		$.post(url,{},function(data){

		});
	});
	$("#admin-pause-button").on('click',function(){
		if ($("#admin-pause-button").text() == "Start Draft")
		{
			var url = "<?=site_url('season/draft/start')?>";
			$.post(url,{},function(data){

			});
		}
		if ($("#admin-pause-button").text() == "Pause Draft")
		{
			var url = "<?=site_url('season/draft/pause')?>";
			$.post(url,{},function(data){

			});
		}
		if ($("#admin-pause-button").text() == "Resume Draft")
		{
			var url = "<?=site_url('season/draft/unpause')?>";
			$.post(url,{},function(data){

			});
		}
	});

	function updateAdminButtons()
	{
		var paused = $("#countdown").data('paused');
		var currenttime = $("#countdown").data('currenttime');
		var starttime = $("#countdown").data('starttime');

		if (starttime > currenttime)
		{$("#admin-pause-button").text("Start Draft");}

		if ((starttime < currenttime) && (!paused))
		{$("#admin-pause-button").text("Pause Draft");}

		if ((starttime < currenttime) && (paused))
		{$("#admin-pause-button").text("Resume Draft");}

		$("#admin-undo").attr("disabled",!paused);

		if($("#admin-picks").data('on'))
		{$("#admin-picks").text("Cancel user Pick");}
		else
		{$("#admin-picks").text("Pick for User");}

	}

	</script>
<?php endif;?>
