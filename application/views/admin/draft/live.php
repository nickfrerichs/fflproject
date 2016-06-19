<script src="<?=site_url('js/foundation-datepicker.min.js')?>"></script>
<link href="<?=site_url('/css/foundation-datepicker.min.css')?>" rel="stylesheet">
<div class="row">
	<div class="columns">
		<h5><?=$year?> Live Draft options</h5>
	</div>
</div>
<div class="row">
	<div class="columns">
		<table>
			<tr>
				<td>Draft Date & Time</td>
				<td id="drafttime-field" class="short"><?=$settings->scheduled_draft_start_time?></td>
				<td class="text-center">
					<a href="#" id="drafttime-control" class="change-control" data-type="text" data-url="<?=site_url('admin/draft/save_draft_settings')?>">Change</a>
					<a href="#" id="drafttime-cancel" class="cancel-control"></a>
				</td>

			</tr>
			<tr>
				<td>Seconds per pick</td>
				<td id="picktime-field" class="short"><?=$settings->draft_time_limit?></td>
				<td class="text-center">
					<a href="#" id="picktime-control" class="change-control" data-type="text" data-url="<?=site_url('admin/draft/save_draft_settings')?>">Change</a>
					<a href="#" id="picktime-cancel" class="cancel-control"></a>
				</td>
			</tr>
			<tr>
				<td><span data-tooltip class="has-tip top" title="Draft beings automatically at the scheduled time.  When off, admin must click start.">Auto start</span></td>
				<td></td>
				<td class="text-center">
					<div class="switch tiny">
					<input  class="switch-input toggle-control" data-item="autostart" data-url="<?=site_url('admin/draft/ajax_toggle_auto_start')?>"
						id="autostart" type="checkbox" <?php if($settings->draft_start_time != 0){echo "checked";}?>
						<?php if($settings->scheduled_draft_start_time == 0){echo " disabled";}?>>
					<label class="switch-paddle" for="autostart">
					</label>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>

<div class="row">
	<div class="columns">
		<h5>Admin Live Options</h5>
	</div>
</div>



<script type="text/javascript">

$(document).ready(function(){

	$('body').on('focus',"#drafttime-edit", function(){
		$(this).fdatepicker({
			format: 'yyyy-mm-dd hh:ii',
			pickTime: true
		});
	})

	$('#save').on('click', function(){
		var date = $("#draft-date").val()
		console.log(date);

		var pick = $("#pick-time").val();
		url ="<?=site_url('admin/draft/save_draft_settings')?>";
		$.post(url, {'date':date,'pick':pick}, function(data){

			window.location.replace("<?=site_url('admin/draft/settings')?>");
		});
	});

});

</script>
