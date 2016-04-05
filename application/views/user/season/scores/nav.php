<div class="container">
	<div class="row">
		<form id="select-form" class="navbar-form text-center" method="post">
		<?php for($i=1; $i<=17; $i++):?>
		    <?php if($i == $week):?>
		        <button class="btn btn-default active" style="min-width:40px;" name="week" value="<?=$i?>"><?=$i?></button>
		    <?php else: ?>
		    	<button class="btn btn-default" style="min-width:40px" name="week" value="<?=$i?>"><?=$i?></button>
		    <?php endif; ?>
		<?php endfor; ?>
		<select id="select-year" name="year" class="form-control">
			<?php foreach ($years as $y): ?>
				<?php if ($y->year == $year): ?>
				<option value="<?=$y->year?>" selected><?=$y->year?></option>
				<?php else: ?>
				<option value="<?=$y->year?>"><?=$y->year?></option>
				<?php endif; ?>
			<?php endforeach; ?>
		</select>
		</form>
	</div>
</div>

<script>
$("#select-year").on("change",function(){
	$('#select-form').submit();
})
</script>