<!-- Foundation 6 -->
<script src="<?=site_url('js/vendor/jquery.js')?>"></script>
<script src="<?=site_url('js/vendor/foundation.min.js')?>"></script>
<script src="<?=site_url('js/vendor/what-input.js')?>"></script>
<script src="<?=site_url('js/vendor/jquery.visible.min.js')?>"></script>
<script src="<?=site_url('js/cropper.min.js')?>"></script>
<script src="<?=site_url('js/eventsource.min.js')?>"></script>

<!-- Using jbox for chat/alerts/notices/error popups -->
<script src="<?=site_url('js/jBox.min.js')?>"></script>

<!-- My custom JS, things that aren't imbedded in the page go here -->
<script>window.BASE_URL = "<?=site_url()?>";</script>
<?php if($this->session->userdata('debug') == 1): ?>
	<script>window.DEBUG_ENABLED = true;</script>
<?php endif;?>
<script>window.TEAM_ID = <?=$this->session->userdata('team_id')?>;</script>
<?php if($this->session->userdata('is_league_admin')): ?>
	<script>window.LEAGUE_ADMIN = true;</script>
<?php else: ?>
	<script>window.LEAGUE_ADMIN = false;</script>
<?php endif;?>
<script src="<?=site_url('js/fflproject.js?1.09')?>"></script>
