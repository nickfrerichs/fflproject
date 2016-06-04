<div class="row callout text-center">
	<div class="columns">
		<div class="row text-center">
			<div class="columns">
				<h5>Admin Panel</h5>
			</div>
		</div>

		<div class="row">
			<div class="columns">
				<?php if(!$has_leagues): ?>
				To get started, <a href="<?=site_url('admin/site/create_league')?>">Create a League.</a>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>