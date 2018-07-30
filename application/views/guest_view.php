<section class="hero is-large is-dark" style="min-height:750px;">
  	<div class="hero-body">
		<div class="columns is-centered">
			<div class="column is-two-fifths">
			<?php if (!$admin_exists): ?>
				<div class="is-size-3 has-text-link"> Welcome to the FFL Project </div>
				<br>
				To get started: <a href="<?=site_url('accounts/register')?>"><b class="has-text-light">Create a Super Admin Account</b></a>
			<?php else: ?>

				<form role="form" method="post" action="auth/login">
					<h1 class="is-size-3 has-text-link"><?=$site_name?></h1>
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
								<input type="text" class="input" placeholder="What is <?=$captcha?> ?" name="math_captcha_response_field">
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
							<button type="submit" class="button is-link">Sign in</button>
						</div>
					</div>
			
				</form>
				<div><a id="show-forgot-password" href="#">Forgot Password</a></div>
				<div id="forgot-password-box" class="is-hidden">
					<br>
					<div class="is-size-5 has-text-link">Password reset</div>
<!--					<form action="<?=site_url('accounts/forgot')?>" method="post"> -->
					<div class="field">
						<div class="control">
						<input id="forgot-password-email" class="input" type="text" placeholder="email address">
						</div>
					</div>
					<div class="columns">
						<div class="column is-narrow">
							<div class="field">
								<div class="control">
									<button id="reset-password-button" class="button is-link">Reset My Password</button><br>
									<a id="cancel-forgot">Cancel</a>
								</div>
							</div>
						</div>
						<div id="forgot-message" class="column">
							
						</div>
					</div>
<!--					</form> -->
					<div></div>
				</div>

			<?php endif;?>
			</div>
		</div>
	</div>
	
</section>
<!-- <section class="hero is-link is-large">
	<div class="hero-body">
	</div>
</section> -->

<script>
		
	$('#show-forgot-password, #cancel-forgot').on('click',function(e){
		$('#forgot-password-box').toggleClass('is-hidden');
		
		e.preventDefault();
	});

	$('#reset-password-button').on('click',function(){
		var email = $('#forgot-password-email').val();
		var url = "<?=site_url('accounts/ajax_forgot/')?>";
		$.post(url,{"email" : email},function(data){
			if (data.success)
			{
				if(data.sent)
				{
					notice('A password reset URL has been emailed to you.','success');
				}
				else{
					notice('Email address not found.','error');

				}
			}
		},'json');
	});
</script>
