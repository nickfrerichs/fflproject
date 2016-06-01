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
		    <th>Category</th><th>Points</th><th>Per</th><th>Round Dec.</th>
		    <?php foreach($values as $v): ?>
		    <tr>
		        <td><?=form_label($v->long_text)?></td>
		        <td><?=form_input('points_'.$v->id, $v->points)?></td>
		        <td><?=form_input('per_'.$v->id, $v->per)?></td>
		        <td><?=form_dropdown('round_'.$v->id,array(1 => 'up', 0 => 'down'),$v->round)?></td>
		    </tr>
		    <?php endforeach; ?>
		</table>
		<?=form_submit('save','Save')?>
	</div>
</div>
