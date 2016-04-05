<style>
.draft-box{
	margin-bottom:40px;
}
.v-align{
height: 60px;
line-height: 60px;
}
</style>
<div class="container">
	<div class="row">
		<div class="col-sm-4 v-align"><h3><?=date("Y",$start_time)?> Draft</h3></div>

		<div class="col-sm-8 v-align" style="min-height:50px;">
		<?php if($this->is_admin):?>

			<button class="btn btn-default" id="admin-pause-button">Start Draft</button>
			<button class="btn btn-default" id="admin-picks" data-on="">Pick for User</button>
			<button class="btn btn-default" disabled id="admin-undo">Undo Last Pick</button>

		<?php endif;?>
		</div>
	</div>

	<!-- Top row with Now Picking and Recent picks -->
	<div class="row" style="min-height:10px">
		<div class="col-sm-3 text-center">
			<div id="on-the-block" style="min-height:150px;">

			</div>
		</div>
		<div class="col-sm-9">

			<div class="text-center"><h4>Recent Picks</h4></div>
			<table class="table table-striped table-condensed table-border">
				<thead>
					<th>Overall</th><th>Round</th><th>Player</th><th>Team</th><th>Owner</th>
				</thead>
				<tbody id="recent-picks">
				</tbody>
			</table>

		</div>
	</div>

	<!-- Row with draft search, watch list, and my team -->
	<div class="row">

		<div class="col-md-4 draft-box">
			<div class="row">
				<div class="text-center"><h4>Watch List</h4></div>
			</div>

			<table class="table text-center table-striped table-condensed table-border">
				<thead>
					<th class="text-center" colspan=5>Prospects</th>
				</thead>
				<tbody id="watch-list">
				</tbody>
			</table>
		</div>

		<div class="col-md-4 draft-box">

			<!-- Search options -->
			<div class="row">
				<div class="text-center"><h4>Player Search</h4></div>
				<div class="search-group col-xs-4">
					<input id="search-name" type="text" class="form-control" placeholder="Search">
				</div>

				<div class='col-xs-4 sort-group'>
					<select id="search-pos" class="form-control search-form">
							<option value="0">All</option>
						<?php foreach ($pos as $p): ?>
							<option value="<?=$p->id?>"><?=$p->text_id?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class='col-xs-4 sort-group'>
					<select id="search-sort" class="form-control search-form">
						<?php foreach ($sort as $id=>$name): ?>
							<option value="<?=$id?>"><?=$name?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div> <!-- end search options -->


			<!-- Available players -->
			<!--<div class="row">-->
				<table class="table text-center table-striped table-condensed table-border">
					<tbody id="available-players">
					</tbody>
				</table>
			<!--</div>-->

			<!-- next/prev buttons -->
			<div class="row">
				<div class="btn-group btn-group-justified col-xs-12">
					<div class="btn-group btn-group-lg">
						<button id="prev" class="btn btn-default page-btn" type="button" value="0">
						Previous
						</button>
					</div>
					<div class="btn-group btn-group-lg">
						<button id="next" class="btn btn-default page-btn" type="button" value="2">
						Next
						</button>
					</div>
				</div>
			</div> <!-- next/prev -->
		</div>


		<div class="col-md-4 draft-box">
			<div class="row">
				<div class="d-myteam-heading text-center"><h4>My Team</h4></div>
			</div>

			<table class="table text-center table-striped table-condensed table-border">
				<thead>
					<th class="text-center">Player Name</th><th class="text-center">Pick</th><th class="text-center">Round</th>
				</thead>
				<tbody id="myteam-list">
				</tbody>
			</table>
		</div>
	</div>

</div>
<div id="debug" class="text-center hidden"></div>

