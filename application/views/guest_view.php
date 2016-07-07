		<br><br>
		<div class="row align-center" style="max-width:425px;">
			<div class="columns callout" >
			<?php if (!$admin_exists): ?>
				<h5> Welcome to the FFL Project </h5>
				<br>
				To get started, please <a href="<?=site_url('accounts/register')?>">Create a Super Admin Account</a>
			<?php else: ?>

				<form role="form" method="post" action="auth/login">
					<h2><?=$site_name?></h2>
						<input type="text" class="form-control" placeholder="Username" id="identity" name="login_identity" required autofocus />
					<input type="password" class="form-control" placeholder="Password"  required id="password" name="login_password" />
					<?php if(isset($redirect)): ?>
						<input type="hidden" name="redirect" value="<?=$redirect?>">
					<?php endif;?>

					<?php if (isset($captcha)):?>
						<?php if($use_recaptcha): ?>
							<?=$captcha?>
						<?php else: ?>
							<input type="text" placeholder="<?=$captcha?>" name="math_captcha_response_field">
						<?php endif;?>

						 <br>
					 <?php endif; ?>
		<!--
					<label class="checkbox">
						<input type="checkbox" id="remember_me" name="remember_me" value="1">
						Remember Me
					</label>
		-->

					<button type="submit" class="button small">Sign in</button>
				</form>
				<div><a href="<?=site_url('accounts/forgot')?>">Forgot Password</a></div>

			<?php endif;?>
			</div>
		</div>
