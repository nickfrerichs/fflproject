<div class="row">
	<div class="columns">
		<h5><?=$this->session->userdata('current_year')?> Season</h5>
	</div>
</div>
<div class="row">
	<div class="columns">
		<?=form_open()?>
		<a href="<?=site_url('admin/scoring')?>">Cancel</a>
		<table class="table table-condensed table-striped">
		    <th>Category</th><th>Points</th><th>Per</th><th>Round Dec.</th><th>Range Start</th><th>Range End</th>
		    <?php foreach($values as $v): ?>
		    <tr>
		        <td><?=form_label($v->long_text)?></td>
		        <td><?=form_input('points_'.$v->id, $v->points)?></td>
		        <td><?php if(!$v->is_range){echo form_input('per_'.$v->id, $v->per);}?></td>
		        <td><?=form_dropdown('round_'.$v->id,array(1 => 'up', 0 => 'down'),$v->round)?></td>
				<td><?php if($v->is_range){echo form_input('start_'.$v->id, $v->range_start);}?></td>
				<td><?php if($v->is_range){echo form_input('end_'.$v->id, $v->range_end);}?></td>
		    </tr>
		    <?php endforeach; ?>
		</table>
		<input class="button small" type="submit" name="save" value="Save"  />
	</div>
</div>
