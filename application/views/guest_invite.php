
<br>

<div class="row callout" style="margin-top:10px;max-width:425px;">
    <div class="columns text-center" >
    	<div class="row">
    		
    		<div class="columns">
    			<h3 class="text-center"><?=$site_name?></h3>
    			<h6>Join League: <strong><?=$league_name?></strong></h6>
    		</div>
    	</div>
	<div class="row">
        <div class="columns">
             <a href="<?=site_url('/?redirect=joinleague/invite/'.$mask_id.'/'.$code)?>"><br>Already have an account?</a><br>
            	- or -<br>
        	<a href="<?=site_url('accounts/register/'.$mask_id.'/'.$code)?>">Create a new account to join.</a>
    	</div>
    </div>
</div>
