<div class="section">
	<div class="columns is-centered">
		<div class="column is-4-tablet">
			<div class="is-size-5">Admin Area</div>
			<br>
			<?php if(!$has_leagues): ?>
			To get started, <a href="<?=site_url('admin/site/create_league')?>">Create a League.</a>
			<?php endif;?>
		</div>
	</div>
</div>