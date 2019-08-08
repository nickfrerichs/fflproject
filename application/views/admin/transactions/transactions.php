<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<?php if(count($approvals) > 0): ?>
<div class="section">
	<div class="columns box">
		<div class="column">
			<div class="is-size-5">Waiver wire approvals</div>
			<table class="table is-fullwidth is-narrow fflp-table-fixed is-striped">
				<thead>
				</thead>
				<tbody>
					<?php foreach($approvals as $a): ?>
						<tr>
							<td><?=date("n/j/y g:i:s a",$a->request_date)?></td>
							<td>
								<div><b>Pick up:</b> <?=$a->p_first.' ',$a->p_last?> (<?=$a->p_pos.' - '.$a->p_club_id?>)</div>
								<div style="color:#999"><b>Drop:</b> <?=$a->d_first.' ',$a->d_last?> (<?=$a->d_pos.' - '.$a->d_club_id?>)</div>

							</td>
							<td>
								<div><b>Team:</b> <?=$a->team_name?></div>
								<div><b>Owner:</b> <?=$a->o_first.' '.$a->o_last?></div>
							</td>
							<td>
								<button class="button is-small is-link ww-approve" data-id="<?=$a->ww_id?>">Approve</button>
								<button class="button is-small is-link ww-reject" data-id="<?=$a->ww_id?>">Reject</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php endif;?>

<div class="section">
	<div class="columns box">
		<div class="column">
			<h5 class="text-left">Settings</h5>
			<table class="table is-fullwidth is-narrow fflp-table-fixed is-striped">
				<thead>
				</thead>
				<tbody>
					<tr>
						<td>Waiver wire deadline</td>
						<td>
						<?php $this->load->view('components/editable_text',array('id' => 'wwdeadline', 
																						'value' => $settings->waiver_wire_deadline,
																						'url' => site_url('admin/transactions/ajax_save_setting')));?>

						</td>
					</tr>
					<tr>
						<td>Waiver wire clear time (hours)</td>
						<td>
						<?php $this->load->view('components/editable_text',array('id' => 'wwcleartime', 
																						'value' => $settings->waiver_wire_clear_time/60/60,
																						'url' => site_url('admin/transactions/ajax_save_setting')));?>
						</td>
					</tr>
					<tr>
						<?php $t = $settings->waiver_wire_approval_type;?>
						<td>Waiverwire approvals</td>
						<td>
							<?php 
									// Inputs: $id, $value, $blank_value, $url, $options, $selected_val
									$options = array('Auto' => 'auto', 'Semiauto' => 'semiauto', 'Manual' => 'manual');
									$this->load->view('components/editable_select',
												array('id' => 'ww-approvals',
													  'options' => $options,
													  'url' => site_url('admin/transactions/set_ww_approval_setting'),
													  'selected_val' => $settings->waiver_wire_approval_type));
								?>
						</td>
					</tr>
					<tr>
						<td>Trade deadline</td>
						<td>
						<?php $this->load->view('components/editable_text',array('id' => 'tdeadline', 
																						'value' => $settings->trade_deadline,
																						'url' => site_url('admin/transactions/ajax_save_setting')));?>

						</td>
					</tr>
					<tr>
						<td><span data-tooltip class="has-tip top" title="Players cannot be picked up/dropped for the current week after their game has started.">Lock players after game start</span></td>
						<td>	
							<div>Lock Players</div>
								<?php $this->load->view('components/toggle_switch',
                                                array('id' => 'disablegt',
													  'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
													  'var1' => 'disablegt',
                                                      'is_checked' => $settings->waiver_wire_disable_gt));
                        		?>
						</td>
					</tr>
					<tr>
						<td><span data-tooltip class="has-tip top" title="Onwner requests will be queued on these days and be subject to the waiver wire priority/approval.">Disable waiver wire on these days</span></td>
						<td>

							<?php
								$wwdays = str_split($settings->waiver_wire_disable_days);
								$dow = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
							?>
							<?php foreach(array("0","1","2","3","4","5","6") as $i): ?>
								<div style="display:inline-block">
									<div style="display:inline-block"><?=$dow[$i]?></div>

									<?php
									$this->load->view('components/toggle_switch',
												array('id' => 'wwday_'.$i,
													'url' => site_url('admin/transactions/ajax_toggle_wwday'),
													'is_checked' => in_array($i,$wwdays),
													'color' => 'is-danger',
													'var1' => $i));
									?>
								</div>

							<?php endforeach;?>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
	</div>
</div>

<script>
	// var picker_options = {
	// 						enableTime: true,
	// 						altInput: true,
	// 						dateFormat: "Y-m-d H:i",
	// 						altFormat: "F j, Y h:i p"		
	// 					};
		
	var picker_options = {
							enableTime: true,
							// altInput: true,
							//altFormat: "F j, Y H:i",
							//altInputClass:"",
							minDate: "2017",
							dateFormat: "Y-m-d H:i",
						};
	$('#wwdeadline-input').flatpickr(picker_options);
	$('#tdeadline-input').flatpickr(picker_options);
	// $('body').on('focus',"#wwdeadline-input, #tdeadline-input", function(){
	// 	$(this).flatpickr();
	// });

	$('.ww-approve').on('click',function(){
		var url = "<?=site_url('admin/transactions/ww_approve')?>";
		var ww_id = $(this).data('id');
		$.post(url, {'id':ww_id},function(data){
			if (data.success == true)
			{location.reload();}
			else
			{
				notice(data.msg,'error');
			}
		},'json');
	});

	$('.ww-reject').on('click',function(){
		var url = "<?=site_url('admin/transactions/ww_reject')?>";
		var ww_id = $(this).data('id');
		$.post(url, {'id':ww_id},function(data){
			location.reload();
		},'json');
	});

	// $(".ww-approvals").on('click', function(){
	// 	var url="<?=site_url('admin/transactions/set_ww_approval_setting')?>";
	// 	$.post(url,{'value':$(this).val()},function(data){
	// 		if (data.success)
	// 		{notice('Saved.','success');}
	// 		else {notice('An error ocurred while saving.','Error');}
	// 	},'json');
	// });

	$('.wwday').on('click',function(){
		$("#wwdaysave").removeClass('hide');
	});

	// $('#wwdaysave').on('click',function(){
	// 	var url = "<?=site_url('admin/transactions/ajax_save_setting')?>";
	// 	var wwdays = "";

	// 	$('.wwday:checked').each(function(){
	// 		wwdays += $(this).data('day');
	// 	});
	// 	console.log(wwdays);
	// 	$.post(url,{"value" : wwdays, "type":"wwdays"}, function(data){
	// 		if (data.success)
	// 		{
	// 			notice('Saved.','success');
	// 			$("#wwdaysave").addClass('hide');
	// 		}
	// 		else {notice('An error ocurred while saving.','Error');}
	// 	},'json');
	// });

</script>
