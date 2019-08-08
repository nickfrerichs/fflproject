<?php //print_r($matchups)?>
<?php $this->load->view('components/stat_popup'); ?>
<style>

.week-selected{

    background: #DDD;
}
.week-link{
    background: #FAFAFA;
}

.week-link a{
    display:block;
    text-decoration:none;
}

.week-future{
    color: #999;
}

</style>
<div class="section">
    <div class="container">

    <!-- The week selector -->
    <?php if ($selected_week == 0): ?>
        <div class='is-size-6'></div>
    <?php else:?>

        <div class='title'>Week <?=$selected_week?> Scores</div>
        <hr>

        <div class="columns is-multiline is-mobile is-size-7 is-hidden-mobile">
            <?php foreach($weeks as $num => $w): ?>
            <div class="column is-1-desktop is-2-tablet">
                <?php if($selected_week == $w->week):?>
                  <div class="has-background-link has-text-centered has-text-light">
                        Week <?=$w->week?>
                    </div>

                <?php elseif($w->week <= $this->session->userdata('current_week') && $selected_year <= $this->session->userdata['current_year']):?>
                    
                        <a href="<?=site_url('season/scores/week/'.$w->week)?>">
                        <div class="has-text-centered has-background-white-ter">Week <?=$w->week?></div></a>

                <?php else:?>

                <a href="<?=site_url('season/scores/week/'.$w->week)?>">
                        <div class="has-text-centered has-background-white">Week <?=$w->week?></div></a>

                <?php endif;?>
            </div>
            <?php endforeach;?>
        </div>
        <div class="select is-hidden-tablet">
            <select id="mobile-week-selector">
            <?php foreach($weeks as $num => $w): ?>
                <option value="<?=$w->week?>" <?php if($selected_week == $w->week){echo "selected";}?>>Week <?=$w->week?></option>
            <?php endforeach;?>
            </select>
        </div>
        <hr>
    <?php endif; // If selected week is > 0?>


    <!-- Print out all the matchups -->
    <?php foreach($matchups as $matchup_id => $m): ?>
        <div class="columns is-size-7-touch is-size-6-desktop is-multiline"> <!-- one matchup -->
            <div class="column is-12-mobile is-6-tablet is-6-desktop"> <!-- home team -->
                <div class="columns is-mobile roster-link has-background-link has-text-white <?php if($m['home_team']['points'] > $m['away_team']['points']){echo "has-text-weight-bold";}?>"
                            data-class=".roster-content<?=$m['home_team']['team']->id?>" style="cursor:pointer"> <!-- Header -->
                    <div class="column is-9">
                        <a class="has-text-white is-hidden-touch" href="<?=site_url('league/teams/view/'.$m['home_team']['team']->id)?>"><?=$m['home_team']['team']->team_name?></a>
                        <span class="is-hidden-desktop"><?=$m['home_team']['team']->team_name?></span>
                    </div>
                    <div class="column is-3">
                        <?=$m['home_team']['points']?>
                    </div>
                </div>
                <?php $this->load->view('user/season/scores/display_roster',array('team' => $m['home_team'], 'matchup_id' => $matchup_id)); ?>

            </div>
            <div class="column is-12-mobile is-6-tablet is-6-desktop"> <!-- away team -->
                <div class="columns is-mobile roster-link has-background-link has-text-white <?php if($m['away_team']['points'] > $m['home_team']['points']){echo "has-text-weight-bold";}?>"
                data-class=".roster-content<?=$m['away_team']['team']->id?>" style="cursor:pointer"> <!-- Header -->
                    <div class="column is-9">
                        
                        <a class="has-text-white is-hidden-touch" href="<?=site_url('league/teams/view/'.$m['away_team']['team']->id)?>"><?=$m['away_team']['team']->team_name?></a>
                        <span class="is-hidden-desktop"><?=$m['away_team']['team']->team_name?></span>
                    </div>
                    <div class="column is-3">
                        <?=$m['away_team']['points']?>
                    </div>
                </div>
                <?php $this->load->view('user/season/scores/display_roster',array('team' => $m['away_team'], 'matchup_id' => $matchup_id)); ?>
            </div>
        </div>
        <hr>
        <br>
    <?php endforeach;?>


<script>
$("#mobile-week-selector").on("change",function(){
    var url = "<?=site_url('season/scores/week/')?>"+$(this).val();
    document.location.href=url;
});

$(".bench-link").on('click',function(){

    $($(this).data('class')).toggleClass('is-hidden');
    $(this).find('.bench-expand-icon').toggleClass('is-hidden');

});

$(".roster-link").on('click',function(){
    if (window.matchMedia("(max-width: 768px)").matches)
    {$($(this).data('class')).toggleClass('is-hidden-mobile');}
});
</script>