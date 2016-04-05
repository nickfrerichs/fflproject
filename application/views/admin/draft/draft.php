<div class="container">

	<div class="page-heading">
	Admin draft
	</div>
	<div><a href="<?=site_url('admin/draft/create')?>">Create New Draft Order</a></div>
	<div><a href="<?=site_url('admin/draft/settings')?>">Draft Settings</a></div>

	<div class="col-xs-12">
		<div id="draft-round" class="table-heading text-center">Round 1</div>
		<table class="table table-striped">
			<thead>
			</thead>
			<tbody id="draft-table">
			</tbody>
		</table>
	<div>

	<!-- Prev/Next buttons -->
	<div class="col-xs-2"></div>
	<div class="col-xs-8">
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
	</div>
	<div class="col-xs-2"></div>
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


	function load_draft_table(round)
	{
		url = "<?=site_url('admin/draft/ajax_draft_table')?>";

		$.post(url,{'round' : round},function(data){
			$("#draft-table").html(data);
		})
	}

	function getround(){return parseInt($('#next').val())-1;} // Which round are we on?

});
</script>