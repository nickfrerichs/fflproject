<!-- Start other modal -->

<?php //Body of the add-player-modal, it's a listing of players

$headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
$headers['Position'] = array('by' => 'position', 'order' => 'asc');
$headers['NFL Team'] = array('by' => 'club_id', 'order' => 'asc');
//$headers['Wk '.$this->session->userdata('current_week').' Opp.'] = array('classes' => array('hide-for-small-only'));
//$headers['Points'] = array('by' => 'points', 'order' => 'asc');
$headers['Team'] = array();

$pos_dropdown['All'] = 0;
foreach($positions as $p)
    $pos_dropdown[$p->text_id] = $p->id;

$body = $this->load->view('components/player_search_table',
                array('id' => 'admin-lineup-player-list',
                      'url' => site_url('load_content/admin_lineup_player_search'),
                      'order' => 'desc',
                      'by' => 'points',
                      'pos_dropdown' => $pos_dropdown,
                      'headers' => $headers),True);




?>

<?php 
//     // League admins modal

    $this->load->view('components/modal', array('id' => 'start-player-modal',
                                                          'title' => 'Start Player (<span id="start-other-week"></span>)',
                                                          'body' => $body,
                                                         'reload_on_close' => True));
?>


<div class="section">
    <div class="container fflp-med-container">
        <div class="columns is-centered">
            <div class="column">
                <div class="is-size-5"><?=$team_name?></div>

                <div class="notification">
                    <b>Note: </b>No checks are done to determing roster/starter limits, if that player is already started on another team, or if the player is owned.
                    Make sure you know what you are doing.<br>
                    <br>If it's a past year/week, you'll need to recalculate statistics.
                </div>
            </div>
        </div>
    </div>
    <div class="container fflp-med-container">
        <div class="columns is-centered">
            <div class="column">
                <div class="field">
                    <div class="label">Year:</div>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="lineup-year-select">
                                <?php foreach($lineup_years as $y): ?>
                                    <option><?=$y->year?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="column">
                <div class="field">
                    <div class="label">Week:</div>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="lineup-week-select">

                            </select>
                        </div>
                    </div>  
                </div>
            </div>
        </div>

        <div class="columns is-centered">
            <div class="column">
                <h5>Starters</h5>
                <table class="table is-fullwidth is-narrow is-striped is-hoverable is-bordered fflp-table-fixed">
                    <thead>
                        <th style="width:60%">Player</th>
                        <th style="width:20%" class="text-center">Starting Pos.</th>
                        <th style="width:20%;" class="text-center">Sit</th>
                    </thead>
                    <tbody id="lineup">
                    </tbody>
                </table>
                <h5>Bench</h5>
                <table class="table is-fullwidth is-narrow is-striped is-hoverable is-bordered fflp-table-fixed">
                    <thead>
                        <th style="width:80%">Player</th>
                        <th style="width:20%;" class="text-center">Start As</th>
                    </thead>
                    <tbody id="bench">
                    </tbody>
                </table>

                <div>
                    <a href="#" id="start-other-player-button">Start Another player</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

updateLineupWeeks();
setTimeout(function(){updateLineup();},'100');

$("#lineup-week-select, #lineup-year-select").on('change',function(){
    updateLineup();
})

$("#choose-pos-modal").on("open.zf.reveal",function(){
    var url = "<?=site_url('admin/rosters/ajax_get_league_positions')?>";
    var year = $("#lineup-year-select").val();
    $.post(url,{'year':year},function(data){
        $("#start-positions").html(data);
    });
})

$('#start-other-player-button').on('click',function(){
    $("#start-other-week").text("Year "+$("#lineup-year-select").val()+" - Week "+$("#lineup-week-select").val());
    $(loadContent("admin-lineup-player-list"));
    $("#start-player-modal").addClass('is-active');
});

$(document).on('click','.admin-sit-button',function(){
    var url = "<?=site_url('admin/rosters/ajax_sit_player')?>";
    var year = $("#lineup-year-select").val();
    var week = $("#lineup-week-select").val();
    var playerid = $(this).data('id');

    $.post(url,{'teamid':<?=$teamid?>, 'playerid' : playerid, 'week' : week, 'year' : year}, function(data){
        if(data.success)
        {
            updateLineup();
        }
    },'json');
})

$(document).on('click','.admin-start-button',function(){
    var url = "<?=site_url('admin/rosters/ajax_start_player')?>";
    var year = $("#lineup-year-select").val();
    var week = $("#lineup-week-select").val();
    var playerid = $(this).data('id');
    var posid = $(this).data('posid');

    $.post(url,{'teamid':<?=$teamid?>, 'playerid':playerid, 'week':week, 'year':year, 'posid':posid}, function(data){
        if(data.success)
        {
//            $("#start-other-modal").foundation('close');
            updateLineup();
        }
    },'json');
})


function updateLineupWeeks()
{
    var url ="<?=site_url('admin/rosters/ajax_get_lineup_weeks')?>";
    var year = $("#lineup-year-select").val();
    $.post(url,{'teamid':<?=$teamid?>, 'year' : year},function(data){
        var html = "";
        var current_week = <?=$this->session->userdata('current_week')?>;
        $.each(data.weeks, function(i,week){
            if (week == current_week){html += "<option selected>"+week+"</option>";}
            else{html += "<option>"+week+"</option>"};
        })
        $("#lineup-week-select").html(html);
    },'json');

}

function updateLineup()
{
    var url = "<?=site_url('admin/rosters/ajax_get_lineup')?>";
    var year = $("#lineup-year-select").val();
    var week = $("#lineup-week-select").val();
    $.post(url, {'year': year, 'week': week, 'teamid': <?=$teamid?>},function(data){
        $("#lineup").html(data);
    });

    url = "<?=site_url('admin/rosters/ajax_get_bench')?>";
    $.post(url, {'year': year, 'week': week, 'teamid': <?=$teamid?>},function(data){
        $("#bench").html(data);
    });
}
</script>
