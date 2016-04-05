<!DOCTYPE html>
<html lang="en">
	<?php $this->load->view('template/head'); ?>
    <div class="container text-center">
		<hr>
        <br>
        <br>
        <h4>Password reset</h4>
        <form action="<?=site_url('accounts/forgot')?>" method="post">
            <input type="text" placeholder="email address" name="email_address">
            <br><br>
            <input type="submit" class="btn btn-default" value="Reset My Password">
        </form>
        <a href="<?=site_url('')?>">Cancel</a>
		<?php if (isset($sent)): ?>
			<meta http-equiv="refresh" content="3;URL='<?=site_url('')?>'" />    
			<br>
			<div class="text-center">
				Reset information as been sent to your email address.
			</div>
		<?php endif;?>
    </div>


</html>
