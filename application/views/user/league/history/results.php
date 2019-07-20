<style>
.leaguetitle{
    padding-bottom: 20px;
}
</style>
<?php //print_r($other_titles); ?>
<div class="section">
    <div class="container">
        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'results'));?>

        <div class="title"><?=$year?> Season</div>

        <?php if(count($title_games)>0 || count($other_titles) > 0): ?>
        <hr>
        <div class="columns is-multiline">
            <?php foreach($title_games as $title): ?>
            <div class="column is-half-tablet leaguetitle">
                <div class="title is-size-5"><?=$title['data']->title_text?></div>
                <?=$title['team_name']?>
            </div>
            <?php endforeach; ?>
            <?php foreach($other_titles as $other): ?>
                <div class="column is-half-tablet leaguetitle">
                    <b><?=$other->text?></b><br>
                    <?=$other->team_name?>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif;?>
        <hr>
        <div class="title is-size-5">Standings</div>
			<div id="standings-table" class="f-scrollbar is-size-7-mobile">
            </div>
	</div>
</div>


<script>
$( document ).ready(function() {
	load_standings();
});

$("#year-select").on("change",function(){
	load_standings();
});

function load_standings()
{
	var year = $("#year-select").val();
	year = <?=$selected_year?>;
	var url = "<?=site_url('season/standings/ajax_get_standings')?>";
	$.post(url,{'year' : year},function(data){
		$("#standings-table").html(data);
		if (year != 0){$("#year").text(year)};
	});
}

</script>
