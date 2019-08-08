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
<?php elseif ($draft_end == $this->session->userdata('current_year')): ?>
	<div class="section">
		<div class="container">
			The <?=$draft_end?> draft is over.
		</div>
	</div>
<?php else: ?>

<!-- Confirm rank reset modal -->
<?php 
// Drop modal
$body =     '	<div class="text-center">This will clear your current watch list!</div>
				<br>
				<div>
					<button class="button is-link is-small" type="button" id="confirm-watch-clear">
						Confirm
					</button>
					<button class="button is-link is-small modal-close" type-"button" id="cancel-watch-clear">
						Cancel
					</button>
				</div>';

			fflp_modal('confirm-watch-clear-modal','Are you sure?',$body);

?>

<div class="section">
<?php if($this->is_league_admin):?>

	<?=fflp_html_block_begin()?>
		<div class="columns">
			<div class="column is-narrow is-size-6">
				Draft Admin
			</div>

			<div class="column has-text-right">
				<button class="button is-small is-link" id="admin-pause-button">Start Draft</button>
				<button class="button is-small is-link" id="admin-picks" data-on="">Pick for User</button>
				<button class="button is-small is-link" disabled id="admin-undo">Undo Last Pick</button>
			</div>
		</div>
	<?=fflp_html_block_end()?>

<?php endif;?>

	<!-- Top row with Now Picking and Recent picks -->

	<?=fflp_html_block_begin()?>
		<div class="columns">
			<div class="column is-2 has-text-centered">
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
			</div>

			<div class="column">
				<div id="d-recent-picks-div" style="max-height:190px;overflow-Y:hidden">
					<div class="text-center hide"><a href="<?=site_url('season/draft')?>" target="_blank">
						Recent Picks</a>
					</div>
					<table class="table is-narrow is-fullwidth is-striped small-text">
						<thead>
							<th>Overall</th><th>Round</th><th>Player</th><th>Team</th><th>Owner</th>
						</thead>
						<tbody id="recent-picks">

						</tbody>
					</table>
				</div>
				<div class="text-center" style="font-size:.9em;cursor:pointer"><a id="d-scroll-link">scroll</a></div>
			</div>
		</div>
	<?=fflp_html_block_end()?>



<!-- Row with draft search, watch list -->
	<?=fflp_html_block_begin()?>

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

			$headers['Rank'] = array('by' => 'rank', 'order' => 'asc');
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

			
			fflp_player_search_table('draft-list',
									site_url('load_content/draft_player_list'),
									'asc',
									'rank',
									$pos_dropdown,
									$headers,
									$classes='is-striped small-text',
									$disable_search=False,
									$per_page=10,
									$check=array('text'=>"Hide Drafted",'checked'=>true));

			?>
		</div>

		<!-- watch list -->
		<div id="draft-watch-list-tab" class="is-hidden">

			<?php if($this->session->userdata('use_draft_ranks')): ?>
			<div class="has-text-centered">
					<small><a onclick='$("#confirm-watch-clear-modal").addClass("is-active");'>(clear watch list)</a></small>
			</div>
			<?php endif;?>

			<?php //Show the player list using player_search_table component
			$headers = array();
			$headers['Rank'] = array('classes'=>array('has-text-centered'));
			$headers['Action'] = array();
			$headers['Player'] = array();


			$pos_dropdown['All'] = 0;
			foreach($pos as $p)
				$pos_dropdown[$p->text_id] = $p->id;


			fflp_player_search_table('watch-list',
				site_url('load_content/draft_watch_list'),
				$order='asc',
				$by='rank_order',
				$pos_dropdown,
				$headers,
				$classes='is-striped small-text f-min-width-small',
				$disable_search=True)

			?>
		</div>

		<div id="draft-myteam-list-tab" class="is-hidden f-scrollbar">
			<!-- <div class="d-myteam-heading text-center"><h5>My Team</h5></div> -->
			<table class="table is-narrow is-fullwidth is-striped small-text">
				<thead>
					<th class="text-center">Player Name</th><th class="text-center">Team/Pos</th><th class="text-center">Bye</th><th class="text-center">Pick</th><th class="text-center hide-for-small-only">Round</th>
				</thead>
				<tbody id="myteam-list" data-url="<?=site_url('season/draft/ajax_get_myteam')?>">
        		</tbody>
				<tbody >
				</tbody>
			</table>
		</div>

	<?=fflp_html_block_end()?>
</div>

<div id="debug" class="text-center hide"></div>

<?php endif; ?>

<script>

$(document).ready(function(){

	//$.post("<?=site_url('season/draft/ajax_get_update_key')?>"); // in case of stale key, force update on load
	
	$(loadContent('draft-list'));
	$(loadContent('watch-list'));
	
	updateTimer();

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

$('#confirm-watch-clear').on('click',function(){
	$('#confirm-watch-clear-modal').removeClass('is-active');
	var url="<?=site_url('season/draft/ajax_clear_watch_list')?>"
	$.post(url,{}, function(data){
		if (data.success)
		{
			$(loadContent('draft-list'));
			$(loadContent('watch-list'));
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
	var off = $("#countdown").data('off');

	hr = parseInt(timer / 60 / 60);
	min = parseInt((timer-(hr*60*60)) / 60);
	sec = parseInt((timer-(hr*60*60)-(min*60)));
	var clocktext = min+":"+pad(sec);

	if (off)
	{
		$("#countdown").text('');
	}
	else if (paused)
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
			//updatePlayerList("watch-list");
			$(loadContent('watch-list'));
			return;
		}
		<?php if($this->is_league_admin): ?>
		set_admin_picks(false);
		<?php endif;?>
		$(loadContent('draft-list'));
		$(loadContent('watch-list'));
		if(vals[0] == "draft"){loadMyTeam();}
	});
})

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
		$(loadContent('draft-list'));
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

	</script>
<?php endif;?>
