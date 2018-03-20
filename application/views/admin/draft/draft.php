
<div class="section">

	<div><a href="<?=site_url('admin/draft/create')?>">Create New <?=$this->session->userdata('current_year')?> Draft Order</a></div>
	<div><a href="<?=site_url('admin/draft/settings')?>">Draft Settings</a></div>
	<?php if($settings->trade_draft_picks): ?>
		<div><a href="<?=site_url('admin/draft/future')?>">Draft Future</a></div>
	<?php endif; ?>



	<?php if($num_rounds > 0): ?>

	<h5 class="text-center">Round <span id="round-num">1</span></h5>
	<table class="table is-narrow is-bordered is-fullwidth fflp-table-fixed">
		<thead>
		<th></th><th>Pick</th><th>Overall</th><th>Team</th><th>Player</th>
		</thead>
		<tbody id="draft-table">
		</tbody>
	</table>



	<div class="columns">
		<!-- Prev/Next buttons -->
		<div class="column is-2"></div>
		<div class="column is-8">

					<button id="prev" class="button is-small is-link page-btn" type="button" value="0">
					Previous
					</button>

					<button id="next" class="button is-small is-link page-btn" type="button" value="2">
					Next
					</button>

		</div>
		<div class="column is-2"></div>
	</div>
	<?php endif;?>
</div>
<script>
$(document).ready(function(){

	load_draft_table(1);

	// Prev/Next button events
	$("#next").click(function(){
		var round = getround();
		var next = round+2;
		var prev = round;
		if (round < <?=$num_rounds?>)
		{
			load_draft_table(round+1);
			//pickupSearch(page+1,getpos(),getsort(),getsearch());
			$("#next").val(next);
			$("#prev").val(prev);
		}
	});
	$("#prev").click(function(){
		var round = getround();
		var next = round;
		var prev = round-1;
		if (round > 1)
		{
			load_draft_table(round-1);
			//pickupSearch(page-1,getpos(),getsort(),getsearch());
			$("#next").val(next);
			$("#prev").val(prev);
		}
	});

	$("#draft-table").on('click','.delete-pick',function(){
		var url = "<?=site_url('admin/draft/ajax_delete_pick')?>";
		console.log(url);
		var pick_id = $(this).data('id');
		$.post(url,{'pick_id':pick_id},function(data){
			if (data.success)
			{
				load_draft_table(getround());
				 {notice('Pick deleted.','success');}
			}
		},'json');
	});


	function load_draft_table(round)
	{
		url = "<?=site_url('admin/draft/ajax_draft_table')?>";

		$.post(url,{'round' : round},function(data){
			$("#round-num").text(round);
			$("#draft-table").html(data);
		})
	}

	function getround(){return parseInt($('#next').val())-1;} // Which round are we on?

});
</script>
