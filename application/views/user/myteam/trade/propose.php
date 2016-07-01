<?php $this->load->view('template/modals/stat_popup'); ?>
<!-- Choose team modal -->
<div class="reveal tiny" id="choose-team-modal" data-reveal data-overlay="true">

	<form class="navbar-form text-center">
		<div><h5>Trade with</h5></div>
		<div class="form-group text-center">
			<?=form_dropdown('',$team_options,'0','id="team-dropdown" class="form-control"')?>
		</div>
	</form>
	<button class="close-button" data-close aria-label="Close modal" type="button">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>

<?php // Add modals for trading draft picks ?>
<?php if($settings['trade_draft_picks']): ?>
	<!-- Request picks modal -->
	<div class="reveal" id="request-picks-modal" data-reveal data-overlay="true">
		<div class="row">
			<div class="columns">
				<h5 class="text-center">Pick request</h5>
			</div>
		</div>
		<select id="requestpick-year">

		</select>
		<table class="text-center table-condensed">
				<thead>
					<th class="text-center">Round</th><th class="text-center">Pick</th><th></th>
				</thead>
				<tbody id="requestpicks">

				</tbody>
		</table>
		<div id="trade-picks-with-team">
		</div>
		<div><button class="button done-button text-center">Done</button></div>

		<button class="close-button" data-close aria-label="Close modal" type="button">
		  <span aria-hidden="true">&times;</span>
		</button>
	</div>

	<!-- Trade picks modal -->
	<div class="reveal" id="offer-picks-modal" data-reveal data-overlay="true">
		<div class="row">
			<div class="columns">
				<h5 class="text-center">Picks offer</h5>
			</div>
		</div>
		<select id="offerpick-year">
			<?php foreach($pick_years as $p): ?>
				<option value="<?=$p?>" <?php if($p == $pick_year){echo "selected";}?>><?=$p?></option>
			<?php endforeach;?>
		</select>
		<table class="text-center table-condensed">
				<thead>
					<th class="text-center">Round</th><th class="text-center">Pick</th><th></th>
				</thead>
				<tbody id="offerpicks">

				</tbody>
		</table>
		<div class="text-center"><button class="button done-button">Done</button></div>
		<button class="close-button" data-close aria-label="Close modal" type="button">
		  <span aria-hidden="true">&times;</span>
		</button>
	</div>



<?php endif;?>

<!-- Request players modal -->
<div class="reveal" id="request-players-modal" data-reveal data-overlay="true">
	<div class="row">
		<div class="columns">
			<h5 class="text-center">Trade request</h5>
		</div>
	</div>
	<div id="trade-with-team">
	</div>
	<div><button class="button done-button text-center">Done</button></div>

	<button class="close-button" data-close aria-label="Close modal" type="button">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>

<!-- Trade players modal -->
<div class="reveal" id="trade-players-modal" data-reveal data-overlay="true">
	<div class="row">
		<div class="columns">
			<h5 class="text-center">Trade offer</h5>
		</div>
	</div>
	<table class="text-center table-condensed">
			<thead>
				<th class="text-center">Pos</th><th class="text-center">Player</th><th></th>
			</thead>
			<tbody>
				<?php foreach ($roster as $r): ?>
				<tr>
					<td><?=$r->pos?></td>
					<td>
			        <div>
			                <?php if(strlen($r->first_name.$r->last_name) > 12){$name = $r->short_name; }
			                      else{$name = $r->first_name." ".$r->last_name;} ?>
			            <a href="#" class="stat-popup" data-type="player" data-id="<?=$r->player_id?>"><?=$name?></a> - <?=$r->club_id?>
			        </div>
		    		</td>
		    		<td>
		    			<button class="button offer-btn small" value="<?=$r->player_id?>" data-name="<?=$name?>">Select</button>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
	</table>
	<div class="text-center"><button class="button done-button">Done</button></div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>


