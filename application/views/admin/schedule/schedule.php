<?php //print_r($schedule); ?>
	
<div class="section">
	<div class="columns">
		<div class="column">
		<?php $this->load->view('admin/schedule/year_bar'); ?>
		<br><br>
				<a href="<?=site_url('admin/schedule/edit/'.$selected_year)?>">Manage schedule</a> <br>
				<?php if($selected_year == $this->session->userdata('current_year')): ?>
					<a href="<?=site_url('admin/schedule/create')?>">Create schedule from template</a> <br>
				<?php endif; ?>
				<a href="<?=site_url('admin/schedule/titles/'.$selected_year)?>">Assign titles</a> <br>
		</div>
	</div>
	<br>
	<div class="columns is-multiline">
		<?php foreach ($schedule as $week_num => $week): ?>
		<div class="column is-6">

			<div class="has-text-centered is-size-5">Week <?=$week_num?></div>
			<table class="table is-fullwidth is-narrow fflp-table-fixed is-striped is-bordered">
			    <th width=34%>Home</th><th width=10%></th><th width=34%>Away</th><th width=20%>Game Type</th>
			    <?php foreach ($week as $game):?>

			    <tr>
			        <td><?=$game['home']?></td>
			        <td class="has-text-centered">at</td>
			        <td><?=$game['away']?></td>
			        <td><?=$game['type']?></td>
			    </tr>
			    <?php endforeach; ?>
			</table>
		</div>
		<?php endforeach; ?>
	</div>
</div>
