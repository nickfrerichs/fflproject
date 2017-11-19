

<footer class="footer">
<div id="footer-bar">
		<!-- <?php if (count($this->session->userdata('leagues')) > 1): ?>
			<?php $this->load->view('template/modals/change_league'); ?>
		<div class="columns small-6">
			<a data-open="change-league-modal">Change League</a>
		</div>
		<?php endif; ?> -->
		<?php $isadmin = ($this->session->userdata('is_league_admin') || $this->flexi_auth->is_admin()); ?>
		<div class="columns small-6 text-right">
		<?php if ($isadmin && stripos($v,'admin') === false) {echo ' <a href = '.site_url().'admin>Admin </a>';} ?>
		<?php if ($isadmin && stripos($v,'admin') > -1) {echo ' <a href = '.site_url().'>User </a>';} ?>
		</div>
</div>
</footer>
