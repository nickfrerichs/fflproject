<div class="section">
	<div class="is-size-5">Create Draft Order</div>
</div>

<div class="section">

		<?php if ($draft_exists > 0): ?>
		<div class="text-center"><strong>Warning: This will wipe out existing <?=$year?> draft data and start the draft over!!</strong></div>
		<?php endif; ?>
			<table class="table is-narrow is-fullwidth fflp-table-fixed">
			<?php for ($i=1; $i <= count($teams); $i++): ?>
			<tr>
				<td class="text-right">Pick <?=$i?></td>
				<td>
					<div class="select">
						<select id="pick_<?=$i?>"class="choose-team">
							<option value="0"></option>
							<?php foreach ($teams as $t): ?>
							<option value="<?=$t->team_id?>"><?=$t->team_name?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</td>
			</tr>
			<?php endfor; ?>
			<tr>
				<td class="text-right"># of Rounds</td>
				<td>
					<div class="select">
						<select id="rounds">
							<?php for ($i=1; $i<=40; $i++): ?>
							<option value="<?=$i?>"><?=$i?></option>
							<?php endfor; ?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td></td><td>
			    <label class="checkbox">
			      <input id="reverse" type="checkbox"> Reverse every other round
			    </label>
				<?php if($traded_picks): ?>
				<label class="checkbox">
				  <input id="trades" type="checkbox" checked> Apply traded draft picks
				</label>
				<?php endif;?>
				</td>
			</tr>
		  	<tr>
			  	<td></td><td>
				<button id="reset-button" class="button is-small is-link">Reset</button>
				<button id="create-button" class="button is-small is-link">Create</button>
				</td>
			</tr>
			</table>


</div>

<script>
$(document).ready(function(){

	$(".choose-team").on("change",function(){
		//$(".choose-team option[value='"+$(this).val()+"']").not($(this).find("option")).remove();

	});
	$("#reset-button").on("click",function(){
		location.reload();
	});

	$("#create-button").on("click",function(){
		url = "<?=site_url('admin/draft/do_create')?>";
		order = getorderarray();
		rounds = $("#rounds").val();
		trades = $("#trades").prop("checked");
		$.post(url,{'order[]' : order, 'rounds' : rounds, 'reverse' : $("#reverse").prop("checked"), 'trades':trades}, function(data){
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
