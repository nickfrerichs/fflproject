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
				<td><div>Draft Date & Time<div><div>( m / d - h : m )</td>
				<td>


					<input id="draft-date" type="text">


				</td>
			</tr>
			<tr>
				<td>Seconds per pick</td>
				<td>
					<input id="pick-time" type="text">
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<button id="save" class="button small">Save</button>
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


	$("#draft-date").fdatepicker({
			format: 'yyyy-mm-dd hh:ii',
			pickTime: true
	})

	$('#save').on('click', function(){
		var date = $("#draft-date").val()
		console.log(date);

		var pick = $("#pick-time").val();
		url ="<?=site_url('admin/draft/save_draft_settings')?>";
		$.post(url, {'date':date,'pick':pick}, function(data){
			window.location.replace("<?=site_url('admin/draft')?>");
		});
	});

});

</script>