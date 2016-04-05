<div class="container">

<div class="page-heading"><?=$year?> Live Draft options</div>

<table class="table">
	<tr>
		<td><div>Draft Date & Time<div><div>( m / d - h : m )</td>
		<td>
			<strong>
			<form class="form-inline">
				<select id="mon" class="form-control">
				<?php for($m=1; $m<=12; $m++): ?>
					<option value="<?=$m?>"><?=$m?></option>
				<?php endfor;?>
				</select> /
				<select id="day" class="form-control">
				<?php for($m=1; $m<=31; $m++): ?>
					<option value="<?=$m?>"><?=$m?></option>
				<?php endfor;?>
				</select> - 
				<select id="hour" class="form-control">
				<?php for($m=1; $m<=12; $m++): ?>
					<option value="<?=$m?>"><?=$m?></option>
				<?php endfor;?>
				</select> : 
				<select id="min" class="form-control">
				<?php for($m=0; $m<=59; $m=$m+5): ?>
					<option value="<?=$m?>"><?=$m?></option>
				<?php endfor;?>
				</select>
				<select id="ampm" class="form-control">
					<option value="am">am</option>
					<option value="pm">pm</option>
				</select>
			</form>
			</strong>
		</td>
	</tr>
	<tr>
		<td>Seconds per pick</td>
		<td>
			<form class="form-inline">
			<input id="pick-time" class="form-control">
		</form>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<button id="save" class="btn btn-default">Save</button>
		</td>
	</tr>
</table>

Admin Live Options

</div>


<script type="text/javascript">

$(document).ready(function(){




	$('#save').on('click', function(){
		var mon = $("#mon").val();
		var day = $("#day").val();
		var hour = $("#hour").val();
		var min = $("#min").val();
		var ampm = $("#ampm").val();
		var pick = $("#pick-time").val();
		url ="<?=site_url('admin/draft/save_draft_settings')?>";
		$.post(url, {'mon':mon,'day':day,'hour':hour,'min':min,'ampm':ampm,'pick':pick}, function(data){
			window.location.replace("<?=site_url('admin/draft')?>");
		});
	});

});

</script>