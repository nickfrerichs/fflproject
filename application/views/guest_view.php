<!DOCTYPE html>
<html lang="en">
	<hr>
	<?php $this->load->view('template/head'); ?>
	<?php if (!$admin_exists): ?>
		<div class="text-center"><a href="<?=site_url('accounts/register')?>">Create Admin Account</a></div>
	<?php else: ?>

	<div class="container text-center" style="max-width: 300px;">

			<form class="form-signin form-group" role="form" method="post" action="auth/login">
				<h2 class="form-signin-heading text-center">FFL</h2>

				<input type="username" class="form-control" placeholder="Username" id="identity" name="login_identity" required autofocus />

				<input type="password" class="form-control" placeholder="Password"  required id="password" name="login_password" />

				<?php if (isset($captcha)) {echo $captcha;} ?>
	<!--
				<label class="checkbox">
					<input type="checkbox" id="remember_me" name="remember_me" value="1">
					Remember Me
				</label>
	-->
				<button class="btn btn-lg btn-default" style="display:block; width:100%" type="submit">Sign in</button>
			</form>


			<div class="text-center"><a href="<?=site_url('accounts/forgot')?>">Forgot Password</a></div>

	</div>
	<?php endif;?>
</html>
