<script src="<?=site_url('js/foundation-datepicker.min.js')?>"></script>
<link href="<?=site_url('/css/foundation-datepicker.min.css')?>" rel="stylesheet">
<div class="row">
	<div class="columns">
		<?php //print_r($approvals); ?>
		<div class="row align-center">
			<div class="columns small-12">
				<h5>Waiver wire approvals</h5>
				<table>
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
									<button class="button small ww-approve" data-id="<?=$a->ww_id?>">Approve</button>
									<button class="button small ww-reject" data-id="<?=$a->ww_id?>">Reject</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<div class="columns small-12" style="max-width:800px;">
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
							<?php $t = $settings->waiver_wire_approval_type;?>
							<td>Waiverwire approvals</td>
							<td>
								<fieldset>
									<div class="row">
										<div class="columns small-12">
											<input type="radio" class="ww-approvals" name="ww-approvals" value="auto" id="fullauto" required <?php if($t=="auto"){echo "checked";}?>>
											<label for="fullauto">
												<span data-tooltip class="has-tip top" title="Auto approve all waiver requests.  When there is contention for a player, team with a worse record wins. (remember to set up a scheduled task)">Fully Automatic</span>
												</label>
										</div>
										<div class="columns small-12">
											<input type="radio" class="ww-approvals" name="ww-approvals" value="semiauto" id="partauto" required <?php if($t=="semiauto"){echo "checked";}?>>
											<label for="partauto">
												<span data-tooltip class="has-tip top" title="Auto approve except when contention for a player, league admin decides priority and manually approves those. (remember to set up schedule task)">Semi Automatic</span>
												</label>
										</div>
										<div class="columns small-12">
											<input type="radio" class="ww-approvals" name="ww-approvals" value="manual" id="manual" required <?php if($t=="manual"){echo "checked";}?>>
											<label for="manual">
												<span data-tooltip class="has-tip top" title="League admin must approve every waiver wire request. (no schedule task needed)">Manual</span>
												</label>
										</div>
									</div>
								</fieldset>
							</td>
							<td></td>
						</tr>
						<tr>
							<td>Trade deadline</td>
							<td id="tdeadline-field" class="short"><?=$settings->trade_deadline?></td>
							<td class="text-center">
								<a href="#" id="tdeadline-control" class="change-control" data-type="text" data-url="<?=site_url('admin/transactions/ajax_save_setting')?>">Change</a>
								<a href="#" id="tdeadline-cancel" class="cancel-control"></a>
							</td>

						</tr>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Players cannot be picked up/dropped for the current week after their game has started.">Lock players after game start</span></td>
							<td></td>
							<td class="text-center">
								<div class="switch tiny">
								<input  class="switch-input toggle-control" data-item="disablegt" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
									id="disablegt" type="checkbox" <?php if($settings->waiver_wire_disable_gt == "1"){echo "checked";}?>>
								<label class="switch-paddle" for="disablegt">
								</label>
								</div>
							</td>
						</tr>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Onwner requests will be queued on these days and be subject to the waiver wire priority/approval.">Disable waiver wire on these days</span></td>
							<td>
								<fieldset>
									<legend>Days of the week</legend>
									<?php $wwdays = str_split($settings->waiver_wire_disable_days); ?>
									<input id="ww0" class="wwday" data-day="0" type="checkbox" <?php if(in_array("0",$wwdays)){echo 'checked="checked"';}?>><label for="ww0">Sun</label>
									<input id="ww1" class="wwday" data-day="1" type="checkbox" <?php if(in_array("1",$wwdays)){echo 'checked="checked"';}?>><label for="ww1">Mon</label>
									<input id="ww2" class="wwday" data-day="2" type="checkbox" <?php if(in_array("2",$wwdays)){echo 'checked="checked"';}?>><label for="ww2">Tue</label>
									<input id="ww3" class="wwday" data-day="3" type="checkbox" <?php if(in_array("3",$wwdays)){echo 'checked="checked"';}?>><label for="ww3">Wed</label>
									<input id="ww4" class="wwday" data-day="4" type="checkbox" <?php if(in_array("4",$wwdays)){echo 'checked="checked"';}?>><label for="ww4">Thu</label>
									<input id="ww5" class="wwday" data-day="5" type="checkbox" <?php if(in_array("5",$wwdays)){echo 'checked="checked"';}?>><label for="ww5">Fri</label>
									<input id="ww6" class="wwday" data-day="6" type="checkbox" <?php if(in_array("6",$wwdays)){echo 'checked="checked"';}?>><label for="ww6">Sat</label>
								</fieldset>
							</td>
							<td><button id="wwdaysave" type="button" class="alert button hide">Save</button></td>

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

	$(".ww-approvals").on('click', function(){
		var url="<?=site_url('admin/transactions/set_ww_approval_setting')?>";
		$.post(url,{'value':$(this).val()},function(data){
			if (data.success)
			{notice('Saved.','success');}
			else {notice('An error ocurred while saving.','Error');}
		},'json');
	});

	$('.wwday').on('click',function(){
		$("#wwdaysave").removeClass('hide');
	});

	$('#wwdaysave').on('click',function(){
		var url = "<?=site_url('admin/transactions/ajax_save_setting')?>";
		var wwdays = "";

		$('.wwday:checked').each(function(){
			wwdays += $(this).data('day');
		});
		console.log(wwdays);
		$.post(url,{"value" : wwdays, "type":"wwdays"}, function(data){
			if (data.success)
			{
				notice('Saved.','success');
				$("#wwdaysave").addClass('hide');
			}
			else {notice('An error ocurred while saving.','Error');}
		},'json');
	});

</script>
