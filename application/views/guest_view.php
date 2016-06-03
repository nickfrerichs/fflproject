<!DOCTYPE html>
<html lang="en">
	<body>
		<br><br>
		<?php $this->load->view('template/head'); ?>
		<div class="row align-center">
			<div class="columns medium-6">
			<?php if (!$admin_exists): ?>
				<h5> Installation </h5>
				<br>
				<a href="<?=site_url('accounts/register')?>">Create Admin Account</a>
			<?php else: ?>

				<form role="form" method="post" action="auth/login">
					<h2>FFL</h2>
						<input type="text" class="form-control" placeholder="Username" id="identity" name="login_identity" required autofocus />
					<input type="password" class="form-control" placeholder="Password"  required id="password" name="login_password" />
					<?php if(isset($redirect)): ?>
						<input type="hidden" name="redirect" value="<?=$redirect?>">
					<?php endif;?>

					<?php if (isset($captcha)) {echo $captcha;} ?>
		<!--
					<label class="checkbox">
						<input type="checkbox" id="remember_me" name="remember_me" value="1">
						Remember Me
					</label>
		-->
					<button type="submit">Sign in</button>
				</form>
				<div><a href="<?=site_url('accounts/forgot')?>">Forgot Password</a></div>

			<?php endif;?>
			</div>
		</div>
	</body>
</html>
