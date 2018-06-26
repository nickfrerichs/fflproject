<div class="section">
    <div class="select">
        <select id="selected-record">
            <option value="record">Record</option>
            <option value="best-week">Best Week</option>
            <option value="worst-week">Worst Week</option>
            <option value="titles">Titles</option>
        </select>
    </div>


    <div id="record-div" class="stat-div">
        <div class="select">
            <select class="form-control player-list-year-select" data-for="record">
                <option value="0">All Years</option>
                <?php foreach($years as $y): ?>
                    <option value="<?=$y->year?>"><?=$y->year?></option>
                <?php endforeach;?>
            </select>
        </div>

        <table class="table table-narrow is-fullwidth table-striped">
            <thead>
                <th></th><th>Owner/Team</th><th>W Pct</th><th>Avg / Opp</th><th>W  L  T</th><th>Pts</th><th>Opp Pts</th>
            </thead>
            <tbody id="record" data-url="<?=site_url('player_search/ajax_team_history_record')?>">
            </tbody>
        </table>
    </div>

</div>


<script>
//$(updatePlayerList("best-week"));

$('#selected-record').on('change',function(){
	load_stat_div();
});

function load_stat_div()
{
	var stat = $('#selected-record').val();
	$('#'+stat+'-div').removeClass('hide');
	$('.stat-div:not(#'+stat+'-div)').addClass('hide');
	$(updatePlayerList(stat));
}

load_stat_div();
</script>