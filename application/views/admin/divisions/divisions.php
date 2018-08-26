<?php //print_r($division_array); ?>

<div class="section">
	<div><a href="<?=site_url('admin/divisions/manage')?>">Manage divisions</a></div>

	<?php $options[0] = 'none'; foreach($divisions as $d){$options[$d->id] = $d->name;} ?>
	<?php foreach($division_array as $div_id => $div): ?>
	<p></p>
	<table class="table is-fullwidth is-narrow fflp-is-fixed">
		<tr>
			<td style="width: 75%"><strong>Division: <?=$div['name']?></strong></td><td></td>
		</tr>
		<?php foreach($div['teams'] as $team): ?>
		<tr>
			<td><?=$team['name']?></td>
			<td>
				<div class="select">
					<select class="division-team-select" data-id="<?=$team['id']?>">
					<?php foreach($divisions as $div): ?>
						<option value="<?=$div->id?>" <?php if($div_id == $div->id){echo "selected";}?>><?=$div->name?></option>
					<?php endforeach;?>
					</select>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endforeach; ?>

	<button id="save-info-button" class="button is-small is-link">Save</button>
</div>

<script>
$('#save-info-button').on('click',function(){
	var url="<?=site_url('admin/divisions/ajax_save_divisions')?>";
	var teams = {};
	$('.division-team-select').each(function(){
		teams[$(this).data('id')] = $(this).val();
	});
	$.post(url,{'teams': teams},function(data){
		if (data.success)
		{
			location.reload();
		}
	},'json');
});
</script>
