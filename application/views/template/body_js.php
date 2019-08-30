
<!-- jquery -->
<script src="<?=site_url('js/jquery-3.2.1.min.js')?>"></script>
<script src="<?=site_url('js/jquery-migrate-3.0.0.min.js')?>"></script>

<!-- Cropper to upload user images -->
<script src="<?=site_url('js/cropper.min.js')?>"></script>

<!-- Event source polyfill for IE browsers -->
<script src="<?=site_url('js/eventsource.min.js')?>"></script>

<!-- jBox for chat/alerts/notices/error popups -->
<script src="<?=site_url('js/jBox.min.js')?>"></script>
<script src="<?=site_url('js/jBox.Notice.js')?>"></script>

<!-- My custom JS things -->
<script>window.BASE_URL = "<?=site_url()?>";</script>
<?php if($this->session->userdata('debug') == 1): ?>
	<script>window.DEBUG_ENABLED = true;</script>
<?php endif;?>
<?php if($this->session->userdata('team_id')): ?>
<script>window.TEAM_ID = <?=$this->session->userdata('team_id')?>;</script>
<?php endif;?>
<?php if($this->session->userdata('is_league_admin')): ?>
	<script>window.LEAGUE_ADMIN = true;</script>
<?php else: ?>
	<script>window.LEAGUE_ADMIN = false;</script>
<?php endif;?>

<!-- Leave these at the end since they may require the above -->
<script src="<?=site_url('js/fflproject.js?1.10')?>"></script>
<script src="<?=site_url('js/fflproject-sse.js?1.16')?>"></script>
<script src="<?=site_url('js/fflproject-chat.js?1.09')?>"></script>
