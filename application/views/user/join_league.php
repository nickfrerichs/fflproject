<div class="row callout" style="max-width:625px;">
    <div class="columns">
        <?php if($this->session->userdata('leagues') && array_key_exists($join_league_id,$this->session->userdata('leagues'))): ?>
            <div class="row">
                <div class="columns">
                    <h5 class="text-center"> League: <?=$league_name?></h5>
                    <h6 class="text-center">You are already in this league!</h6>
                </div>
            </div>
        <?php else:?>
        <div class="row">
            <div class="columns">
                <h5 class="text-center">League: <?=$league_name?></h5>
            </div>
        </div>
        <div class="row">
            <div class="columns" style="width:500px;">
                <form id="join-form" data-abide novalidate>
                	<?php if($is_owner == false): ?>
                    <label for="first-name" class="text-left">First Name</label>
                    <input id="first-name" name="first-name" type="text" value="" required/>

                    <label for="last-name" class="text-left">Last Name</label>
                    <input id="last-name" name="last-name" type="text" value="" required/>

            	    <?php endif;?>
                    <label for="team-name" class="text-left">Team Name</label>
                    <input id="team-name" name="team-name" type="text" value="" required/>

                    <?php if($code_required): ?>
                        <label for="join-code" class="text-left">League Password</label>
                        <input id="join-code" name="join-code" type="text" value="" required/>
                    <?php endif;?>
                </form>

                    <button id="join-league" class="button small">Join League</button>


        	</div>
        </div>
        <?php endif;?>
    </div>
</div>


<script>

    $("#join-league").on("click",function(){

        $('#join-form').foundation('validateForm',$('#join-form'));
        if ($('#join-form').find('.form-error.is-visible').length || $('#join-form').find('.is-invalid-label').length) {
            return false;
        }

        var url = "<?=site_url('user/do_joinleague/'.$mask_id)?>";
        var code = $("#join-code").val();
        var first = $("#first-name").val();
        var last = $("#last-name").val();
        var teamname = $("#team-name").val();
        var mask_id = "<?=$mask_id?>";

        $.post(url, {'mask_id':mask_id, 'code': code, 'first':first,'last':last,'team_name':teamname},function(data){
            console.log(data);
            var d = $.parseJSON(data);
                console.log(d);

            if (d.success)
            {
        	       window.location.replace("<?=site_url('/')?>");
            }
            else
            {
                notice(d.msg,'error');
            }
        });
    });



</script>
