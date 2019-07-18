<?php $this->load->view('components/stat_popup'); ?>

<!-- Modals that are hidden by default -->

<?php 
// Confirm modal
$body =     '<div id="notclear" class="hidden" style="padding-bottom:10px;">Players waivers have not cleared, you\'ll be notified when they do.</div>
            <div class="drop-text">Drop: No One</div>
            <div class="pickup-text" style="padding-bottom:10px;">Pick up: No One</div>
            <button class="button is-link is-small" id="confirm-drop">Confirm</button>
            <button class="button is-link is-small" id="cancel-drop">Cancel</button>';

$this->load->view('components/modal', array('id' => 'confirm-modal',
                                                    'title' => 'Confirm',
                                                    'body' => $body,
                                                    'reload_on_close' => True));
?>

<?php 
// Drop modal
$body =     '   <div class="f-scrollbar">
                <table class="table is-bordered table-condensed is-fullwidth is-size-7-mobile" style="min-width:300px">
                <thead>
                    <th colspan=3 class="text-center">Player</th>
                </thead>
                <tbody id="ww-drop-table" data-playerid="0">
                </tbody>
                </table>
                </div>';

$this->load->view('components/modal', array('id' => 'drop-modal',
                                                    'title' => 'Drop Player',
                                                    'body' => $body,
                                                    'reload_on_close' => True));
?>
<!-- End Modals -->


<div class="section">
    <div class="container">
        <div class="columns">
            <div class="column">
                <div class="title">Waiver wire</div>
            </div>
            <div class="column">
                <a href="<?=site_url('myteam/waiverwire/priority')?>">Priority & Rules</a> |
                <a href="<?=site_url('myteam/waiverwire/log')?>">Log</a>
                <?php if(!$this->session->userdata('offseason')): ?>
                    | <a href="#" id="drop-only">Drop Player</a>
                <?php endif;?>   
            </div>
            
        </div>
    </div>
    <div class="is-divider"></div>

    <!-- Show pending waiver wire requests, if any -->
    <?php if(count($pending) > 0): ?>
        <div class="container">
            <h6 class="title is-size-5">Pending Requests</h6>
            <div class="f-scrollbar">
                <table class="table is-fullwidth is-narrow is-size-7-mobile">
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
                                    <button class="button cancel-request is-link is-small" data-id="<?=$a->ww_id?>"><b>Cancel</b> <?=$clear_text?></button>
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
        <div class="is-divider"></div>
    <?php endif;?>


    <?php if($this->session->userdata('offseason')): ?>
        <?php $this->load->view('user/offseason'); ?>
    <?php else: ?>

    <!-- Show the main waiver wire player list for picking up new players -->
    <div class="container">
        <div class="title is-size-5">Request Player</div>

        <?php //Show the player list using player_search_table component

        $headers['Position'] = array('by' => 'position', 'order' => 'asc');
        $headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
        $headers['NFL Team'] = array('by' => 'club_id', 'order' => 'asc');
        $headers['Wk '.$this->session->userdata('current_week').' Opp.'] = array('classes' => array('hide-for-small-only'));
        $headers['Bye'] = array();
        $headers['Points'] = array('by' => 'points', 'order' => 'asc');
        $headers['Team'] = array();

        $pos_dropdown['All'] = 0;
        foreach($pos as $p)
            $pos_dropdown[$p->text_id] = $p->id;

        $this->load->view('components/player_search_table',
                        array('id' => 'ww-list',
                            'url' => site_url('load_content/ww_player_list'),
                            'order' => 'desc',
                            'by' => 'points',
                            'pos_dropdown' => $pos_dropdown,
                            'headers' => $headers));


        ?>

    </div>
    <?php endif;?>

</div>
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
        $("#drop-modal").addClass('is-active');
    });
    
    $("#cancel-drop").on('click', function(){
        $("#confirm-modal").removeClass('is-active');
    })

    // Pick up table button click
	$("#ww-list").on("click","button.player-pickup",function(){
        $("#ww-list").data('clear',$(this).data('clear'));
		$("#ww-list").data('playerid',$(this).data('pickup-id'));
		$("#ww-list").data('playername',$(this).data('pickup-name'));
		$(".pickup-text").text("Pickup: "+$(this).data("pickup-name"));
        //showConfirm();
        $("#drop-modal").addClass('is-active');
//		$("#drop-modal").foundation('open');
	});

    // Drop table button click (old)
    $("#ww-drop-table").on("click","button.drop-player",function(){
        $("#ww-drop-table").data('playerid',$(this).data('drop-id'));
        $("#ww-drop-table").data('playername',$(this).data('drop-name'));
        $(".drop-text").text("Drop: "+$(this).data("drop-name"));
        $("#drop-modal").removeClass('is-active');
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
            console.log(data);
			var response = jQuery.parseJSON(data);
			//pickupSearch(1,getpos(),getsort(),getsearch());
           // $("#confirm-modal").foundation('close');
            $("#confirm-modal").removeClass('is-active');
			reloadPage();
			if (response.success == true)
			{
				if (pickup_name == undefined){pickup_name = "No one";}
                if ((response.status_code == 1) || (response.manual != undefined && response.manual == true))
                {notice("Request submitted, pending approval.<br><br> Drop: "+drop_name+"<br>Add: "+pickup_name);
                }
                else {notice("Request processed succcessfuly.<br>Dropped: "+drop_name+"<br>Added: "+pickup_name,'success');
                }
			}
			else
			{
				//showMessage(response.error,'alert-error');
                notice(response.error,'error');
            }
            $("#drop-modal").removeClass('is-active');
		}).fail(function(){
            notice('A system error occured.','error');
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
                $("#notclear").addClass('is-invisible');
                $("#confirm-drop").text('Confirm');
                $("#confirm-modal").addClass('is-active');
                $("#drop-modal").removeClass('is-active');
            }
			else {
                if (d.status_code == 1)
                {
                    pickup_name = $("#ww-list").data("playername");
                    // $("#notclear").text("You'll be notified when "+pickup_name+"'s waivers clear.");
                    $("#notclear").html("<b>"+d.error+"</b>");
                    $("#notclear").removeClass('hide');
                    $("#confirm-drop").text('Request player');
                    //$("#confirm-modal").foundation('open');
                    $("#confirm-modal").addClass('is-active');
                    return;
                }

                notice("Cannot process request:<br> "+d.error,'warning');
				//$("#error-text").text("Error:"+d.error);
				//$("#error-modal").foundation('open');
			}
		}).fail(function(){
            notice('A system error occured.','error');
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
