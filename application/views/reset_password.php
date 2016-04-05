<!DOCTYPE html>
<html lang="en">
	<?php $this->load->view('template/head'); ?>

    <div class="container text-center">
        <br>
        <br>
        <h4>Choose a new password</h4>
        <form action="<?=site_url('accounts/reset_password/'.$user_id.'/'.$reset_token)?>" method="post">
            <input type="password" placeholder="new password" name="password1"><br>
            <input type="password" placeholder="confirm password" name="password2">
            <br><br>
            <input type="submit" class="btn btn-default" name="submit" value="Reset My Password">
        </form>
        <a href="<?=site_url('')?>">Cancel</a>
    </div>

</html>
