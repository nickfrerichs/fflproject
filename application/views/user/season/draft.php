<?php //print_r($years); ?>
<div class="row">
	<div class="columns callout">
		<div class="row">
			<h4 class="columns">
				<a href="<?=site_url('season/draft/live')?>" class="button"><?=$this->session->userdata('current_year')?> Live Draft</a>
			</h4>
		</div>
		<div class="row align-center">
			<div class="columns small-5 medium-2">
				<select id="year-select" class="form-control">
					<?php if(!in_array($this->session->userdata('current_year'),$years)):?>
					<option value="<?=$this->session->userdata('current_year')?>"><?=$this->session->userdata('current_year')?></option>
					<?php endif;?>
					<?php foreach($years as $y): ?>
						<option value="<?=$y?>"><?=$y?></option>
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
