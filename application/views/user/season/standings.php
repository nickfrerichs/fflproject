
<div class="section">
	<div class="container">
			<div class="subtitle">Standings</div>
			<div id="standings-table">
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
	year = <?=$selected_year?>;
	var url = "<?=site_url('season/standings/ajax_get_standings')?>";
	$.post(url,{'year' : year},function(data){
		$("#standings-table").html(data);
		if (year != 0){$("#year").text(year)};
	});
}

</script>
