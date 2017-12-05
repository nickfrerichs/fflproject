<?php $this->load->view('template/modals/stat_popup'); ?>

<!-- Modals that are hidden by default -->

<!-- Confirm modal -->
<!-- <div class="reveal" id="confirm-modal" data-reveal data-overlay="true">
    <div id="notclear" class="hide text-center" style="padding-bottom:10px;">Players waivers have not cleared, you'll be notified when they do.</div>
    <div class="text-center">
            <div class="drop-text">Drop: No One</div>
            <div class="pickup-text" style="padding-bottom:10px;">Pick up: No One</div>
        <button class="button" type="button" id="confirm-drop">
            Confirm
        </button>
        <button class="button" type-"button" id="cancel-drop" data-close aria-label="Close modal">
            Cancel
        </button>
    </div>
</div> -->

    <div class="modal" id="confirm-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
               <p class="modal-card-title">Waivers not clear</p>
               <button class="delete modal-close-button" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <h5>Waivers not clear</h5>
                <div id="notclear" class="hide text-center" style="padding-bottom:10px;">Players waivers have not cleared, you'll be notified when they do.</div>
                
                <div class="drop-text">Drop: No One</div>
                <div class="pickup-text" style="padding-bottom:10px;">Pick up: No One</div>
                <button class="button" id="confirm-drop">Confirm</button>
                <button class="button" id="cancel-drop" data-close aria-label="Close modal">Cancel</button>

            </section>
            <footer class="modal-card-foot">
                <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
            </footer>
            <!--  -->

        </div>
        
    </div>

<!-- Drop modal -->
<!-- <div class="reveal" id="drop-modal" data-reveal data-overlay="true">
    <div class="text-center">
            <div>
                <div class="text-center">
                    <h4>Drop Player</h4>
                    <h5>My Team Roster</h5>
                </div>
                <table class="table table-border table-condensed text-center table-striped table-hover">
                    <thead>
                        <th colspan=3 class="text-center">Player</th>
                    </thead>
                <tbody id="ww-drop-table" data-playerid="0">

                </tbody>
                </table>
                <button class="close-button" data-close aria-label="Close modal" type="button">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
    </div>
</div> -->



<div class="modal" id="drop-modal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
               <p class="modal-card-title">Drop Player</p>
               <button class="delete modal-close-button" aria-label="close"></button>
            </header>
            <section class="modal-card-body">
                <h5>Drop player</h5>

                <table class="table table-border table-condensed text-center table-striped table-hover">
                    <thead>
                        <th colspan=3 class="text-center">Player</th>
                    </thead>
                    <tbody id="ww-drop-table" data-playerid="0">

                    </tbody>
                </table>

            </section>
            <footer class="modal-card-foot">
                <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
            </footer>
            <!--  -->

        </div>
        
    </div>


<!-- End Modals -->

<div class="columns section">
    <div class="column">
        <h5 class="is-size-4">Waiver wire</h5>
    </div>
    <div class="column">
        <a href="<?=site_url('myteam/waiverwire/priority')?>">Priority & Rules</a> |
        <a href="<?=site_url('myteam/waiverwire/log')?>">Log</a>
        <?php if(!$this->session->userdata('offseason')): ?>
            | <a href="#" id="drop-only">Drop Player</a>
        <?php endif;?>   
    </div>
    
</div>

<?php if(count($pending) > 0): ?>
    <div class="columns section">
        <div class="column">
            <h6 class="is-size-5">Pending Requests</h6>
            <table class="table">
                <thead>
                    <th>Clear Time</th><th>Pick Up</th><th>Drop</th>
                </thead>
                <tbody>
            <?php foreach($pending as $i => $a): ?>
                <?php if ($a->clear_time)
                {

                    $remaining = $a->clear_time - time();
                    $hr = (int)($remaining / (60*60));
                    $min = (int)(($remaining - $hr*(60*60)) / 60);
                    $sec = (int)(($remaining - $hr*(60*60) - $min*60));
                    $clear_text = "(".$hr.":".$min.")";
                }
                else
                    {$clear_text = "";}
                ?>
                <tr>
                    <td>
                        <button class="button cancel-request" data-id="<?=$a->ww_id?>"><b>Cancel</b> <?=$clear_text?></button>
                    </td>
                    <td>
                        <?=$a->p_first.' ',$a->p_last?> (<?=$a->p_pos.' - '.$a->p_club_id?>)
                        <?php if(count($pending > 1) && $a->ww_id == $latest_request_id): ?>
                            <span style="font-size:.8em"><b>*Preferred*</b></span>
                        <?php elseif(count($pending)>1):?>
                            <span style="font-size:.8em"><a class="set-preferred" data-id="<?=$a->ww_id?>" href="#">(Set Preferred)</a></span>
                        <?php endif;?>
                    </td>
                    <td><?=$a->d_first.' ',$a->d_last?> (<?=$a->d_pos.' - '.$a->d_club_id?>)</td>
                </tr>
            <?php endforeach;?>
        </tbody>
        </table>
        </div>
    </div>


<?php endif;?>


<?php if($this->session->userdata('offseason')): ?>
    <?php $this->load->view('user/offseason'); ?>
<?php else: ?>

