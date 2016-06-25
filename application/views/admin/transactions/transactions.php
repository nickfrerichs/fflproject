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

	$('.ww-approve').on('click',function(){
		var url = "<?=site_url('admin/transactions/ww_approve')?>";
		var ww_id = $(this).data('id');
		$.post(url, {'id':ww_id},function(data){
			console.log(data);

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
			console.log('here');
			location.reload();
		},'json');
	});

</script>
