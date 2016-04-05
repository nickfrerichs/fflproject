<?php //print_r($years); ?>

<div class="container">
	<div class="row">
		<h3>Draft</h3>
		<a href="<?=site_url('season/draft/live')?>"><?=$this->session->userdata('current_year')?> Live Draft</a>
	</div>
	<div class="row">
		<form class="form-inline text-center">
			<div class="form-group">
				<select id="year-select" class="form-control">
					<?php foreach($years as $y): ?>
						<option value="<?=$y->year?>"><?=$y->year?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</form>


		<table class="table table-condensed table-striped">
			<thead>
				<th>Pick</th>
				<th>Player</th>
				<th>Pos</th>
				<th>NFL Team</th>
				<th>Owner</th>
			</thead>
			<tbody id="draft-results-table">
			</tbody>
		</table>
	</div>
</div>

<script>
$( document ).ready(function() {
	load_draft_results();
});

$("#year-select").on("change",function(){
	load_draft_results();
});

function load_draft_results()
{
	var year = $("#year-select").val();
	var url = "<?=site_url('season/draft/ajax_get_draft_results')?>";
	$.post(url,{'year' : year},function(data){
		$("#draft-results-table").html(data);
		if (year != 0){$("#year").text(year)};
	});
}

</script>
