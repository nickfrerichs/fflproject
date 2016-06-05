<!DOCTYPE html>
<html lang="en">
<?php $this->load->view('template/head'); ?>
<br><br>
<div class="row text-center callout" style="max-width: 425px;">
	<div class="columns">
        <br>
		<h4><?=$site_name?></h4>
        <h5>Password reset</h5>
        <form action="<?=site_url('accounts/forgot')?>" method="post">
            <input type="text" placeholder="email address" name="email_address">
            <input type="submit" class="button small" value="Reset My Password">
        </form>
        <a href="<?=site_url('')?>">Cancel</a>
		<?php if (isset($sent)): ?>
			<!-- <meta http-equiv="refresh" content="3;URL='<?=site_url('')?>'" /> -->
			<br>
			<div class="text-center">
				Reset information as been sent to your email address.
			</div>
		<?php endif;?>
    </div>
</div>


</html>
