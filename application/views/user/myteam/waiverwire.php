<?php $this->load->view('template/modals/stat_popup'); ?>

<div class="container">
	<div><h3>Waiver wire</h3></div>
	<div class="row">
		<div style="float:right"><a href="<?=site_url('myteam/waiverwire/priority')?>">Waiver Wire Priority</a> | <a href="<?=site_url('myteam/waiverwire/log')?>">Waiver Wire Log</a></div>
	</div>
	<hr>
	<div class="row">

		<!-- Confirm modal -->
		<div class="modal fade" id="confirm-modal" aria-hidden="true" style="z-index:1060; top:25%">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
							<div class="drop-text">Drop: No One</div>
							<div class="pickup-text">Pick up: No One</div>
						<button class="btn btn-default" type="button" id="confirm-drop">
							Confirm
						</button>
						<button class="btn btn-default" type-"button" id="cancel-drop" data-dismiss="modal">
							Cancel
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Error modal -->
		<div class="modal fade" id="error-modal" aria-hidden="true" style="z-index:1060; top:25%">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<div class="modal-body text-center">
							<div class="drop-text">Drop: No One</div>
							<div class="pickup-text">Pick up: No One</div>
							<div id="error-text"></div>
						<button class="btn btn-default" type-"button" id="cancel-drop" data-dismiss="modal">
							OK
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Drop modal -->
		<div class="modal fade" id="drop-modal" aria-hidden="true" style="z-index:1050">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body text-center">
							<div>
								<div class="text-center">
									<h4>Drop Player</h4>
									<h5>My Team Roster</h5>
								</div>
								<table class="table table-border table-condensed text-center table-striped table-hover">
									<thead>
										<th colspan=3 class="text-center">Player</th>
									</thead>
								<tbody id="ww-drop-table" data-playerid="0">

								</tbody>
								</table>
								<button class="btn btn-default" type-"button" id="cancel-drop" data-dismiss="modal">
									Cancel
								</button>
							</div>
					</div>
				</div>
			</div>
		</div>


		<div class="col-sm-12 text-center">
			<div>
				<div class="table-heading text-center"><h4>Available Players</h4></div>

				<!-- Search options -->
				<div class="row">
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
				</div> <!-- row -->

				<!-- Pick Up table -->
				<div class="row">
					<div class="col-xs-12">
						<table class="table table-border table-condensed text-center table-striped">
						<thead>
							<!--<th class="text-center">Select</th><th class="text-center">Pos</th>-->
							<th class="text-center">Pos</th>
							<th class="text-center">Player</th>
							<th class="text-center">Team</th>
							<th class="text-center">Opponent</th>
							<th class="text-center">Points</th>
							<th></th>
						<tbody id="ww-pickup-table" data-playerid="0">

						</tbody>
					</table>
					</div>
				</div>

				<!-- Prev/Next buttons -->
				<div class="row">
					<div class="btn-group btn-group-justified col-xs-12" data-toggle="buttons">
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
				</div>
				<br>
				<div class="row">
					<button id="drop-only" class="btn btn-default">Drop Player</button>
				</div>
			</div>

			<!-- Status text -->
			<div id="ww-status" class="col-xs-12 text-center pad-tb-5" style="opacity:0; color:blue;">Status</div>

		</div> <!-- col-sm-8 -->

		<!-- Drop table -->
		<!--
		<div class="col-sm-4 text-center footer-nav-item" data-nav-name="Drop">
			<div class="text-center"><h4>Team Roster</h4></div>
			<table class="table table-border table-condensed text-center table-striped">
				<thead>
					<th>Drop</th><th>Player</th>
				</thead>
			<tbody id="ww-drop-table">

			</tbody>
			</table>

		</div>
		-->
	</div> <!-- row -->
</div> <!-- container -->



