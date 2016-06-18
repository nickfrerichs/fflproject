<div class="row align-center callout" style="max-width: 425px;">
	<div class="columns small-12">
		<div class="text-center">
			<br>
		<?php if(!$admin_exists):?>
			<h4>Super Admin Account</h4>
			<small>(email addresses must be unique and cannot be used for multiple accounts)</small>
		<?php else: ?>
			<h3><?=$site_name?></h3>
			<h5>Create new account</h5>
		<?php endif;?>
		<?php if(isset($error)): ?>
			<div class="row callout alert">
				<div class="columns">
					<?=$error?>
				</div>
			</div>
		<?php endif;?>
			    <form action="<?=site_url('accounts/register/'.$maskid)?>" method="post" data-abide novalidate>
			    <?php if($admin_exists): ?>
					<?php if($code_required): ?>
						<label for="league_password" class="text-left">League Password</label>
						<input type="text" name="league_password" value="" required/>
					<?php endif;?>

					<label for="first_name" class="text-left">First Name</label>
					<input type="text" name="first_name" value="" required/>

					<label for="last_name" class="text-left">Last Name</label>
					<input type="text" name="last_name" value="" required/>

					<label for="team_name" class="text-left">Team Name</label>
					<input type="text" name="team_name" value="" required/>

			    <?php endif;?>
			    <label for="email_address" class="text-left">Email Address</label>
			    <input type="text" name="email_address" value="" required/>


			    <label for="username" class="text-left">Username</label>
			    <input type="text" name="username" value="" required/>


			    <label for="password" class="text-left">Password</label>
			    <input id="password" type="password" name="password" value=""  required>


				<label for="password2" class="text-left">Confirm Password</label>
				<input type="password" name="password2" value="" data-equalto="password">
				<span class="form-error">
						Passwords do not match.
				</span>

			    <input class="button small" type="submit" name="register" value="Register"  />

				</form>
		</div>
	</div>
</div>