<div class="hide trade-form row">
	<div class="small-12 medium-6">
		<table class="table-condensed">
			<thead>
				<th>
					<div class="text-center"><h5><?=$team_name?></h5></div>
					<div class="text-center"><h6><a href="#" id="trade-players">Add/Remove Players</a></h6></div>
				</th>
			</thead>
			<tbody id="trade-offer-text" class="text-center">
			</tbody>
		</table>
		<?php if($settings['trade_draft_picks']): ?>
		<table class="table-condensed">
			<thead>
				<th>
					<div class="text-center"><h6><a href="#" id="offer-picks">Add/Remove Draft Picks</a></h6></div>
				</th>
			</thead>
			<tbody id="pick-offer-text" class="text-center">

			</tbody>
		</table>
		<?php endif;?>
	</div>

	<div class="small-12 medium-6">
		<table class="table-condensed">
			<thead>
				<th class="text-center">
					<h5><span class="text-center" id="request-team-name"></span><span style="font-size:.5em"><a href="#" id="trade-title"> (change)</a></h5></span>
					<div class="text-center"><h6><a id="request-players" href="#">Add/Remove Players</a></h6></div>
				</th>
			</thead>
			<tbody id="trade-request-text" class="text-center">

			</tbody>
		</table>
		<?php if($settings['trade_draft_picks']): ?>
			<table class="table-condensed">
				<thead>
					<th class="text-center"><div class="text-center"><h6><a href="#" id="request-picks">Add/Remove Draft Picks</a></h6></div></th>
				</thead>
				<tbody id="pick-request-text" class="text-center">

				</tbody>
			</table>
		<?php endif;?>

	</div>
</div>
<div class="row" style="max-width:325px;">
	<div class="column">
		<form class="navbar-form text-center hide trade-form">
			<div class="form-group">
				<select id="trade-expire" class="form-control">
					<option value="<?=time()+(60*60*6)?>">Expires in 6 hours</option>
					<option value="<?=time()+(60*60*12)?>">Expires in 12 hours</option>
					<option value="<?=time()+(60*60*24)?>">Expires in 24 hours</option>
					<option value="<?=time()+(60*60*48)?>">Expires in 48 hours</option>
				</select>
			</div>

			<button id="send-trade-offer" class="button" type="button">
			Send Trade offer
			</button>
		</form>
	</div>
</div>



