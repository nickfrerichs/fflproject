<br>
<h4 class="text-center"><?=$site_name?></h4>
<div class="row callout" style="max-width:450px;">
    <div class="columns text-center">
        <br>
        <h5>Choose a new password</h5>
        <form action="<?=site_url('accounts/reset_password/'.$user_id.'/'.$reset_token)?>" method="post">
            <input type="password" placeholder="new password" name="password1"><br>
            <input type="password" placeholder="confirm password" name="password2">
            <br>
            <input type="submit" class="button small" name="submit" value="Reset My Password">
        </form>
        <a href="<?=site_url('')?>">Cancel</a>
    </div>
</div>
