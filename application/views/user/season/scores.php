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

                        <?=$w->week?>

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
    <?php foreach($matchups as $m): ?>

        <div class="columns is-mobile is-multiline is-size-7-mobile is-size-6-desktop has-background-link has-text-white">
            <div class="column is-6-desktop is-12-mobile is-12-tablet <?php if($m['home_team']['points'] > $m['away_team']['points']){echo "has-text-weight-bold";}?>">
                <div class="columns is-mobile">
                    <div class="column is-9">
                        <a class="has-text-white" href="<?=site_url('league/teams/view/'.$m['home_team']['team']->id)?>"><?=$m['home_team']['team']->team_name?></a>
                    </div>
                    <div class="column is-3">
                        <?=$m['home_team']['points']?>
                    </div>
                </div>
            </div>
            <div class="column is-6-desktop is-12-mobile is-12-tablet <?php if($m['away_team']['points'] > $m['home_team']['points']){echo "has-text-weight-bold";}?>">
                <div class="columns is-mobile">
                    <div class="column is-9">
                        <a class="has-text-white" href="<?=site_url('league/teams/view/'.$m['away_team']['team']->id)?>"><?=$m['away_team']['team']->team_name?></a>
                    </div>
                    <div class="column is-3">
                        <?=$m['away_team']['points']?>
                    </div>
                </div>
            </div>
        </div>


        <?php foreach($m['home_team']['starters'] as $key=>$p): ?>
                <?php $hp = $m['home_team']['starters'][$key]; ?>
                <?php $ap = $ap = isset($m['away_team']['starters'][$key]) ? $m['away_team']['starters'][$key] : false; ?>

                <div class="columns is-mobile is-size-7 is-multiline">
                    <div class="column is-6-desktop is-12-mobile is-12-tablet">
                        <?php if ($hp['player']): ?>
                            <div class="columns is-mobile">
                                <div class="column is-2">
                                    <?=$hp['pos_text']?>
                                </div>
                                <div class="column is-7">
                                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$hp['player']->player_id?>"><?=$hp['player']->short_name?></a>
                                </div>
                                <div class="column is-3">
                                    <?=$hp['player']->points?>
                                </div>
                            </div>


                        <?php else: ?>
                        <div class="columns is-mobile">
                            <div class="column is-2">
                                <?=$p['pos_text']?>
                            </div>
                            <div class="column is-7">
                            <i>Vacant</i>
                            </div>
                            <div class="column is-3">
                                -
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <div class="column is-6-desktop is-12-mobile is-12-tablet">
                        <?php if ($ap['player']): ?>
                            <div class="columns is-mobile">
                                <div class="column is-2">
                                    <?=$ap['pos_text']?>
                                </div>
                                <div class="column is-7">
                                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$ap['player']->player_id?>"><?=$ap['player']->short_name?></a>
                                </div>
                                <div class="column is-3">
                                    <?=$ap['player']->points?>
                                </div>
                            </div>


                        <?php else: ?>
                        <div class="columns is-mobile">
                            <div class="column is-2">
                                <?=$p['pos_text']?>
                            </div>
                            <div class="column is-7">
                                <i>Vacant</i>
                            </div>
                            <div class="column is-3">
                                -
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                    <hr>
                </div>

 
        <?php endforeach;?>
        <hr>
    <?php endforeach;?>


<!--

        <div class="columns is-multiline ">
            <?php foreach($matchups as $m): ?>
                <div class="column is-half-desktop is-12">
                    <div class="f-scrollbar">
                        <table class="table is-fullwidth is-bordered is-striped f-table-fixed is-size-7" style="min-width: 450px;">
                            <thead>
                                <th height="55px"><?=$m['home_team']['points']?></th>
                                <th class="has-text-right" style="width:40%"><a href="<?=site_url('league/teams/view/'.$m['home_team']['team']->id)?>"><?=$m['home_team']['team']->team_name?></a></th>
                                <th class="has-text-center"></th>
                                <th style="width:40%"><a href="<?=site_url('league/teams/view/'.$m['away_team']['team']->id)?>"><?=$m['away_team']['team']->team_name?></a></th>
                                <th class="has-text-right"><?=$m['away_team']['points']?></th>
                            </thead>
                            <tbody>
                                <?php foreach($m['home_team']['starters'] as $key=>$p): ?>
                                    <?php $hp = $m['home_team']['starters'][$key]; ?>
                                    <?php $ap = $ap = isset($m['away_team']['starters'][$key]) ? $m['away_team']['starters'][$key] : false; ?>
                                    <tr>
                                        <?php if ($hp['player']): ?>
                                            <td><?=$hp['player']->points?></td>
                                            <td class="has-text-right">
                                                <a href="#" class="stat-popup" data-type="player" data-id="<?=$hp['player']->player_id?>"><?=$hp['player']->short_name?></a>
                                            </td>
                                        <?php else: ?>
                                            <td></td><td></td>
                                        <?php endif;?>

                                        <td class="has-text-center"><strong><?=$hp['pos_text']?></strong></td>

                                        <?php if($ap['player']): ?>
                                        <td>
                                            <a href="#" class="stat-popup" data-type="player" data-id="<?=$ap['player']->player_id?>"><?=$ap['player']->short_name?></a>
                                        </td>
                                        <td class="has-text-right"><?=$ap['player']->points?></td>
                                        <?php else: ?>
                                            <td></td><td></td>
                                        <?php endif; ?>

                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                    <hr>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div>
-->
<script>
$("#mobile-week-selector").on("change",function(){
    var url = "<?=site_url('season/scores/week/')?>"+$(this).val();
    document.location.href=url;
});
</script>