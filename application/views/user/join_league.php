<div class="row callout" style="max-width:625px;">
    <div class="columns">
        <?php if(array_key_exists($join_league_id,$this->session->userdata('leagues'))): ?>
            <div class="row">
                <div class="columns">
                    <h6>You are already in this league!</h6>
                </div>
            </div>
        <?php else:?>
        <div class="row">
            <div class="columns">
                <h6>Do you want to join the league with this account?</h6>
            </div>
        </div>
        <div class="row">
            <div class="columns">
                <table class="table" style="width:500px;">
                	<?php if($is_owner == false): ?>
                    <tr>
                    	<td>First Name</td>
                    	<td style="width:33%"><input type="text" id="first-name"></input></td>
                    	<td></td>
                    </tr>
                    <tr>
                    	<td>Last Name</td>
                    	<td style="width:33%"><input type="text" id="last-name"></input></td>
                    	<td></td>
                    </tr>
            	    <?php endif;?>
                    <tr>
                    	<td>Team Name</td>
                    	<td style="width:33%"><input type="text" id="team-name"></input></td>
                    	<td></td>
                    </tr>
                </table>
        	</div>
        </div>
        <div class="row">
        	<div class="columns">
        		<button class="button" id="join-league">Join League</button>
        	</div>
        </div>
        <?php endif;?>
    </div>
</div>


<script>

    $("#join-league").on("click",function(){
        var url = "<?=site_url('user/do_joinleague/'.$mask_id.'/'.$code)?>";
        var code = "<?=$code?>"
        var first = $("#first-name").val();
        var last = $("#last-name").val();
        var teamname = $("#team-name").val();
        var mask_id = "<?=$mask_id?>";
        $.post(url, {'mask_id':mask_id, 'code': code, 'first':first,'last':last,'team_name':teamname},function(data){
        	window.location.replace("<?=site_url('/')?>");
        });
    });



</script>
