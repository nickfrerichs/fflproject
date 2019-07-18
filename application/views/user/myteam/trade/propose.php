<?php //$this->load->view('components/stat_popup'); ?>
<?php
// Choose team modal
$body = '<div class="select"><select id="team-dropdown">';
foreach($team_options as $o => $t)
{
	$body .= '<option value="'.$o.'">'.$t.'</option>';
}
$body .= '</select></div>';

$this->load->view('components/modal', array('id' => 'choose-team-modal',
                                                    'title' => 'Trade with team:',
                                                    'body' => $body,
                                                    'reload_on_close' => False));
?>

<?php // Add modals for trading draft picks ?>
<?php if($settings['trade_draft_picks']): ?>

	<?php 
	// Request picks modal
	$body = '	<div class="select">
				<select id="requestpick-year">

				</select>
				</div>
				<div class="f-scrollbar">
					<table class="table is-fullwidth f-min-width-small is-size-7-mobile">
							<thead>
								<th class="text-center">Round</th><th class="text-center">Pick</th><th></th>
							</thead>
							<tbody id="requestpicks">

							</tbody>
					</table>
				</div>
				<div id="trade-picks-with-team">
				</div>';

	$this->load->view('components/modal', array('id' => 'request-picks-modal',
														'title' => 'Pick request',
														'body' => $body,
														'reload_on_close' => False));

	// Offer picks modal
	$body = '
			<div class="select">
			<select id="offerpick-year">';
				if ($pick_years)
				{
					foreach($pick_years as $p)
					{
						$body.='<option value="'.$p.'"';
						if($p == $pick_year){$body.= "selected";}
						$body.='>'.$p.'</option>';
					}
				}
				else
				{
					$body.= '<option value="sd">N/A</option>';
				}

	$body.='</select>
			</div>
			<div class="f-scrollbar">
				<table class="table is-fullwidth f-min-width-small is-size-7-mobile">
						<thead>
							<th class="text-center">Round</th><th class="text-center">Pick</th><th></th>
						</thead>
						<tbody id="offerpicks">

						</tbody>
				</table>
			</div>';
		

	$this->load->view('components/modal', array('id' => 'offer-picks-modal',
														'title' => 'Pick offer',
														'body' => $body,
														'reload_on_close' => False));
														

	?>
<?php endif;?>

<?php 
// Request players modal
$body = '<div id="trade-with-team">
		 </div>
		 <div>';

$this->load->view('components/modal', array('id' => 'request-players-modal',
                                                    'title' => 'Trade request',
                                                    'body' => $body,
                                                    'reload_on_close' => False));

// Trade players modal
$body = '<div id="trade-my-team">
		 </div>
		 <div>';

$this->load->view('components/modal', array('id' => 'trade-players-modal',
                                                    'title' => 'Trade offer',
                                                    'body' => $body,
                                                    'reload_on_close' => False));



?>

<div class="section">
	<div class="columns">
		<div class="column">
			<table class="table is-fullwidth is-narrow">
				<thead>
					<th>
						<div class="is-size-5"><?=$team_name?></div>
						<div class="is-size-6"><a href="#" id="trade-players">Add/Remove Players</a></div>
					</th>
				</thead>
				<tbody id="trade-offer-text" class="text-center">
				</tbody>
			</table>
			<?php if($settings['trade_draft_picks']): ?>
			<table class="table is-fullwidth">
				<thead>
					<th colspan=2>
						<div class="is-size-6"><a href="#" id="offer-picks">Add/Remove Draft Picks</a></div>
					</th>
				</thead>
				<tbody id="pick-offer-text" class="text-center">

				</tbody>
			</table>
			<?php endif;?>
		</div>

		<div class="column">
			<table class="table is-fullwidth is-narrow">
				<thead>
					<th class="text-center">
						<span class="is-size-5" id="request-team-name"></span><span style="font-size:.8em"><a href="#" id="trade-title"> (change)</a></span>
						<div class="is-size-6"><a id="request-players" href="#">Add/Remove Players</a></div>
					</th>
				</thead>
				<tbody id="trade-request-text" class="text-center">

				</tbody>
			</table>
			<?php if($settings['trade_draft_picks']): ?>
				<table class="table is-fullwidth">
					<thead>
						<th class="text-center" colspan=2><div class="is-size-6"><a href="#" id="request-picks">Add/Remove Draft Picks</a></div></th>
					</thead>
					<tbody id="pick-request-text" class="text-center">

					</tbody>
				</table>
			<?php endif;?>
		</div>
	</div>

	<div class="columns is-centered">
		<div class="column is-half has-text-centered">
			<div class="field">
				<div class="control has-text-centered">
					<div class="select is-expanded">
						<select id="trade-expire" class="is-expanded">
							<option value="<?=time()+(60*60*6)?>">Expires in 6 hours</option>
							<option value="<?=time()+(60*60*12)?>">Expires in 12 hours</option>
							<option value="<?=time()+(60*60*24)?>">Expires in 24 hours</option>
							<option value="<?=time()+(60*60*48)?>">Expires in 48 hours</option>
						</select>
					</div>
				</div>
			</div>
			<div class="field">
				<div class="control has-text-centered">
					<button id="send-trade-offer" class="button is-link is-small is-centered" type="button">
					Send Trade offer
					</button>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
$(document).ready(function(){


$("#choose-team-modal").addClass('is-active');

$("#request-players").click(function(){
	//$("#request-players-modal").foundation("open");
	$("#request-players-modal").addClass('is-active')
	event.stopPropagation();
});

$("#trade-players").click(function(){
	//$("#trade-players-modal").foundation("open");
	$("#trade-players-modal").addClass('is-active');
	event.stopPropagation();
});

<?php if($settings['trade_draft_picks']): ?>

// When the button to bring up modal for other teams picks is clicked
$("#request-picks").click(function(){
	// otherpicks, otherpick-year
	if ($("#requestpicks").html().trim() == "")
	{

		$.post("<?=site_url('myteam/trade/ajax_get_pick_years')?>",{"teamid":$("#team-dropdown").val()},function(data){
			$("#requestpick-year").html(data);
			updatePicks("request", $("#team-dropdown").val());
		});
	}
	$("#request-picks-modal").addClass("is-active");
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
	$("#offer-picks-modal").addClass('is-active');
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
	console.log("here");
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
	//$("#choose-team-modal").foundation('open');
	$("#choose-team-modal").addClass('is-active');
	
})

$(".done-button").click(function(){
	//$("#request-players-modal").foundation("close");
	$("#request-players-modal").removeClass('is-active');
	$("#trade-players-modal").removeClass('is-active');
<?php if($settings['trade_draft_picks']): ?>
	$("#request-picks-modal").removeClass("is-active");
	$("#offer-picks-modal").removeClass("is-active");
<?php endif;?>
});

$("#team-dropdown").on("change",function(){
	url = "<?=site_url('myteam/trade/ajax_get_team_roster')?>";
	$.post(url,{'team_id' : $("#team-dropdown").val()},function(data){ $("#trade-with-team").html(data); });
	// $(".footer-nav-item").each(function(){
	// 	$(this).removeClass('hide');
	// });

	url = "<?=site_url('myteam/trade/ajax_get_my_roster')?>";
	$.post(url,{},function(data){ $("#trade-my-team").html(data); });

	// Reset a bunch of stuff.
	$("#choose-team-modal").removeClass('is-active');
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
$("#trade-my-team").on("click","button.offer-btn",function(){
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
	element.toggleClass("is-link");
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
		 			'request_picks' : request_picks, 'offer_picks' : offer_picks}, function(data)
		{
			console.log(data);
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
