<div class="container text-center">
	<div class="row">
		<div class="text-center center-block" style="width:400px;">
			<br>
		<?php if(!$admin_exists):?>
			<h4>Create admin account</h4>
		<?php else: ?>
			<h4>Create new account</h4>
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
			        <td><?=form_submit('register','Register')?></td>
			    </tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
