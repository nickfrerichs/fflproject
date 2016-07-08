
<div class="row">
	<div class="columns callout">
		<div class="row">
			<div class="columns">
				<h5><span id="year"><?=$year?></span> Standings</h5>
			</div>
		</div>
		<div class="row">
			<div id="standings-table" class="columns">
			</div>
		</div>
		<div class="row align-center">
			<div class="columns medium-2 small-8">
				<select id="year-select">
					<?php foreach($years as $y): ?>
						<option value="<?=$y->year?>"><?=$y->year?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
</div>


<script>
$( document ).ready(function() {
	load_standings();
});

$("#year-select").on("change",function(){
	load_standings();
});

function load_standings()
{
	var year = $("#year-select").val();
	var url = "<?=site_url('season/standings/ajax_get_standings')?>";
	$.post(url,{'year' : year},function(data){
		$("#standings-table").html(data);
		if (year != 0){$("#year").text(year)};
	});
}

</script>
