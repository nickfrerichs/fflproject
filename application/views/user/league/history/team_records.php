<div class="section"> 
    <div class="container">
        <div class="tabs is-small is-boxed fflp-tabs-active">
            <ul>
                <li class="is-active" data-for="record-tab" data-load-content="record-content"><a>Record</a></li>
                <!-- <li class="" data-for="best-week-tab" data-load-content="best-week-content"><a>Best Week</a></li>
                <li class="" data-for="worst-week-tab" data-load-content="worst-week-content"><a>Worst Week</a></li>
                <li class="" data-for="titles-tab" data-load-content="titles-content"><a>Titles</a></li> -->
            </ul>
        </div>


        <div id="record-tab">
            <div class="select">
                <select class="pagination-filter" data-filter="year" data-for="record-content">
                    <option value="0">All Years</option>
                    <?php foreach($years as $y): ?>
                        <option value="<?=$y->year?>"><?=$y->year?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="f-scrollbar">
                <table class="table table-narrow is-fullwidth table-striped f-min-width-medium is-size-7-mobile">
                    <thead>
                        <th></th><th>Owner/Team</th><th>W Pct</th><th>Avg / Opp</th><th>W  L  T</th><th>Pts</th><th>Opp Pts</th>
                    </thead>
                    <tbody  id="record-content" 
                            data-url="<?=site_url('load_content/history_team_record')?>"
                            data-per-page="10">
                    </tbody>
                </table>
            </div>
            <?php $this->load->view('load_content/template/load_more_buttons',array('for' => 'record-content'));?>
        </div>
    </div>
</div>

<script>
//$(updatePlayerList("best-week"));
loadContent('record-content');
</script>