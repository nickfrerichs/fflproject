<?php $this->load->view('template/modals/stat_popup'); ?>

<!-- Modals that are hidden by default -->

<!-- Confirm modal -->
<div class="reveal" id="confirm-modal" data-reveal data-overlay="true">
    <div class="text-center">
            <div class="drop-text">Drop: No One</div>
            <div class="pickup-text">Pick up: No One</div>
        <button class="button" type="button" id="confirm-drop">
            Confirm
        </button>
        <button class="button" type-"button" id="cancel-drop" data-close aria-label="Close modal">
            Cancel
        </button>
    </div>
</div>

<!-- Drop modal -->
<div class="reveal" id="drop-modal" data-reveal data-overlay="true">
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
</div>
<!-- End Modals -->

<div class="row align-middle align-justify small-unstack callout">
    <div class="columns small-12 medium-4 text-center">
        <h5>Waiver wire</h5>
    </div>
    <div class="columns small-12 medium-8 text-center">
        <a href="<?=site_url('myteam/waiverwire/priority')?>">Waiver Wire Priority</a> |
        <a href="<?=site_url('myteam/waiverwire/log')?>">Waiver Wire Log</a>
        <?php if(!$this->session->userdata('offseason')): ?>
            | <a href="#" id="drop-only">Drop Player</a>
        <?php endif;?>

    </div>
</div>


<?php if($this->session->userdata('offseason')): ?>
    <?php $this->load->view('user/offseason'); ?>
<?php else: ?>

<!-- Search options -->
<div class="row callout">
    <div class="columns">
        <div class="row align-center">
            <div class="search-group columns small-12 medium-8">
                <input type="text" class="player-list-text-input" data-for="ww-list" placeholder="Search">
            </div>

            <div class='sort-group columns small-12 medium-4'>
                <select data-for="ww-list" class="player-list-position-select">
                        <option value="0">All</option>
                    <?php foreach ($pos as $p): ?>
                        <option value="<?=$p->id?>"><?=$p->text_id?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="columns">
                <table class="table table-condensed" >
                    <thead>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="position" class="player-list-a-sort">Position</a></th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="last_name" class="player-list-a-sort">Name</a></th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="club_id" class="player-list-a-sort">NFL Team</a></th>
                        <th class="hide-for-small-only">Wk <?=$this->session->userdata('current_week')?> Opp.</th>
                        <th><a href="#" data-order="asc" data-for="ww-list" data-by="points" class="player-list-a-sort">Points</a></th>
                        <th><Team</th>
                    </thead>
                    <tbody id="ww-list" data-by="points" data-order="desc" data-url="<?=site_url('player_search/ajax_ww_player_list')?>">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row align-center">
            <div class="columns text-right">
                <ul class="pagination" role="navigation" aria-label="Pagination">
                    <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="ww-list">Previous</a></li>
                </ul>
            </div>
            <div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
                <div class="player-list-total" data-for="ww-list"></div>
                <br class="show-for-small-only">
            </div>
            <div class="columns text-left small-order-2 medium-order-3">
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



    $("#drop-only").on('click',function(){
        clearSelections();
		$("#drop-modal").foundation('open');
	});

    // Pick up table button click
	$("#ww-list").on("click","button.player-pickup",function(){
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
                notice("Request processed succcessfuly.<br>Dropped: "+drop_name+"<br>Added: "+pickup_name,'success');

			}
			else
			{
				showMessage(response.error,'alert-error');
                notice(response.error,'warning');
			}
			$("#drop-modal").foundation('close');

		});

	});

    function showConfirm()
	{
        // Checks to make sure the transaction is OK to go through, if so display confirm modal
		var url="<?=site_url('myteam/waiverwire/transaction')?>";
		drop_id = $("#ww-drop-table").data('playerid');
		pickup_id = $("#ww-list").data('playerid');

		$.post(url,{'pickup_id':pickup_id,'drop_id':drop_id},function(data){
            console.log(data);
			var d = jQuery.parseJSON(data);
			if (d.success == true)
			{$("#confirm-modal").foundation('open');}
			else {
				console.log(d.error);
                notice("Cannot process request:<br> "+d.error,'warning');
				//$("#error-text").text("Error:"+d.error);
				//$("#error-modal").foundation('open');
			}
		});
	}

    function reloadPage()
    {
        $(updatePlayerList("ww-list"));
        url = "<?=site_url('myteam/waiverwire/ajax_drop_table')?>";
        $.post(url,{},function(data){ $("#ww-drop-table").html(data); });

        clearSelections();
    }

    function clearSelections()
    {
        $("#ww-list").data('playerid',0);
		$("#ww-list").data('playername',"No One");
		$(".pickup-text").text("Pick Up: No One");
    }

</script>
