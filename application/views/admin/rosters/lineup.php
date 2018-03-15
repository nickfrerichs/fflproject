<!-- Start other modal -->
<!-- <div class="reveal large" id="start-other-modal" data-reveal data-overlay="true" data-multiple-opened-"true">
    <div>
        <div>
            <div class="text-center">
                <h5>Start a player for: <?=$team_name?></h5>
                <h5 id="start-other-week"></h5>
            </div>

            <div class="row align-center">
                <div class="columns small-8">
                    <input type="text" class="player-list-text-input" data-for="main-list" placeholder="Player search">
                    <select data-for="main-list" class="player-list-position-select">
                        <option value="0">All</option>
                        <?php foreach($positions as $pos): ?>
                            <option value="<?=$pos->id?>"><?=$pos->text_id?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="columns">
                    <table class="table-condensed" >
                        <thead>
                            <th><a href="#" data-order="asc" data-for="main-list" data-by="last_name" class="player-list-a-sort">Name</a></th>
                            <th><a href="#" data-order="asc" data-for="main-list" data-by="position" class="player-list-a-sort">Position</a></th>
                            <th><a href="#" data-order="asc" data-for="main-list" data-by="club_id" class="player-list-a-sort">NFL Team</a></th>
                            <th></th>
                        </thead>
                        <tbody id="main-list" data-by="points" data-order="desc" data-url="<?=site_url('player_search/ajax_admin_start_get_player_list')?>">

                        </tbody>
                    </table>

                    <div class="row align-center">
                        <div class="columns text-right">
                            <ul class="pagination" role="navigation" aria-label="Pagination">
                                <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="main-list">Previous</a></li>
                            </ul>
                        </div>
                        <div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
                            <div class="player-list-total" data-for="main-list"></div>
                            <br class="show-for-small-only">
                        </div>
                        <div class="columns text-left small-order-2 medium-order-3">
                            <ul class="pagination" role="navigation" aria-label="Pagination">
                                <li class="pagination-next"><a href="#" class="player-list-next" data-for="main-list">Next</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <button class="close-button" data-close aria-label="Close modal" type="button">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div> -->

<div class="section">
    <b>Note:</b>No checks are done to determing roster/starter limits, if that player is already started on another team, or if the player is owned.
    Make sure you know what you are doing.<br><br>If it's a past year/week, you'll need to recalculate statistics.

    <h5 class="text-center">Edit Lineup</h5>
    <div class="columns is-centered">

        <div class="column fflp-med-container">
            Year:
            <select id="lineup-year-select">
                <?php foreach($lineup_years as $y): ?>
                    <option><?=$y->year?></option>
                <?php endforeach;?>
            </select>
        </div>
        <div class="column fflp-med-container">
            Week:
            <select id="lineup-week-select">

            </select>
        </div>
    </div>

</div>

<div class="section">
    <div class="columns is-centered">
        <div class="column">
            <h5>Starters</h5>
            <table class="table">
                <thead>
                    <th style="width:60%">Player</th>
                    <th style="width:20%" class="text-center">Starting Pos.</th>
                    <th style="width:20%;" class="text-center">Sit</th>
                </thead>
                <tbody id="lineup">
                </tbody>
            </table>
            <h5>Bench</h5>
            <table class="table">
                <thead>
                    <th style="width:80%">Player</th>
                    <th style="width:20%;" class="text-center">Start As</th>
                </thead>
                <tbody id="bench">
                </tbody>
            </table>

            <div>
                <a href="#" data-open="start-other-modal">Start Another player</a>
            </div>
        </div>

</div>


<script>

updateLineupWeeks();
setTimeout(function(){updateLineup();},'100');

$("#lineup-week-select, #lineup-year-select").on('change',function(){
    updateLineup();
})

$("#start-other-modal").on("open.zf.reveal",function(){
    $("#start-other-week").text($("#lineup-year-select").val()+" - Week "+$("#lineup-week-select").val());
    $(updatePlayerList("main-list"));
})

$("#choose-pos-modal").on("open.zf.reveal",function(){
    var url = "<?=site_url('admin/rosters/ajax_get_league_positions')?>";
    var year = $("#lineup-year-select").val();
    $.post(url,{'year':year},function(data){
        $("#start-positions").html(data);
    });
})

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
            $("#start-other-modal").foundation('close');
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
