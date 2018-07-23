<br>
<div class="section">

<div class="columns is-centered has-background-dark">

    <div class="column is-6-tablet">
    <a href="<?=site_url('')?>"><div class="is-size-4 has-text-link"><?=$site_name?></div></a>
        <br>
        <div class="is-size-5 has-text-light">Choose a new password</div>
        <form action="<?=site_url('accounts/reset_password/'.$user_id.'/'.$reset_token)?>" method="post">
        <div class="field">
			<div class="control">
            <input class="input" type="password" placeholder="new password" name="password1">
            </div>
        </div>
        <div class="field">
			<div class="control">
            <input class="input" type="password" placeholder="confirm password" name="password2">
            </div>
        </div>
        <div class="field">
							<div class="control">
            <input type="submit" class="button small is-link" name="submit" value="Reset My Password">
            </div>
            </div>
        </form>
    </div>
</div>
</div>