<!-- Search options -->
<div class="columns section">
    <div class="column">
        <div class="columns">
            <div class="column">
                <input type="text" class="player-list-text-input" data-for="ww-list" placeholder="Search">
            </div>

            <div class='column'>
                <select data-for="ww-list" class="player-list-position-select">
                        <option value="0">All</option>
                    <?php foreach ($pos as $p): ?>
                        <option value="<?=$p->id?>"><?=$p->text_id?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <table class="table" >
                    <thead>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="position" class="player-list-a-sort">Position</a></th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="last_name" class="player-list-a-sort">Name</a></th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="club_id" class="player-list-a-sort">NFL Team</a></th>
                        <th class="hide-for-small-only">Wk <?=$this->session->userdata('current_week')?> Opp.</th>
                        <th>Bye</th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="points" class="player-list-a-sort">Points</a></th>
                        <th><Team</th>
                    </thead>
                    <tbody id="ww-list" data-by="points" data-order="desc" data-url="<?=site_url('load_content/ww_player_list')?>">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <ul class="pagination" role="navigation" aria-label="Pagination">
                    <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="ww-list">Previous</a></li>
                </ul>
            </div>
            <div class="column">
                <div class="player-list-total" data-for="ww-list"></div>
                <br class="show-for-small-only">
            </div>
            <div class="column">
                <ul class="pagination" role="navigation" aria-label="Pagination">
                    <li class="pagination-next"><a href="#" class="player-list-next" data-for="ww-list">Next</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif;?>
<script>
    reloadPage();

    $(".cancel-request").on('click',function(){
        var id = $(this).data('id');
        var url = "<?=site_url('myteam/waiverwire/ajax_cancel_request')?>";
        $.post(url,{'id':id},function(data){
            location.reload();
        },'json');
    });

    $("#drop-only").on('click',function(){
        clearSelections();
		$("#drop-modal").foundation('open');
	});

    // Pick up table button click
	$("#ww-list").on("click","button.player-pickup",function(){
        $("#ww-list").data('clear',$(this).data('clear'));
		$("#ww-list").data('playerid',$(this).data('pickup-id'));
		$("#ww-list").data('playername',$(this).data('pickup-name'));
		$(".pickup-text").text("Pickup: "+$(this).data("pickup-name"));
		//showConfirm();
		$("#drop-modal").foundation('open');
	});

    // Drop table button click (old)
    $("#ww-drop-table").on("click","button.drop-player",function(){
        $("#ww-drop-table").data('playerid',$(this).data('drop-id'));
        $("#ww-drop-table").data('playername',$(this).data('drop-name'));
        $(".drop-text").text("Drop: "+$(this).data("drop-name"));
        showConfirm();
    });


    // Post the waivier wire transaction.
	$("#confirm-drop").on('click',function(){
		//drop_id = $("tr.drop-player.active").data("drop-id");
		drop_id = $("#ww-drop-table").data('playerid');
		//pickup_id = $("tr.pickup-player.active").data("pickup-id");
        clear = $("#ww-list").data('clear');
		pickup_id = $("#ww-list").data('playerid');
		drop_name = $("tr.drop-player.active").data("drop-name");
		drop_name = $("#ww-drop-table").data("playername");
		pickup_name = $("tr.pickup-player.active").data("pickup-name");
		pickup_name = $("#ww-list").data("playername");

		url = "<?=site_url('myteam/waiverwire/transaction/execute')?>";
		$.post(url,{'pickup_id' : pickup_id, 'drop_id' : drop_id},function(data)
		{
			var response = jQuery.parseJSON(data);
			//pickupSearch(1,getpos(),getsort(),getsearch());
			$("#confirm-modal").foundation('close');
			reloadPage();
			if (response.success == true)
			{
				if (pickup_name == undefined){pickup_name = "No one";}
                if ((response.status_code == 1) || (response.manual != undefined && response.manual == true))
                {notice("Request submitted, pending approval.<br><br> Drop: "+drop_name+"<br>Add: "+pickup_name);}
                else {notice("Request processed succcessfuly.<br>Dropped: "+drop_name+"<br>Added: "+pickup_name,'success');}
			}
			else
			{
				showMessage(response.error,'alert-error');
                notice(response.error,'warning');
			}
			$("#drop-modal").foundation('close');

		});

	});

    $('.set-preferred').on('click',function(){
        var id = $(this).data('id');
        var url = "<?=site_url('myteam/waiverwire/ajax_make_preferred')?>";
        $.post(url,{'id':id},function(data){
            if (data.success)
            {
               location.reload();
            }
        },'json');

    });

    function showConfirm()
	{
        // Checks to make sure the transaction is OK to go through, if so display confirm modal
		var url="<?=site_url('myteam/waiverwire/transaction')?>";
		drop_id = $("#ww-drop-table").data('playerid');
		pickup_id = $("#ww-list").data('playerid');

		$.post(url,{'pickup_id':pickup_id,'drop_id':drop_id},function(data){
			var d = jQuery.parseJSON(data);
            console.log(d);
			if (d.success == true)
			{
                $("#notclear").addClass('hide');
                $("#confirm-drop").text('Confirm');
                $("#confirm-modal").foundation('open');
            }
			else {
                if (d.status_code == 1)
                {
                    pickup_name = $("#ww-list").data("playername");
                    // $("#notclear").text("You'll be notified when "+pickup_name+"'s waivers clear.");
                    $("#notclear").html("<b>"+d.error+"</b>");
                    $("#notclear").removeClass('hide');
                    $("#confirm-drop").text('Request player');
                    $("#confirm-modal").foundation('open');
                    return;
                }

                notice("Cannot process request:<br> "+d.error,'warning');
				//$("#error-text").text("Error:"+d.error);
				//$("#error-modal").foundation('open');
			}
		});
	}

    function reloadPage()
    {
        $(loadContent('ww-list'));
        url = "<?=site_url('myteam/waiverwire/ajax_drop_table')?>";
        $.post(url,{},function(data){ $("#ww-drop-table").html(data); });

        clearSelections();
    }

    function clearSelections()
    {
        $("#ww-list").data('playerid',0);
		$("#ww-list").data('playername',"No One");
		$(".pickup-text").text("Pick Up: No One");
        $("#notclear").addClass('hide');
        $("#confirm-drop").text('Confirm');
    }

</script>