<script>
$(document).ready(function(){
	reloadPage();
	pickupSearch(1,'0','points','');

	$("#drop-only").on('click',function(){
		$("#drop-modal").modal('show');
	});

	// Name search event
	var timer;
	$("#search-name").on("input",function(event){
		clearTimeout(timer);
		var delay = 500;
		timer = setTimeout(function(){
			pickupSearch(getpage(),getpos(),getsort(),getsearch());
		},delay);
	});

	// Position & sort event
	$('.sort-group').on('change', function(){ pickupSearch(1,getpos(),getsort(),getsearch()); });

	// Prev/Next button events
	$("#next").click(function(){
		var page = getpage();
		var next = page+2;
		var prev = page;
		if ((page)*<?=$per_page?> <= gettotal())
		{
			pickupSearch(page+1,getpos(),getsort(),getsearch());
			$("#next").val(next.toString());
			$("#prev").val(prev.toString());
		}
		event.stopImmediatePropagation();
	});
	$("#prev").click(function(){
		var next = parseInt($('#next').val())-1;
		var prev = parseInt($('#prev').val())-1;
		var page = getpage();
		if (page > 1)
		{
			pickupSearch(page-1,getpos(),getsort(),getsearch());
			$("#next").val(next);
			$("#prev").val(prev);
		}

		event.stopImmediatePropagation();
		$(this).removeClass('active');
	});

	// Pick up table button click
	$("#ww-pickup-table").on("click","button.player-pickup",function(){
		$("#ww-pickup-table").data('playerid',$(this).data('pickup-id'));
		$("#ww-pickup-table").data('playername',$(this).data('pickup-name'));
		$(".pickup-text").text("Pickup: "+$(this).data("pickup-name"));
		//showConfirm();
		$("#drop-modal").modal('show');
	});

	// Pick up table TR click
	/*
	$("#ww-pickup-table").on("click","tr.pickup-player",function(){
		$(this).addClass("active");
		$("tr.pickup-player").not(this).removeClass("active")
		$("#to-pickup").text("Pickup: "+$(this).data("pickup-name"));
		//showConfirm();
		$("#drop-modal").modal('show');
	});
	*/

	// Drop  table button click (old)
	$("#ww-drop-table").on("click","button.drop-player",function(){

		$("#ww-drop-table").data('playerid',$(this).data('drop-id'));
		$("#ww-drop-table").data('playername',$(this).data('drop-name'));
		$(".drop-text").text("Drop: "+$(this).data("drop-name"));
		showConfirm();
	});

	// Drop  table TR click
	/*
	$("#ww-drop-table").on("click","tr.drop-player",function(){
		$(this).addClass("active");
		$("tr.drop-player").not(this).removeClass("active")
		$("#to-drop").text("Drop: "+$(this).data("drop-name"));
		showConfirm();
	});
	*/

	// Post the waivier wire transaction.
	$("#confirm-drop").click(function(){
		//drop_id = $("tr.drop-player.active").data("drop-id");
		drop_id = $("#ww-drop-table").data('playerid');
		//pickup_id = $("tr.pickup-player.active").data("pickup-id");
		pickup_id = $("#ww-pickup-table").data('playerid');
		drop_name = $("tr.drop-player.active").data("drop-name");
		drop_name = $("#ww-drop-table").data("playername");
		pickup_name = $("tr.pickup-player.active").data("pickup-name");
		pickup_name = $("#ww-pickup-table").data("playername");

		url = "<?=site_url('myteam/waiverwire/transaction/execute')?>";
		$.post(url,{'pickup_id' : pickup_id, 'drop_id' : drop_id},function(data)
		{
			var response = jQuery.parseJSON(data);
			//pickupSearch(1,getpos(),getsort(),getsearch());
			$("#confirm-modal").modal('hide');
			reloadPage();
			if (response.success == true)
			{
				if (pickup_name == undefined){pickup_name = "No one";}
				showMessage("Request processed succcessfuly.<br>Dropped: "+drop_name+"<br>Added: "+pickup_name,'alert-success');
			}
			else
			{
				showMessage(response.error,'alert-error');
			}
			$("#drop-modal").modal('hide');

		});

	});

	// Reset if model is hidden (user cancel
	$("#confirm-modal").on('hide.bs.modal', function(){
		$("#drop-modal").modal('hide');
        $("button.drop-player").removeClass('active');
		$("button.pickup-player").removeClass('active');
		reloadPage();
    });

   	// Reset if model is hidden (user cancel
	$("#drop-modal").on('hide.bs.modal', function(){
		$("button.drop-player").removeClass('active');
		$("button.pickup-player").removeClass('active');
		reloadPage();
    });

	function gettotal(){return parseInt($('#count-total').text());}
	function getpos(){return $("#search-pos").val();}
	function getsort(){return $("#search-sort").val();}
	function getsearch(){return $("#search-name").val();}
	function getpage(){return parseInt($('#next').val())-1;} // Which page are we on?

	function pickupSearch(page, pos, sort, search, url)
	{
		url = typeof url !== 'undefined' ? url : "<?=site_url('myteam/waiverwire/ajax_pickup_table')?>";
		if ($("#is-xs").css("display") == "block"){var per_page = 6;}
		else {var per_page = 12;}
		$.post(url,{'page':page-1, 'sel_pos':pos, 'sel_sort':sort, 'search' : search, 'per_page': per_page }, function(data){
			$("#ww-pickup-table").html(data);
		});
		$("#next").val(2);
		$("#prev").val(0);

	}

	function reloadPage()
	{
		url = "<?=site_url('myteam/waiverwire/ajax_drop_table')?>";
		$.post(url,{},function(data){ $("#ww-drop-table").html(data); });

		$("#to-drop").text("Drop: No One");
		$("#to-pickup").text("Pick Up: No One");
		$("select#search-pos>option:eq(0)").prop('selected',true);
		$("select#search-sort>option:eq(0)").prop('selected',true);
		$("tr.pickup-player").removeClass('active');
		$("tr.drop-player").removeClass('active');
		pickupSearch(1,0,'points','');
	}

	function showConfirm()
	{
		var url="<?=site_url('myteam/waiverwire/transaction')?>";
		//drop_id = $("tr.drop-player.active").data("drop-id");
		drop_id = $("#ww-drop-table").data('playerid');

		//pickup_id = $("tr.pickup-player.active").data("pickup-id");
		pickup_id = $("#ww-pickup-table").data('playerid');
		console.log(pickup_id);
		//drop_name = $("tr.drop-player.active").data("drop-name");
		//pickup_name = $("tr.pickup-player.active").data("pickup-name");
		$.post(url,{'pickup_id':pickup_id,'drop_id':drop_id},function(data){

			var d = jQuery.parseJSON(data);
			if (d.success == true)
			{$("#confirm-modal").modal('show');}
			else {
				$("#error-text").text("Error:"+d.error);
				$("#error-modal").modal('show');
			}

		});

		/*
		if ($("#footer-nav").is(":visible"))
		{
			if (($("button.pickup-player.active").length == 0) && ($("button.drop-player.active").length > 0))
			{
				showMessage('Select player on pick up tab.','alert-info');
			}
			if (($("button.pickup-player.active").length > 0) && ($("button.drop-player.active").length == 0))
			{
				showMessage('Select player on drop tab.','alert-info');
			}
		}
		*/
	}

});
</script>
