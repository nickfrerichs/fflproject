<div class="section">

        <?php if($this->session->userdata('leagues') && array_key_exists($join_league_id,$this->session->userdata('leagues'))): ?>

            <div class="is-size-5"> League: <?=$league_name?></div>
            <div class="id-size-6">You are already in this league!</div>

        <?php else:?>
            <div class="columns is-centered">
                <div class="column is-one-third-tablet">
                    <div class="is-size-5">League: <?=$league_name?></div>
                    <br>
                    <form>
                        <!-- <?php if($is_owner == false): ?>
                        <div class="field">
                            <label for="first-name" class="label">First Name</label>
                            <div class="control">
                                <input id="first-name" name="first-name" class="input" type="text" value="" required/>
                            </div>
                        </div>

                        <div class="field">
                            <label for="last-name" class="label">Last Name</label>
                            <div class="control">
                                <input id="last-name" name="last-name" class="input" type="text" value="" required/>
                            </div>
                        </div>
                        <?php endif;?> -->

                        <div class="field">
                            <label for="team-name" class="label">Team Name</label>
                            <div class="control">
                                <input data-post-id="team_name" class="input join-league-info" type="text" value="" required/>
                            </div>
                        </div>
                        <?php if($code_required): ?>
                        <div class="field">
                            <label for="join-code" class="label">League Password</label>
                            <div class="control">
                                <input data-post-id="join_code" class="input join-league-info" type="text" value="" required/>
                            </div>
                        </div>
                        <?php endif;?>
                        <!-- <button id="join-league" class="button is-link">Join League</button> -->
                        <button class="button is-link ajax-submit-button" 
                            data-url="<?=site_url('user/do_joinleague/'.$mask_id)?>"
                            data-varclass="join-league-info"
                            data-reload="false"
                            data-redirect="<?=site_url('')?>">
                            Join League
                        </button>
                    </form>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>


<script>

    // $("#join-league").on("click",function(){

    //     $('#join-form').foundation('validateForm',$('#join-form'));
    //     if ($('#join-form').find('.form-error.is-visible').length || $('#join-form').find('.is-invalid-label').length) {
    //         return false;
    //     }

    //     var url = "<?=site_url('user/do_joinleague/'.$mask_id)?>";
    //     var code = $("#join-code").val();
    //     var first = $("#first-name").val();
    //     var last = $("#last-name").val();
    //     var teamname = $("#team-name").val();
    //     var mask_id = "<?=$mask_id?>";

    //     $.post(url, {'mask_id':mask_id, 'code': code, 'first':first,'last':last,'team_name':teamname},function(data){
    //         console.log(data);
    //         var d = $.parseJSON(data);
    //             console.log(d);

    //         if (d.success)
    //         {
    //     	       window.location.replace("<?=site_url('/')?>");
    //         }
    //         else
    //         {
    //             notice(d.msg,'error');
    //         }
    //     });
    // });



</script>