<script>
$(document).ready(function(){


$("#choose-team-modal").foundation('open');

$("#request-players").click(function(){
	$("#request-players-modal").foundation("open");
	event.stopPropagation();
});

$("#trade-players").click(function(){
	$("#trade-players-modal").foundation("open");
	event.stopPropagation();
});

<?php if($settings['trade_draft_picks']): ?>

// When the button to bring up modal for other teams picks is clicked
$("#request-picks").click(function(){
	// otherpicks, otherpick-year
	if ($("#requestpicks").html().trim() == "")
	{
		updatePicks("request", $("#team-dropdown").val());
		$.post("<?=site_url('myteam/trade/ajax_get_pick_years')?>",{"teamid":$("#team-dropdown").val()},function(data){
			$("#requestpick-year").html(data);
		});
	}
	$("#request-picks-modal").foundation("open");
	event.stopPropagation();
});

// When year changes on trade my picks
$("#offerpick-year").on('change',function(){
	updatePicks("offer","<?=$this->session->userdata('team_id')?>");
});

$("#requestpick-year").on('change',function(){
	updatePicks("request",$("#team-dropdown").val());
});

// When the button to bring up the modal is clicked.
$("#offer-picks").click(function(){
	if ($("#offerpicks").html().trim() == "")
	{updatePicks("offer", "<?=$this->session->userdata('team_id')?>");}
	$("#offer-picks-modal").foundation("open");
});

// When a mypick is selected
$("#offerpicks").on('click','.pick-btn',function(){
	var year = $("#offerpick-year").val();
	var round = $(this).data('round');
	var pick = $(this).data('pick');
	var pickid = $(this).data('id');
	var future = $(this).data('future');
	toggleSelected($(this));
	togglePickList("offer", year, round, pick, pickid, future);
});

// When a request is selected
$("#requestpicks").on('click','.pick-btn',function(){
	var year = $("#requestpick-year").val();
	var round = $(this).data('round');
	var pick = $(this).data('pick');
	var pickid = $(this).data('id');
	var future = $(this).data('future');
	toggleSelected($(this));
	togglePickList("request", year, round, pick, pickid, future);
});

// When the done button is click
// $("#offerpicks").on('click','.done-button',function(){
// 	$("#request-players-modal").foundation("close");
// 	$("#trade-players-modal").foundation("close");
// 	$("#request-picks-modal").foundation("close");
// 	$("#offer-picks-modal").foundation("close");
// });

function updatePicks(whichone, teamid)
{
	var url="<?=site_url('myteam/trade/ajax_get_picks')?>";
	var year = $("#"+whichone+"pick-year").val();
	$.post(url,{'year':year,'teamid':teamid},function(data){
		$("#"+whichone+"picks").html(data);
		$.each($("."+whichone+"-btn"),function(){
			var id = whichone+"-"+$(this).data('year')+"-"+$(this).data('round');
			if($("#"+id).length > 0)
			{toggleSelected($(this));}
		});
	});
}

function togglePickList(whichone, year, round, pick, pickid, future)
{
	//pick-offer-text, pick-request-text
	var id = whichone+"-"+pickid+"-"+year+"-"+round+"-"+future;
	var tr_class = "pick-"+whichone
	if ($("#"+id).length == 0)
	{
		var html = '<tr id="'+id+'" class="'+tr_class+'"><td>'+year+'</td><td>'+round+'</td></tr>';
		$("#pick-"+whichone+"-text").append(html);
	}
	else{$("#"+id).remove();}
}

<?php endif;?>

$("#trade-title").click(function(){
	$("#choose-team-modal").foundation('open');
})

$(".done-button").click(function(){
	$("#request-players-modal").foundation("close");
	$("#trade-players-modal").foundation("close");
<?php if($settings['trade_draft_picks']): ?>
	$("#request-picks-modal").foundation("close");
	$("#offer-picks-modal").foundation("close");
<?php endif;?>
});

$("#team-dropdown").on("change",function(){
	url = "<?=site_url('myteam/trade/ajax_get_team_roster')?>";
	$.post(url,{'team_id' : $("#team-dropdown").val()},function(data){ $("#trade-with-team").html(data); });
	$(".footer-nav-item").each(function(){
		$(this).removeClass('hide');
	});
	// Reset a bunch of stuff.
	$("#choose-team-modal").foundation('close');
	$("#request-team-name").text($("#team-dropdown option:selected").text())
	$("#trade-offer-text").html('');
	$("#trade-request-text").html('');
	$(".trade-form").removeClass('hide');
	$("button.offer-btn").removeClass("secondary");
	$("button.request-btn").removeClass("secondary");
	$("button.offer-btn").text("Select");
	$("button.request-btn").text("Select");
	<?php if($settings['trade_draft_picks']): ?>
	$("button.pick-btn").text("Select");
	$("button.pick-btn").removeClass("secondary");
	console.log($("#offerpicks").html());
	$("#pick-request-text").html('');
	$("#pick-offer-text").html('');
	console.log("Reset");
	<?php endif;?>

})

// Update list of offered players
$("button.offer-btn").on("click",function(){
	toggleSelected($(this));
	updateList("offer");
});

// Update list of requested players
$("#trade-with-team").on("click","button.request-btn",function(){
	toggleSelected($(this));
	updateList("request");
});

function toggleSelected(element)
{
	element.toggleClass("secondary");
	if (element.text() == "Select"){element.text("Remove");}
	else {element.text("Select");}
}

$("#send-trade-offer").click(function(){
	var myteam = [];
	$("input:checkbox[name=myteam-checkbox]:checked").each(function(i){ myteam[i] = $(this).val(); });
	var request = [];
	$("input:checkbox[name=request-checkbox]:checked").each(function(i){ request[i] = $(this).val(); });
	$(".offer-btn.secondary").each(function(i){myteam[i] = $(this).val(); });
	$(".request-btn.secondary").each(function(i){request[i] = $(this).val(); });
	var other_team = "";
	other_team = ($("#team-dropdown").val());
	var trade_expire = $("#trade-expire").val();

	var request_picks = [];
	var offer_picks = [];
	$(".pick-offer").each(function(i){offer_picks[i] = $(this).attr('id');})
	$(".pick-request").each(function(i){request_picks[i] = $(this).attr('id');})

	var url = "<?=site_url('myteam/trade/submit_trade_offer')?>";
	if (myteam.length > 0 || request.length > 0 || request_picks.length >0 || offer_picks.length > 0)
	{
		$.post(url,{'offer' : myteam, 'request' : request, 'other_team' : other_team, 'trade_expire': trade_expire,
		 			'request_picks' : request_picks, 'offer_picks' : offer_picks}, function(data){

			$("#send-trade-offer").addClass("disabled");
			notice("Trade request sent.",'success');
			setTimeout(function(){
				window.location.replace("<?=site_url('myteam/trade')?>");
			}, 2000);

		});
	}
});

});

function updateList(classname)
{
	var html = ""
	$("button."+classname+"-btn").each(function(){
		if($(this).hasClass("secondary"))
		{
			html = html +'<tr><td>'+$(this).data("name")+"</td></tr>";
		}
	})
	$("#trade-"+classname+"-text").html(html);
}

</script>
