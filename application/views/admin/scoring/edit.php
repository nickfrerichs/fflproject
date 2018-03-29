<div class="section">

	<div class="is-size-5"><?=$selected_year?> Season</div>


	<!-- <?=form_open()?> -->
	<a href="<?=site_url('admin/scoring')?>">Cancel</a>
	<table class="table is-striped is-narrow is-fullwidth fflp-table-fixed">
		<th>Category</th><th>Points</th><th>Per</th><th>Round Dec.</th><th>Range Start</th><th>Range End</th>
		<?php foreach($values as $v): ?>
		<tr>
			<td><?=$v->long_text?></td>
			<td><input class="input scoring-points" data-id="<?=$v->id?>" type="text" value="<?=$v->points?>"></td>
			<td>
				<?php if(!$v->is_range): ?>
					<input class="input scoring-per" data-id="<?=$v->id?>" text="text" value="<?=$v->per?>"></td>
				<?php endif;?>
			</td>
			<td>
				<div class="select">
					<select class="scoring-round" data-id="<?=$v->id?>">
						<option value="1" <?php if($v->round){echo "selected";}?>>Up</option>
						<option value="0" <?php if(!$v->round){echo "selected";}?>>Down</option>
					</select>
				</div>
				<!-- <?=form_dropdown('round_'.$v->id,array(1 => 'up', 0 => 'down'),$v->round)?> -->
			</td>
			<td>
				<?php if($v->is_range): ?>
					<input class="input scoring-start" data-id="<?=$v->id?>" type="text" value="<?=$v->range_start?>">
				<?php endif;?>
			</td>
			<td>
				<?php if($v->is_range): ?>
					<input class="input scoring-end" data-id="<?=$v->id?>" type="text" value="<?=$v->range_end?>">
				<?php endif;?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<button id="save-scoring-defs" class="button is-small is-link">Save</button>

</div>

<script>
$('#save-scoring-defs').on('click',function(){
	var url = "<?=site_url('admin/scoring/ajax_save_scoring_defs')?>";
	var values = {};
	var year = "<?=$selected_year?>";
	$('.scoring-points').each(function(){
		values[$(this).data('id')] = {};
		values[$(this).data('id')]['points'] = $(this).val();
	});
	$('.scoring-per').each(function(){values[$(this).data('id')]['per'] = $(this).val();});
	$('.scoring-round').each(function(){values[$(this).data('id')]['round'] = $(this).val();});
	$('.scoring-start').each(function(){values[$(this).data('id')]['start'] = $(this).val();});
	$('.scoring-end').each(function(){values[$(this).data('id')]['end'] = $(this).val();});
	console.log(values);
	$.post(url,{'values':values},function(data){
		console.log(data);
		console.log('here');
		if (data.success)
		{
			location.reload();
		}
	},'json');

	//console.log(values);

});
</script>