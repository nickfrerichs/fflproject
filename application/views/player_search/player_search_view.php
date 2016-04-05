<script>
$(document).ready(function() {
	playerSearchPost();

	// Name search event
	var timer;
	$(".search-group").on("input","#search-name", function(event){
		clearTimeout(timer);
		var delay = 300;
		var page = parseInt($('#prev').val())+parseInt($('#next').val()); // Which page are we on?
		timer = setTimeout(function(){
			playerSearchPost(page,$('#select-pos').val(),$("#select-sort").val(),$("#search-name").val());
		},delay);
	});

	// Position & sort event
	$('.sort-group').on('change','.search-form', function(){
		//doPost('0',$(this).val());
		playerSearchPost('0',$("#select-pos").val(),$("#select-sort").val(),$("#search-name").val());
	});

	// Prev/Next button event
	$('#roster-table').on('click','.page-btn', function(){
		playerSearchPost($(this).val(),$('#select-pos').val(),$("#select-sort").val(),$("#search-name").val());
	});

	function page()
	{
		return parseInt($('#prev').val())+parseInt($('#next').val()); // Which page are we on?
	}

	function playerSearchPost(page, pos, sort, search, url)
	{
		url = typeof url !== 'undefined' ? url : "<?=site_url('player_search/get_player_table')?>";
		$.post(url,{'page':page, 'sel_pos':pos, 'sel_sort':sort, 'search' : search }, function(data){
			$("div#roster-table").html(data);
		});
	}
});
</script>	


<div class="table-heading text-center"> Pick up Player</div>
<div class="row">
	<div class="search-group col-xs-4">
			<input id="search-name" type="text" class="form-control" placeholder="Search" autofocus>
	</div>
	<div class='col-xs-4 sort-group'>
		<select id="select-pos" class="form-control search-form">
				<option value="0">All</option>
			<?php foreach ($pos as $p): ?>

				<option value="<?=$p->id?>"><?=$p->text_id?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class='col-xs-4 sort-group'>
		<select id="select-sort" class="form-control search-form">
			<?php foreach ($sort as $id=>$name): ?>
				<option value="<?=$id?>"><?=$name?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<div  id="roster-table">
</div>
