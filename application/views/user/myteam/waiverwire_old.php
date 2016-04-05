<script>
$(document).ready(function(){
	reloadPage();
	
	function reloadPage()
	{
		url = "<?=site_url('myteam/waiverwire/ajax_roster_table')?>";
		$.post(url,{},function(data){
			$("#ww-roster").html(data);
			$("#to-drop").text("Drop: No One");
			$("#to-pickup").text("Pick Up: No One");
		});
	}
	function playerSearchPost(page, pos, sort, search, url)
	{
		url = typeof url !== 'undefined' ? url : "<?=site_url('player_search/get_player_table')?>";
		$.post(url,{'page':page, 'sel_pos':pos, 'sel_sort':sort, 'search' : search }, function(data){
			$("div#roster-table").html(data);
		});
	}

	$("#confirm-drop").click(function(){
		drop_id = $("input:radio[name=drop-player]:checked").val();
		pickup_id = $("input:radio[name=selected-player]:checked").val();
		url = "<?=site_url('myteam/waiverwire/process_transaction')?>";
		$.post(url,{'pickup_id' : pickup_id, 'drop_id' : drop_id},function(data)
		{
			reloadPage();
			playerSearchPost(0,'0','points','');
		});

	});

	$("#ww-roster").on('change',"input:radio[name=drop-player]",function(){
		//console.log($(this+':checked').val().find(".drop-player-name").text());
		$("#to-drop").text("Drop: "+$("input:radio[name=drop-player]:checked").next().find(".drop-player-name").text());
		checkOK();
	});
	$("#ww-search").on("change","input:radio[name=selected-player]",function(){
		$("#to-pickup").text("Pick Up: "+$("input:radio[name=selected-player]:checked").next().find(".selected-player-name").text());
		checkOK();
	});

	function checkOK()
	{
		if (($("#to-drop").text() != "Drop: No One")&& ($("#to-pickup").text() != "Pick Up: No One"))
			{$("#confirm-drop").addClass("ww-confirm");}
		else
			{$("#confirm-drop").removeClass("ww-confirm");}
	}


});
</script>

<div class="container">
	<div class="page-heading text-center pad-tb-1">Waiver wire</div>
	<div class="row">
		<div id="ww-status"></div>
		<div id="ww-search" class="col-sm-8 text-center">
		<?php $this->load->view('player_search/player_search_view');?>
			<div class="btn-group btn-group-justified">
				<div class="btn-group">
				<button class="btn btn-default" type="button" id="confirm-drop">
					<div> OK </div>
					<div id="to-drop">Drop: No One</div>
					<div id="to-pickup">Pick up: No One</div>
				</button>
				</div>
			</div>
		</div>

		<div class="col-sm-4">
			<div class="table-heading text-center"> Drop Player</div>
			<div id="ww-roster" data-toggle="buttons" class="btn-group">

			</div>

		</div>
	</div>
</div>