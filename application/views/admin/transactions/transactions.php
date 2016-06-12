<script src="<?=site_url('js/foundation-datepicker.min.js')?>"></script>
<link href="<?=site_url('/css/foundation-datepicker.min.css')?>" rel="stylesheet">
<div class="row">
	<div class="columns">
		<?php //print_r($settings); ?>
		<div class="row align-center">
			<div class="columns" style="max-width:800px;">
				<h5 class="text-left">Settings</h5>
				<table class="text-left">
					<thead>
					</thead>
					<tbody>
						<tr>
							<td>Waiver wire deadline</td>
							<td id="wwdeadline-field" class="short"><?=$settings->waiver_wire_deadline?></td>
							<td class="text-center">
								<a href="#" id="wwdeadline-control" class="change-control" data-type="text" data-url="<?=site_url('admin/transactions/ajax_save_setting')?>">Change</a>
								<a href="#" id="wwdeadline-cancel" class="cancel-control"></a>
							</td>
						</tr>
						<tr>
							<td>Waiver wire clear time (hours)</td>
							<td id="wwcleartime-field" class="short"><?=$settings->waiver_wire_clear_time/60/60?></td>
							<td class="text-center">
								<a href="#" id="wwcleartime-control" class="change-control" data-type="text" data-url="<?=site_url('admin/transactions/ajax_save_setting')?>">Change</a>
								<a href="#" id="wwcleartime-cancel" class="cancel-control"></a>
							</td>
						</tr>
						<tr>
							<td>Trade deadline</td>
							<td id="tdeadline-field" class="short"><?=$settings->trade_deadline?></td>
							<td class="text-center">
								<a href="#" id="tdeadline-control" class="change-control" data-type="text" data-url="<?=site_url('admin/transactions/ajax_save_setting')?>">Change</a>
								<a href="#" id="tdeadline-cancel" class="cancel-control"></a>
							</td>

						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>

	$('body').on('focus',"#wwdeadline-edit, #tdeadline-edit", function(){
		$(this).fdatepicker({
			format: 'yyyy-mm-dd hh:ii',
			pickTime: true
		});
	});

</script>
