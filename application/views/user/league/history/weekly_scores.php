
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

<?php if ($selected_week == 0): ?>
        <div class='is-size-6'></div>
<?php else:?>
    <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'scores'));?>

    <div class='is-size-4'>Week <?=$selected_week?></div>
    <br>
    <table class="table table-narrow is-fullwidth">
        <tr>
        <?php foreach($weeks as $num => $w): ?>
            <?php if($selected_week == $w->week):?>
                <td class="week-selected has-text-centered">
                    <?=$w->week?>
                </td>
            <?php elseif($w->week <= $this->session->userdata('current_week') || $selected_year < $this->session->userdata['current_year']):?>
                <td class="week-link has-text-centered">
                    <a href="<?=site_url('league/history/scores/'.$selected_year.'/'.$w->week)?>"><?=$w->week?></a>
                </td>
            <?php else:?>
                <td class="week-future has-text-centered">
                    <?=$w->week?>
                </td>
            <?php endif;?>
        <?php endforeach;?>
        </tr>

    </table>

<?php endif; // If selected week is > 0?>
<br>
    <div class="columns is-multiline">
        <?php foreach($matchups as $m): ?>
            <div class="column is-half-tablet">
                <table class="table table-narrow is-fullwidth fflp-table-fixed is-striped">
                    <thead>
                        <th height="55px"><?=$m['home_team']['points']?></th>
                        <th class="text-right" style="width:40%"><a href="<?=site_url('league/teams/view/'.$m['home_team']['team']->id)?>"><?=$m['home_team']['team']->team_name?></a></th>
                        <th class="text-center"></th>
                        <th style="width:40%"><a href="<?=site_url('league/teams/view/'.$m['away_team']['team']->id)?>"><?=$m['away_team']['team']->team_name?></a></th>
                        <th class="text-right"><?=$m['away_team']['points']?></th>
                    </thead>
                    <tbody>
                        <?php foreach($m['home_team']['starters'] as $key=>$p): ?>
                            <?php $hp = $m['home_team']['starters'][$key]; ?>
                            <?php $ap = $ap = isset($m['away_team']['starters'][$key]) ? $m['away_team']['starters'][$key] : false; ?>
                            <tr>
                                <?php if ($hp['player']): ?>
                                    <td><?=$hp['player']->points?></td>
                                    <td class="text-right">
                                        <a href="#" class="stat-popup" data-type="player" data-id="<?=$hp['player']->player_id?>"><?=$hp['player']->short_name?></a>
                                    </td>
                                <?php else: ?>
                                    <td></td><td></td>
                                <?php endif;?>

                                <td class="text-center"><strong><?=$hp['pos_text']?></strong></td>

                                <?php if($ap['player']): ?>
                                <td>
                                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$ap['player']->player_id?>"><?=$ap['player']->short_name?></a>
                                </td>
                                <td class="text-right"><?=$ap['player']->points?></td>
                                <?php else: ?>
                                    <td></td><td></td>
                                <?php endif; ?>

                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <hr>
            </div>
        <?php endforeach;?>
    </div>
</div>
