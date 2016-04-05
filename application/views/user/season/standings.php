<div class='container'>
	<?php //print_r($teams);?>
	<h3><span id="year"><?=$year?></span> Standings</h3>
	<br>
	<div id="standings-table">
	</div>
	<form class="form-inline text-center" style="padding-top: 10px;">
		<div class="form-group">
			<select id="year-select" class="form-control">
				<?php foreach($years as $y): ?>
					<option value="<?=$y->year?>"><?=$y->year?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</form>
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
