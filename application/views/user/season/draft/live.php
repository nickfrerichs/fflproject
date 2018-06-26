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

<!-- Confirm rank reset modal -->
<?php 
// Drop modal
$body =     '	<div class="text-center">This will clear your current watch list!</div>
				<br>
				<div>
					<button class="button is-link is-small" type="button" id="confirm-rank-reset">
						Confirm
					</button>
					<button class="button is-link is-small" type-"button" id="cancel-rank-reset">
						Cancel
					</button>
				</div>';

$this->load->view('components/modal', array('id' => 'drop-modal',
                                                    'title' => 'Are you sure?',
                                                    'body' => $body,
                                                    'reload_on_close' => True));
?>
<!-- <div class="reveal" id="confirm-rank-reset-modal" data-reveal data-overlay="true">
	<h5 class="text-center">Are you sure?</h5>
	<div class="text-center">This will clear your current watch list!</div>
	<br>
    <div class="text-center">
        <button class="button" type="button" id="confirm-rank-reset">
            Confirm
        </button>
        <button class="button" type-"button" id="cancel-rank-reset" data-close aria-label="Close modal">
            Cancel
        </button>
    </div>
</div> -->

<?php if($this->is_league_admin):?>
<div class="section">

		<div class="is-size-5"><?=$this->session->userdata('current_year')?> Draft</div>

	<div>
			<button class="button is-link is-small" id="admin-pause-button">Start Draft</button>
			<button class="button is-link is-small" id="admin-picks" data-on="">Pick for User</button>
			<button class="button is-link is-small" disabled id="admin-undo">Undo Last Pick</button>
	</div>
</div>
<?php endif;?>

	<!-- Top row with Now Picking and Recent picks -->
<div class="section">

	<div class="is-size-5" id="d-block-title">
		<?=$block_title?>
	</div>

	<div id="on-the-block">
		<!-- moved stuff from old ajax here -->
		<?php if(($scheduled_start_time > $current_time) && ($start_time == 0 || $start_time > $current_time)): // Draft is in the future?>
			<div class="d-block-team-name"><?=date('D M j - g:i a',$scheduled_start_time)?></div>
			<div>
				<img id="d-block-team-logo" class="hide-for-small-only" src="">
			</div>

			<div class="d-block-round">
			</div>
			
			<div id="countdown" class="d-block-clock" data-deadline=""
				data-currenttime="<?=$current_time?>" data-seconds="-1"
				data-paused="" data-starttime="<?=$start_time?>" data-teamid="">
			</div>
		<?php elseif (empty($current_pick)): // Draft is over??>
			<div class="d-block-team-name">Draft is over.</div>
		<?php else: // Draft is in progress?>
			<div class="d-block-team-name"><?=$current_pick->team_name?></div>
			<div>
				<img id="d-block-team-logo" class="hide-for-small-only" src="<?=$current_pick->logo_url?>">
			</div>

			<div class="d-block-round">
				Round <?=$current_pick->round?>

				Pick <?=$current_pick->pick?>
			</div>

			<div id="countdown" class="d-block-clock" data-deadline="<?=$current_pick->deadline?>"
				data-currenttime="<?=$current_time?>" data-seconds="<?=$seconds_left?>"
				data-paused="<?=$paused?>" data-starttime="<?=$start_time?>" data-teamid ="<?=$current_pick->team_id?>">...
			</div>
		<?php endif; ?>
		<!-- end move stuff from old ajax -->
	</div>


	<div id="d-recent-picks-div" style="max-height:190px;overflow-Y:hidden">
		<div class="text-center hide"><a href="<?=site_url('season/draft')?>" target="_blank">
			Recent Picks</a>
		</div>
		<table class="table is-narrow is-fullwidth">
			<thead>
				<th>Overall</th><th>Round</th><th>Player</th><th>Team</th><th>Owner</th>
			</thead>
			<tbody id="recent-picks">

			</tbody>
		</table>
	</div>
	<div class="text-center" style="font-size:.9em;cursor:pointer"><a id="d-scroll-link">scroll</a></div>


	<div class="text-center show-for-small-only">
		<a href="#" class="show-for-small-only">Recent Picks</a>
	</div>
</div>


