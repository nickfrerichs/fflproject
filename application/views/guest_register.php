<!DOCTYPE html>
<html lang="en">
	<body>
		<br><br>
		<?php $this->load->view('template/head'); ?>

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

					<table class="table">
						<tbody class="text-left">
					    <?=form_open(current_url())?>
					    <?php if($admin_exists): ?>
					        <tr>
								<td><?=form_label('First Name','first_name')?></td>
					            <td><?=form_input('first_name')?></td>
					        </tr>
					        <tr>
					            <td><?=form_label('Last Name','last_name')?></td>
					            <td><?=form_input('last_name')?></td>
					        </tr>
					        <tr>
					            <td><?=form_label('Team Name','team_name')?></td>
					            <td><?=form_input('team_name')?></td>
					        </tr>
					    <?php endif;?>
					    <tr>
					        <td><?=form_label('Email Address','email_address')?></td>
					        <td><?=form_input('email_address')?></td>
					    </tr>
					    <tr>
					        <td><?=form_label('Username','username')?></td>
					        <td><?=form_input('username')?></td>
					    </tr>
					    <tr>
					        <td><?=form_label('Password','password')?></td>
					        <td><?=form_password('password')?></td>
					    </tr>
					    <tr>
					        <td colspan=2><input class="button small" type="submit" name="register" value="Register"  /></td>
					    </tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</body>
</html>
