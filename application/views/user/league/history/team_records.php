<div class="row callout">
    <div class="columns small-12" style="max-width:200px">
        <select id="selected-record">
            <option value="record">Record</option>
            <option value="best-week">Best Week</option>
            <option value="worst-week">Worst Week</option>
            <option value="titles">Titles</option>
        </select>
    </div>
</div>

<div class="row callout">
    <div id="record-div" class="columns small-12 hide stat-div">
        <div class="row">
            <div class="columns column small-12 medium-4">
                <select class="form-control player-list-year-select" data-for="record">
                    <option value="0">All Years</option>
                    <?php foreach($years as $y): ?>
                        <option value="<?=$y->year?>"><?=$y->year?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>

        <table class="table table-condensed table-striped">
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