<?php //print_r($years); ?>
<div class="section">
	<div class="container">
		<div class="title"> Draft Results</div>
		<?php if ($draft_end != $this->session->userdata('current_year')): ?>
		<div class="is-size-5">
			<a href="<?=site_url('season/draft/live')?>"><?=$this->session->userdata('current_year')?> Live Draft</a>
		</div>
		<br>
		<?php endif;?>
		<div class="select">
			<select id="year-select" class="form-control">
				<?php if(!in_array($this->session->userdata('current_year'),$years)):?>
				<option value="<?=$this->session->userdata('current_year')?>"><?=$this->session->userdata('current_year')?></option>
				<?php endif;?>
				<?php foreach($years as $y): ?>
					<option value="<?=$y?>"><?=$y?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<br><br>
		<div class="f-scrollbar">
			<table class="table is-fullwidth is-narrow is-striped f-table-fixed is-size-7-mobile">
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
