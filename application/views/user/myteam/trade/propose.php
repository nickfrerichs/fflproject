<?php $this->load->view('template/modals/stat_popup'); ?>

<!-- Choose team modal -->
<div class="reveal" id="choose-team-modal" data-reveal data-overlay="true">

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

<!-- Request players modal -->
<div class="reveal" id="request-players-modal" data-reveal data-overlay="true">
	<div id="trade-with-team">
	</div>
	<div><button class="button done-button">Done</button></div>

	<button class="close-button" data-close aria-label="Close modal" type="button">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>

<!-- Trade players modal -->
<div class="reveal" id="trade-players-modal" data-reveal data-overlay="true">
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
<!--
								<span class="switch">
									<input class="switch-input" id="<?=$r->player_id?>-switch" type="checkbox">
									<label class="switch-paddle" for="<?=$r->player_id?>-switch">
									</label>
								</span>
-->

					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
	</table>
	<div><button class="button done-button">Done</button></div>
	<button class="close-button" data-close aria-label="Close modal" type="button">
	  <span aria-hidden="true">&times;</span>
	</button>
</div>

<div class="row">
	<div class="column">
		<h5>Propose Trade</h5>
	</div>
</div>


<div class="hide trade-form row">
	<div class="small-12 medium-6">
		<table class="table-condensed">
			<thead>
				<th>
					<div class="text-center"><h4><?=$team_name?></h4></div>
					<div class="text-center"><h4><a href="#" id="trade-players">Add/Remove</a></h4></div>
				</th>
			</thead>
			<tbody id="trade-offer-text" class="text-center">

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-6">
		<table class="table-condensed">
			<thead>
				<th class="text-center">
					<h4><span class="text-center" id="request-team-name"></span> <a href="#" id="trade-title">Blah</a></h4>
					<div class="text-center"><h4><a id="request-players" href="#">Add/Remove</a></h4></div>
				</th>
			</thead>
			<tbody id="trade-request-text" class="text-center">

			</tbody>
		</table>

	</div>
</div>
<div class="row">
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

$("#trade-title").click(function(){
	$("#choose-team-modal").foundation('open');
})

$(".done-button").click(function(){
	$("#request-players-modal").foundation("close");
	$("#trade-players-modal").foundation("close");
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

	url = "<?=site_url('myteam/trade/submit_trade_offer')?>";
	$.post(url,{'offer' : myteam, 'request' : request, 'other_team' : other_team, 'trade_expire': trade_expire}, function(){
		$("#send-trade-offer").addClass("disabled");
		notice("Trade request sent.",'success');
		setTimeout(function(){
			window.location.replace("<?=site_url('myteam/trade')?>");
		}, 2000);

	});
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
