<?php $this->load->view('template/modals/stat_popup'); ?>
<div class="container">

<h3>Propose Trade</h3>

<!-- Choose team modal -->
<div class="modal fade" id="choose-team-modal" aria-hidden="true" style="z-index:1060; top:25%">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<form class="navbar-form text-center">
					<div><h5>Trade with</h5></div>
					<div class="form-group text-center">
						<?=form_dropdown('',$team_options,'0','id="team-dropdown" class="form-control"')?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Request players modal -->
<div class="modal fade" id="request-players-modal" aria-hidden="true" style="z-index:1060; top:0%">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<div id="trade-with-team">
				</div>
				<div><button class="btn btn-default done-button">Done</button></div>
			</div>
		</div>
	</div>
</div>

<!-- Trade players modal -->
<div class="modal fade" id="trade-players-modal" aria-hidden="true" style="z-index:1060; top:0%">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<table class="table text-center table-border table-condensed table-striped">
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
		        			<button class="btn btn-default offer-btn btn-sm" value="<?=$r->player_id?>" data-name="<?=$name?>"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
						</td>
					</tr>
					<?php endforeach; ?>
						</tbody>
				</table>
				<div><button class="btn btn-default done-button">Done</button></div>
			</div>
		</div>
	</div>
</div>

	<div class="hide trade-form row">
		<div class="col-sm-6">

			<table class="table table-striped table-condensed table-border">
				<thead>
					<th>
						<div class="text-center"><h4><?=$team_name?></h4></div>
						<div class="text-center"><h4><a href="#" id="trade-players"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> Add/Remove</a></h4></div>
					</th>
				</thead>
				<tbody id="trade-offer-text" class="text-center">

				</tbody>
			</table>
		</div>

		<div class="col-sm-6">


			<table class="table table-striped table-condensed table-border">
				<thead>
					<th class="text-center">
						<h4><span class="text-center" id="request-team-name"></span> <a href="#" id="trade-title"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></h4>
						<div class="text-center"><h4><a id="request-players" href="#"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> Add/Remove</a></h4></div>
					</th>
				</thead>
				<tbody id="trade-request-text" class="text-center">

				</tbody>
			</table>

		</div>
	</div>
	<div class="row">
		<form class="navbar-form text-center hide trade-form">
			<div class="form-group">
				<select id="trade-expire" class="form-control">
					<option value="<?=time()+(60*60*6)?>">Expires in 6 hours</option>
					<option value="<?=time()+(60*60*12)?>">Expires in 12 hours</option>
					<option value="<?=time()+(60*60*24)?>">Expires in 24 hours</option>
					<option value="<?=time()+(60*60*48)?>">Expires in 48 hours</option>
				</select>
			</div>

			<button id="send-trade-offer" class="btn btn-default" type="button">
			Send Trade offer
			</button>
		</form>
	</div>

</div>

<script>
$(document).ready(function(){

$("#choose-team-modal").modal('show');

$("#request-players").click(function(){
	$("#request-players-modal").modal("show");
	event.stopPropagation();
});

$("#trade-players").click(function(){
	$("#trade-players-modal").modal("show");
	event.stopPropagation();
});

$("#trade-title").click(function(){
	$("#choose-team-modal").modal('show');
})

$(".done-button").click(function(){
	$("#request-players-modal").modal("hide");
	$("#trade-players-modal").modal("hide");
});

$("#team-dropdown").on("change",function(){
	url = "<?=site_url('myteam/trade/ajax_get_team_roster')?>";
	$.post(url,{'team_id' : $("#team-dropdown").val()},function(data){ $("#trade-with-team").html(data); });
	$(".footer-nav-item").each(function(){
		$(this).removeClass('hide');
	});
	// Reset a bunch of stuff.
	$("#choose-team-modal").modal('hide');
	$("#request-team-name").text($("#team-dropdown option:selected").text())
	$("#trade-offer-text").html('');
	$("#trade-request-text").html('');
	$(".trade-form").removeClass('hide');
	$("button.offer-btn").removeClass("active");
	$("button.request-btn").removeClass("active");

})

// Update list of offered players
$("button.offer-btn").on("click",function(){
	$(this).toggleClass("active");
	updateList("offer");
});

// Update list of requested players
$("#trade-with-team").on("click","button.request-btn",function(){
	$(this).toggleClass("active");
	updateList("request");
});


$("#send-trade-offer").click(function(){
	var myteam = [];
	$("input:checkbox[name=myteam-checkbox]:checked").each(function(i){ myteam[i] = $(this).val(); });
	var request = [];
	$("input:checkbox[name=request-checkbox]:checked").each(function(i){ request[i] = $(this).val(); });
	$(".offer-btn.active").each(function(i){myteam[i] = $(this).val(); });
	$(".request-btn.active").each(function(i){request[i] = $(this).val(); });
	var other_team = "";
	other_team = ($("#team-dropdown").val());
	var trade_expire = $("#trade-expire").val();

	url = "<?=site_url('myteam/trade/submit_trade_offer')?>";
	$.post(url,{'offer' : myteam, 'request' : request, 'other_team' : other_team, 'trade_expire': trade_expire}, function(){
		showMessage("Trade request sent.",'alert-success');
		setTimeout(function(){
			window.location.replace("<?=site_url('myteam/trade')?>");
		}, 3000);

	});
});

});

function updateList(classname)
{

	var html = ""
	$("button."+classname+"-btn").each(function(){
		if($(this).hasClass("active"))
		{
			html = html +'<tr><td>'+$(this).data("name")+"</td></tr>";
		}
	})
	$("#trade-"+classname+"-text").html(html);
}

</script>
