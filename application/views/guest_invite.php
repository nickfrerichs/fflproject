
<br>
<div class="section">
	<div class="columns is-centered has-background-dark has-text-centered">
    	<div class="column is-6-tablet">
			<a href="<?=site_url('')?>"><div class="is-size-3 has-text-link"><?=$site_name?></div></a>
			<div class="is-size-6 has-text-light">Join League: <strong class="has-text-light"><?=$league_name?></strong></div>
			<br>
			<a href="<?=site_url('/?redirect=joinleague/invite/'.$mask_id)?>"><div class="is-link button">Already have an account?</div></a><br>
			<div class="has-text-light">- or -</div>
			<a href="<?=site_url('accounts/register/'.$mask_id)?>"><div class="is-link button">Create a new account to join.</div></a>
			<br><br>
    	</div>
    </div>
</div>