<!-- Row with draft search, watch list -->
<div class="section">

	<div class="tabs is-small is-boxed fflp-tabs-active">
		<ul>
			<li class="is-active" data-for="draft-player-list-tab" data-load-content="draft-list"><a>Player List</a></li>
			<li class="" data-for="draft-watch-list-tab" data-load-content="watch-list"><a>Watch List</a></li>
			<li class="" data-for="draft-myteam-list-tab" data-load-content="myteam-list"><a>My Team</a></li>
		</ul>
	</div>

 
	<!-- Player search goes here -->



	<div id="draft-player-list-tab">
		<?php //Show the player list using player_search_table component

		$headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
		$headers['Team'] = array('by' => 'club_id', 'order' => 'asc');
		$headers['Position'] = array('by' => 'position', 'order' => 'asc');
		$headers['Bye'] = array();
		$headers[''] = array();
		//$headers['Wk '.$this->session->userdata('current_week').' Opp.'] = array('classes' => array('hide-for-small-only'));
		//$headers['Bye'] = array();
		//$headers['Points'] = array('by' => 'points', 'order' => 'asc');
		//$headers['Team'] = array();

		$pos_dropdown['All'] = 0;
		foreach($pos as $p)
			$pos_dropdown[$p->text_id] = $p->id;

		$this->load->view('components/player_search_table',
						array('id' => 'draft-list',
							'url' => site_url('load_content/draft_player_list'),
							'order' => 'desc',
							'by' => 'last_name',
							'pos_dropdown' => $pos_dropdown,
							'headers' => $headers,
							'disable_search' => False,
							'blah' => "blah"));


		?>
	</div>

    <!-- watch list -->
	<div id="draft-watch-list-tab" class="is-hidden">
		<?php //Show the player list using player_search_table component

		$headers[''] = array('by' => 'last_name', 'order' => 'asc');
		$headers[''] = array('by' => 'club_id', 'order' => 'asc');
		$headers[''] = array('by' => 'position', 'order' => 'asc');
		$headers[''] = array();
		$headers[''] = array();

		$pos_dropdown['All'] = 0;
		foreach($pos as $p)
			$pos_dropdown[$p->text_id] = $p->id;

		$this->load->view('components/player_search_table',
						array('id' => 'watch-list',
							'url' => site_url('load_content/draft_watch_list'),
							'order' => 'asc',
							'by' => 'meh',
							'pos_dropdown' => $pos_dropdown,
							'disable_search' => True,
							'headers' => $headers));


		?>
	</div>

	<div id="draft-myteam-list-tab" class="is-hidden">
		<div class="d-myteam-heading text-center"><h5>My Team</h5></div>
		<table class="text-center table-condensed">
			<thead>
				<th class="text-center">Player Name</th><th class="text-center">Team/Pos</th><th class="text-center">Bye</th><th class="text-center">Pick</th><th class="text-center hide-for-small-only">Round</th>
			</thead>
			<tbody id="myteam-list">
			</tbody>
		</table>
	</div>


<div id="debug" class="text-center hide"></div>

<?php endif; ?>

<script>

$(document).ready(function(){

	//$.post("<?=site_url('season/draft/ajax_get_update_key')?>"); // in case of stale key, force update on load
	
	$(loadContent('draft-list'));
	$(loadContent('watch-list'));

	//updatePlayerList("draft-list");
	//updatePlayerList("watch-list");
	
	
	updateTimer();



	//loadWatchList();

	//updateBlock();
	//loadMyTeam();
	//updateRecentPicks();

	// This doesnt work in IE, need to check for that and use ajax instead at a longer interval
	// Also, may want to add a variable to check if draft is live or not.

	// var evtSource = new EventSource("<?=site_url('season/draft/stream_get_update_key')?>");
	// evtSource.onmessage = function(e){
	// 	if($("#debug").text() != e.data)
	// 	{
	// 		updatePlayerList("draft-list");
	// 		loadWatchList();
	// 		updateRecentPicks();
	// 		updateBlock();
	// 		$("#debug").text(e.data);
	// 	}

	// 	$("#debug").text(e.data);
	// }
});

$('#d-scroll-link').on('click',function(){
	if ($(this).text() == 'scroll')
	{
		$('#d-recent-picks-div').css('overflowY','auto');
		$(this).text('lock');
	}
	else
	{
		$('#d-recent-picks-div').css('overflowY','hidden');
		$(this).text('scroll');
	}
});

