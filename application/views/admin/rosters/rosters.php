
<?php //$this->load->view('components/stat_popup'); ?>

<?php //Body of the add-player-modal, it's a listing of players

$headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
$headers['Position'] = array('by' => 'position', 'order' => 'asc');
$headers['NFL Team'] = array('by' => 'club_id', 'order' => 'asc');
//$headers['Wk '.$this->session->userdata('current_week').' Opp.'] = array('classes' => array('hide-for-small-only'));
//$headers['Points'] = array('by' => 'points', 'order' => 'asc');
$headers[''] = array();

$pos_dropdown['All'] = 0;
foreach($positions as $p)
    $pos_dropdown[$p->text_id] = $p->id;
$body = "<div>";
$body .= $this->load->view('components/player_search_table',
                array('id' => 'admin-player-add-list',
                      'url' => site_url('load_content/admin_rosters_player_search'),
                      'order' => 'desc',
                      'by' => 'points',
                      'pos_dropdown' => $pos_dropdown,
                      'headers' => $headers),True);
$body .= "</body>";

?>


<?php 
//     // League admins modal

    $this->load->view('components/modal', array('id' => 'add-player-modal',
                                                          'title' => 'Add Player',
                                                          'body' => $body,
                                                         'reload_on_close' => True));
?>


<!-- End Modals -->

<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-med-container">
        <h5><?=$team_name?></h5>

        <div id='teamlist'>
            <?php //print_r($roster); ?>
            <a id="add-player-button" href="#">Add player </a><br>
            <a href="<?=site_url('admin/rosters/lineup/'.$teamid)?>">Edit Starting Lineup</a><br><br>
            <h6>Current Roster</h6>
            <table class="table is-fullwidth fflp-table-fixed">
                <thead>
                    <th>Player</th><th>Team</th><th>Position</th><th></th>
                </thead>
                <?php foreach ($roster as $player): ?>
                <tr>
                    <td><?php echo $player->short_name; ?></td>
                    <td><?php echo $player->club_id; ?></td>
                    <td><?php echo $player->position; ?></td>
                    <td>
                        <a href='<?php echo site_url('admin/rosters/removeplayer/'.$teamid.'/'.$player->player_id); ?>'>remove</a>
                    </td>
                </tr>


                <?php endforeach; 
                ?>
            </table>
            </div>
        </div>
    </div>
</div>

<script>

    // Show modal and load list of admins
    $("#add-player-button").on('click',function(){
        $(loadContent("admin-player-add-list"));
        $("#add-player-modal").addClass('is-active');
    });


    $("#add-modal").on("open.zf.reveal",function(){
        $(updatePlayerList("main-list"));
    })

    $("#admin-player-add-list").on("click",".add-button",function(){
        var url = "<?=site_url('admin/rosters/ajax_addplayer')?>";
        console.log("<?=$teamid?>");
        var teamid = "<?=$teamid?>";
        var playerid = $(this).data('id');
        var playername = $(this).data('name');
        $.post(url,{'teamid':teamid,'playerid':playerid},function(data){
            var d = jQuery.parseJSON(data);
			if (d.success == true)
            {
                $(loadContent("admin-player-add-list"));
            }
        });
    });

    $("#add-modal").on("closed.zf.reveal",function(){
        location.reload();
    });

</script>
