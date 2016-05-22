<?php //print_r($years); ?>

<div class="row">
	<div class="columns">
		<h5>Draft</h5>
	</div>
</div>
<div class="row">
	<div class="columns">
		<a href="<?=site_url('season/draft/live')?>"><?=$this->session->userdata('current_year')?> Live Draft</a>
	</div>
</div>
<div class="row align-center">
	<div class="columns small-5 medium-2">
		<select id="year-select" class="form-control">
			<?php foreach($years as $y): ?>
				<option value="<?=$y->year?>"><?=$y->year?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<div class="row">
	<div class="columns">
		<table>
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