$('#confirm-rank-reset').on('click',function(){
	$('#confirm-rank-reset-modal').foundation('close');
	var url="<?=site_url('season/draft/ajax_reset_player_ranks')?>"
	$.post(url,{}, function(data){
		console.log(data);
		if (data.success)
		{
			updatePlayerList("watch-list");
			updatePlayerList("draft-list");
		}
	},'json');
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
		else
		{
			$("#countdown").css('color','#000000');
			$("#countdown").data('warning',false);
		}
		$("#countdown").text(clocktext);
		$("#countdown").data('seconds',(timer-1));
	}
}

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

		if (vals[0] == "up" || vals[0] == "down")
		{
			//var e = "a[data-value='"+vals[0]+"_"+vals[1]+"']";
			e = "."+vals[0]+"-"+vals[1];
			updatePlayerList("watch-list");
			return;
		}
		<?php if($this->is_league_admin): ?>
		set_admin_picks(false);
		<?php endif;?>
		updatePlayerList("watch-list");
		updatePlayerList("draft-list");
		if(vals[0] == "draft"){loadMyTeam();}
	});
})


// function loadWatchList()
// {
// 	updatePlayerList("watch-list");
// }

// function updateBlock()
// {
// 	url = "<?=site_url('season/draft/ajax_get_block_info')?>";
// 	$.post(url,{},function(data){
// 	//	$("#on-the-block").css('background-color','');
// 		$("#countdown").css('color','');
// 		$("#on-the-block").html(data);
// 		//flash($("#on-the-block"))
// 		<?php if ($this->is_league_admin)
// 		{
// 			echo "$('#admin-picks').data('on',false);\n";
// 			echo "$('#draft-list').data('var1',false);\n";
// 			echo "updateAdminButtons();\n";
// 		}
// 		?>
// 	});
// }

// function updateRecentPicks()
// {
// 	var old_pick = $("#recent-top-row").data('pickid');
// 	url ="<?=site_url('season/draft/ajax_get_recent_picks')?>";
// 	$.post(url,{},function(data){

// 		$("#recent-picks").html(data);
// 		var new_pick = $("#recent-top-row").data('pickid');
// 		if (old_pick != new_pick){flash($("#recent-top-row"));}

// 	});
// }

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

function loadMyTeam()
{
	url ="<?=site_url('season/draft/ajax_get_myteam')?>";
	$.post(url,{},function(data){
		$('#myteam-list').html(data);
	});
}

function pad(n) {
    return (n < 10) ? ("0" + n) : n;
}

</script>

<?php if($this->is_league_admin): // All the admin javascript?>
	<script>


	function set_admin_picks(setting)
	{
		$("#admin-picks").data('on',setting);
		$("#draft-list").data('var1',setting);
		if (setting == true){$("#admin-picks").text("Cancel user Pick");}
		else {$("#admin-picks").text("Pick for User");}
		updatePlayerList("draft-list");
	}
	$("#admin-picks").on('click',function(){

		if($("#admin-picks").data('on'))
		{
			set_admin_picks(false);
			// $("#admin-picks").data('on',false);
			// $("#draft-list").data('var1',false);
			// $("#admin-picks").text("Pick for User");
			
			// updatePlayerList("draft-list");
		}
		else
		{
			set_admin_picks(true);
			// $("#admin-picks").data('on',true);
			// $("#draft-list").data('var1',true);
			// $("#admin-picks").text("Cancel user Pick");
			
			// updatePlayerList("draft-list");
		}

		//updateAdminButtons();
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
		//updateAdminButtons();
	});


	// function updateAdminButtons()
	// {
	// 	var paused = $("#countdown").data('paused');
	// 	var currenttime = $("#countdown").data('currenttime');
	// 	var starttime = $("#countdown").data('starttime');

	// 	if (starttime == "" || starttime > currenttime)
	// 	{$("#admin-pause-button").text("Start Draft");}
	// 	else if ((starttime < currenttime) && (!paused))
	// 	{$("#admin-pause-button").text("Pause Draft");}
	// 	 else if ((starttime < currenttime) && (paused))
	// 	{$("#admin-pause-button").text("Resume Draft");}

	// 	$("#admin-undo").attr("disabled",!paused);

	// 	if($("#admin-picks").data('on'))
	// 	{
	// 		$("#admin-picks").text("Cancel user Pick");
	// 		updatePlayerList("draft-list");
	// 		//$(".btn-draft:contains('Draft')").attr('disabled',false);
	// 	}
	// 	else
	// 	{
	// 		$("#admin-picks").text("Pick for User");
	// 		updatePlayerList("draft-list");
	// 		//$(".btn-draft:contains('Draft')").attr('disabled',true);
	// 	}

	// }

	</script>
<?php endif;?>