<script>
$(document).ready(function(){

	$.post("<?=site_url('season/draft/ajax_get_update_key')?>"); // in case of stale key, force update on load
	loadPlayerList(1,'0','a','');
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

			loadPlayerList(getpage(),getpos(),getsort(),getsearch());
			loadWatchList();
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

// Name search event
var timer;
$("#search-name").on("input",function(event){
	clearTimeout(timer);
	var delay = 500;
	timer = setTimeout(function(){
		loadPlayerList(1,getpos(),getsort(),getsearch());
	},delay);
});

// Position & sort event
$('.sort-group').on('change', function(){ loadPlayerList(1,getpos(),getsort(),getsearch()); });

// Prev/Next button events
$("#next").click(function(){
	var page = getpage();
	var next = page+2;
	var prev = page;
	if ((page)*<?=$per_page?> <= gettotal())
	{
		loadPlayerList(page+1,getpos(),getsort(),getsearch());
		$("#next").val(next.toString());
		$("#prev").val(prev.toString());
	}

});
$("#prev").click(function(){
	var next = parseInt($('#next').val())-1;
	var prev = parseInt($('#prev').val())-1;
	var page = getpage();
	if (page > 1)
	{
		loadPlayerList(page-1,getpos(),getsort(),getsearch());
		$("#next").val(next);
		$("#prev").val(prev);
	}
});

// Watch and draft button events also addded Up and Down
$("#available-players, #watch-list").on("click",".btn-draft",function(event){
	event.preventDefault();
	//var vals = $(this).val().split("_");
	var vals = $(this).data('value').split("_");
	if(vals[0] == "watch")
	{url="<?=site_url('season/draft/toggle_watch_player')?>";}
	if(vals[0] == "draft")
	{url="<?=site_url('season/draft/draft_player')?>";}
	if(vals[0] == "up")
	{url="<?=site_url('season/draft/watch_player_up')?>";}
	if(vals[0] == "down")
	{url="<?=site_url('season/draft/watch_player_down')?>";}
	$.post(url,{'player_id':vals[1]},function(data){

		if (vals[0] == "up" || vals[0] == "down")
		{
			//var e = "a[data-value='"+vals[0]+"_"+vals[1]+"']";
			e = "."+vals[0]+"-"+vals[1];
			loadWatchList(e);
			return;
		}
		loadWatchList();
		loadPlayerList(getpage(),getpos(),getsort(),getsearch(),true);
		if(vals[0] == "draft"){loadMyTeam();}
	});
})



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
		<?php if ($this->is_admin)
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

function loadPlayerList(page, pos, sort, search, dontreset)
{
		url = "<?=site_url('season/draft/ajax_get_draft_table')?>";
		$.post(url,{'page':page-1, 'sel_pos':pos, 'sel_sort':sort, 'search' : search }, function(data){
			$("#available-players").html(data);

			<?php if($this->is_admin)
			{
				echo 'if($("#admin-picks").data("on")) {$(".btn-draft:contains(\"Draft\")").attr("disabled",false);}';
			}
			?>

		});
		if (dontreset == false)
		{
			$("#next").val(2);
			$("#prev").val(0);
		}
}

function loadWatchList(selector_text)
{

	url ="<?=site_url('season/draft/ajax_get_watch_list')?>";
	$.post(url,{},function(data){
		$("#watch-list").html(data);
		if (typeof(selector_text) != "undefined")
		{
			flash($(selector_text),100);
		}
	});
}

function loadMyTeam()
{
	url ="<?=site_url('season/draft/ajax_get_myteam')?>";
	$.post(url,{},function(data){
		$('#myteam-list').html(data);
	});
}



function getpage(){return parseInt($('#next').val())-1;} // Which page are we on?
function gettotal(){return parseInt($('#count-total').text());}
function getpos(){return $("#search-pos").val();}
function getsort(){return $("#search-sort").val();}
function getsearch(){return $("#search-name").val();}

function pad(n) {
    return (n < 10) ? ("0" + n) : n;
}

</script>

<?php if($this->is_admin): // All the admin javascript?>
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
