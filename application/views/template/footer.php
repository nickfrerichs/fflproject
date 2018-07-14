

<footer class="fflp-footer has-background-dark has-text-white">
	<div class= "has-text-centered" style="padding-left:10px;padding-right:10px;">
		<div class="columns">

			<div class="column has-text-left">
				<!-- <?php if (count($this->session->userdata('leagues')) > 1): ?>
					<?php $this->load->view('template/modals/change_league'); ?>
				<div class="columns small-6">
					<a data-open="change-league-modal">Change League</a>
				</div>
				<?php endif; ?> -->
				<?php $isadmin = ($this->session->userdata('is_league_admin') || $this->ion_auth->is_admin()); ?>

				<?php if ($isadmin && stripos($v,'admin') === false) {echo ' <a href = '.site_url().'admin><span class="has-text-light">Admin</span> </a>';} ?>
				<?php if ($isadmin && stripos($v,'admin') > -1) {echo ' <a href = '.site_url().'><span class="has-text-light">User</span> </a>';} ?>
			</div>
			<div class="column is-narrow has-text-link small-text">
				2018 FFL Project
			</div>
		</div>
	</div>
</footer>
<?php if($this->session->userdata('debug')):?>
	<?php $this->load->view('template/debug_block'); ?>
<?php endif;?>

