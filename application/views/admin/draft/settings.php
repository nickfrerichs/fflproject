<!-- <script src="<?=site_url('js/foundation-datepicker.min.js')?>"></script>
<link href="<?=site_url('/css/foundation-datepicker.min.css')?>" rel="stylesheet"> -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="section">
	<div class="is-size-5"><?=$year?> Live Draft options</div>
</div>

<div class="section">
	<table class="table is-narrow is-fullwidth is-borderd fflp-table-fixed">
		<tr>
			<td>Draft Date & Time</td>
			<td>
				<?php $this->load->view('components/editable_text',array('id' => 'drafttime', 
																		'value' => $settings->scheduled_draft_start_time,
																		'url' => site_url('admin/draft/save_draft_settings')));?>
			</td>
		</tr>
		<tr>
			<td>Seconds per pick</td>
			<td>
				<?php $this->load->view('components/editable_text',array('id' => 'picktime', 
																		'value' => $settings->draft_time_limit,
																		'url' => site_url('admin/draft/save_draft_settings')));?>
			</td>
		</tr>
		<tr>
			<td><span data-tooltip class="has-tip top" title="Draft beings automatically at the scheduled time.  When off, admin must click start.">Auto start</span></td>
			<td>
				<?php $this->load->view('components/toggle_switch',
													array('id' => 'draftautostart',
														'url' => site_url('admin/draft/ajax_toggle_auto_start'),
														'var1' => 'draftautostart',
														'is_checked' => ($settings->draft_start_time !=0)));
                        		?>
			</td>
		</tr>
	</table>
</div>

<div class="section">
	<div class="is-size-5">Admin Live Options</div>
</div>



<script type="text/javascript">

var picker_options = {
							enableTime: true,
							// altInput: true,
							//altFormat: "F j, Y H:i",
							//altInputClass:"",
							minDate: "2017",
							dateFormat: "Y-m-d H:i",
						};
	$('#drafttime-input').flatpickr(picker_options);

$(document).ready(function(){

	// $('body').on('focus',"#drafttime-edit", function(){
	// 	$(this).fdatepicker({
	// 		format: 'yyyy-mm-dd hh:ii',
	// 		pickTime: true
	// 	});
	// })

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
