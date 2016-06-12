
<div class="row">
	<div class="columns">

		<div><a href="<?=site_url('admin/draft/create')?>">Create New Draft Order</a></div>
		<div><a href="<?=site_url('admin/draft/settings')?>">Draft Settings</a></div>

	</div>
</div>

<?php if($num_rounds > 0): ?>
<div class="row">
	<div class="columns">
		<h5 class="text-center">Round <span id="round-num">1</span></h5>
		<table>
			<thead>
			</thead>
			<tbody id="draft-table">
			</tbody>
		</table>
	</div>
</div>


<div class="row">
	<!-- Prev/Next buttons -->
	<div class="columns small-2"></div>
	<div class="columns small-8">

				<button id="prev" class="button small page-btn" type="button" value="0">
				Previous
				</button>

				<button id="next" class="button small page-btn" type="button" value="2">
				Next
				</button>

	</div>
	<div class="columns small-2"></div>
<?php endif;?>

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
			$("#round-num").text(round);
			$("#draft-table").html(data);
		})
	}

	function getround(){return parseInt($('#next').val())-1;} // Which round are we on?

});
</script>
