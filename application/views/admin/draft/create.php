<div class="container">
	<div class="page-heading">
		Create Draft Order
	</div>
	<?php if ($draft_exists > 0): ?>
	<div class="col-xs-12 text-center table-heading">Warning: This will wipe out existing <?=$year?> draft data!!</div>
	<?php endif; ?>
	<div class="col-xs-2"></div>
	<div class="col-xs-8">
		<table class="table">
		<?php for ($i=1; $i <= count($teams); $i++): ?>
		<tr>
			<td class="vert text-right">Pick <?=$i?></td>
			<td>
			<select id="pick_<?=$i?>"class="form-control choose-team">
				<option value="0"></option>
				<?php foreach ($teams as $t): ?>
				<option value="<?=$t->team_id?>"><?=$t->team_name?></option>
				<?php endforeach; ?>
			</select>
			</td>
		</tr>
		<?php endfor; ?>
		<tr>
			<td class="vert text-right"># of Rounds</td>
			<td>
				<select id="rounds" class="form-control">
					<?php for ($i=1; $i<=40; $i++): ?>
					<option value="<?=$i?>"><?=$i?></option>
					<?php endfor; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td></td><td>			
		    <label>
		      <input id="reverse" type="checkbox"> Reverse every other round
		    </label>
			</td>
		</tr>  	
	  	<tr>
		  	<td></td><td>
			<button id="reset-button" class="btn btn-default">Reset</button>
			<button id="create-button" class="btn btn-default">Create</button>
			</td>
		</tr>
		</table>
	</div>
	<div class="col-xs-2"></div>
</div>

<script>
$(document).ready(function(){

	$(".choose-team").on("change",function(){
		$(".choose-team option[value='"+$(this).val()+"']").not($(this).find("option")).remove();

	});
	$("#reset-button").on("click",function(){
		location.reload();
	});
	$("#create-button").on("click",function(){
		url = "<?=site_url('admin/draft/do_create')?>";
		order = getorderarray();
		rounds = $("#rounds").val();
		$.post(url,{'order[]' : order, 'rounds' : rounds, 'reverse' : $("#reverse").prop("checked")}, function(data){
			window.location.replace("<?=site_url('admin/draft')?>");
		});
	});

	function getorderarray()
	{
		var order = [];
		$(".choose-team").each(function(){
			//order[$(this).attr('id').replace("pick_","")] = $(this).val();
			order.push($(this).val());
		});
	
		return order;

	}

});
</script>