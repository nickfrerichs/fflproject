<div class="section">
	<div class="columns is-centered has-background-dark has-text-light">
    	<div class="column is-6-tablet">
			<br>
			<a href="<?=site_url('')?>"><div class="is-size-3 has-text-link"><?=$site_name?></div></a>
			<?php if(isset($league_name)):?>
				League: <?=$league_name?>
				<br><br>
			<?php endif;?>
		<?php if(!$admin_exists):?>
			<div class="is-size-4 has-text-light">Super Admin Account</div>
			<small>(email addresses must be unique and cannot be used for multiple accounts)</small>
		<?php else: ?>
			<div class="is-size-5 has-text-light">Create new account and join</div>
		<?php endif;?>
		<hr>
		<?php if(isset($error)): ?>
			<div>
				<?=$error?>
			</div>
		<?php endif;?>
			    <!-- <form action="<?=site_url('accounts/register/'.$maskid)?>" method="post" data-abide novalidate> -->
				<!-- <form> -->
			<div class="field">
					<label for="first_name" class="label has-text-light">First Name</label>
					<div class="control">
						<input class="input register-info" type="text" data-post-id="first_name" />
					</div>
				</div>
				<div class="field">
					<label for="last_name" class="label has-text-light">Last Name</label>
					<div class="control">
						<input class="input register-info" type="text" data-post-id="last_name" />
					</div>
				</div>
			    <?php if($admin_exists): ?>
					<?php if($code_required): ?>
						<div class="field">
							<label for="league_password" class="label has-text-light">League Password</label>
							<div class="control">
								<input class="input register-info" type="text" data-post-id="league_password" />
							</div>
						</div>
					<?php endif;?>

					<div class="field">
						<label for="team_name" class="label has-text-light">Team Name</label>
						<div class="control">
							<input class="input register-info" type="text" data-post-id="team_name" />
						</div>
					</div>

			    <?php endif;?>
				<div class="field">
			    	<label for="email_address" class="label has-text-light">Email Address</label>
					<div class="control">
						<input class="input register-info" type="text" data-post-id="email_address" />
					</div>
				</div>

				<hr>
				<div class="field">
					<label for="username" class="label has-text-light">Username</label>
					<input class="input register-info" type="text" data-post-id="username" />
				</div>

				<div class="field">
					<label for="password" class="label has-text-light">Password</label>
					<input class="input register-info" data-post-id="password" type="password" name="password" value=""  >
				</div>

				<div class="field">
					<label for="password2" class="label has-text-light">Confirm Password</label>
					<input class="input register-info" data-post-id="password2" type="password" name="password2" value="" >
				</div>
			
				<div class="form-error is-hidden">
						Passwords do not match.
				</div>

			    <!-- <input class="button is-link" type="submit" name="register" value="Register"  />

				</form> -->

				<button class="button is-link ajax-submit-button" 
						data-url="<?=$register_url?>"
						data-varclass="register-info"
						data-reload="false"
						data-redirect="<?=site_url('')?>">
					Register!</button>
				<!-- </form>-->
				<br>
				<br>
				<br>
		</div>
	</div>
</div>

