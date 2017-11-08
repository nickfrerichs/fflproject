<section class="hero is-info is-medium">
  	<div class="hero-body">
		<div class="columns is-centered">
			<div class="column is-two-fifths">
			<?php if (!$admin_exists): ?>
				<h5> Welcome to the FFL Project </h5>
				<br>
				To get started, please <a href="<?=site_url('accounts/register')?>">Create a Super Admin Account</a>
			<?php else: ?>

				<form role="form" method="post" action="auth/login">
					<h1 class="title"><?=$site_name?></h1>
					<div class="field">
						<div class="control">
							<input type="text" class="input" placeholder="Username" id="identity" name="login_identity" required autofocus />
						</div>
					</div>
					<div class="field">
						<div class="control">
							<input type="password" class="input" placeholder="Password"  required id="password" name="login_password" />
						</div>
					</div>
					<?php if(isset($redirect)): ?>
						<input type="hidden" name="redirect" value="<?=$redirect?>">
					<?php endif;?>

					<?php if (isset($captcha)):?>
						<?php if($use_recaptcha): ?>
							<?=$captcha?>
						<?php else: ?>
						<div class="field">
							<div class="control">
								<input type="text" class="input" placeholder="<?=$captcha?>" name="math_captcha_response_field">
							</div>
						</div>
						<?php endif;?>

						 <br>
					 <?php endif; ?>
		<!--
					<label class="checkbox">
						<input type="checkbox" id="remember_me" name="remember_me" value="1">
						Remember Me
					</label>
		-->
					<div class="field">
						<div class="control">
							<button type="submit" class="button">Sign in</button>
						</div>
					</div>
			
				</form>
				<div><a href="<?=site_url('accounts/forgot')?>">Forgot Password</a></div>

			<?php endif;?>
			</div>
		</div>
	</div>
</section>
