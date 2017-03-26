<?php //print_r($schedule); ?>
	

	<div class="row">
		<div class="columns small-12">
		<?php $this->load->view('admin/schedule/year_bar'); ?>
		<br>
				<a href="<?=site_url('admin/schedule/edit/'.$selected_year)?>">Manage schedule</a> <br>
				<?php if($selected_year == $this->session->userdata('current_year')): ?>
					<a href="<?=site_url('admin/schedule/create')?>">Create schedule from template</a> <br>
				<?php endif; ?>
		</div>
	</div>
	<br>
	<div class="row">
		<?php foreach ($schedule as $week_num => $week): ?>
		<div class="columns large-6 medium-12 small-12">
			<div class="callout">
			<h5 class="text-center">Week <?=$week_num?></h5>
			<table class="">
			    <th width=34%>Home</th><th width=10%></th><th width=34%>Away</th><th width=20%>Game Type</th>
			    <?php foreach ($week as $game):?>

			    <tr>
			        <td><?=$game['home']?></td>
			        <td>at</td>
			        <td><?=$game['away']?></td>
			        <td><?=$game['type']?></td>
			    </tr>
			    <?php endforeach; ?>
			</table>
		</div>
		</div>
		<?php endforeach; ?>
	</div>
