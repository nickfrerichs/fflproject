<!DOCTYPE html>
<html lang="en">
	<?php $this->load->view('template/head'); ?>
	<?php if(!$admin_exists):?>
		<h4>Create admin account</h4>
	<?php endif;?>

    <?=form_open(current_url())?>
    <li>
        <?=form_label('First Name','first_name')?>
        <?=form_input('first_name')?>
    </li>
    <li>
        <?=form_label('Last Name','last_name')?>
        <?=form_input('last_name')?>
    </li>
    <li>
        <?=form_label('Email Address','email_address')?>
        <?=form_input('email_address')?>
    </li>
    <li>
        <?=form_label('Team Name','team_name')?>
        <?=form_input('team_name')?>
    </li>
    <li>
        <?=form_label('Username','username')?>
        <?=form_input('username')?>
    </li>
    <li>
        <?=form_label('Password','password')?>
        <?=form_password('password')?>
    </li>
    <li>
        <?=form_submit('register','Register')?>
    </li>
</html>
