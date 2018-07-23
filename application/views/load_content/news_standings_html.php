
<table class="table is-hoverable is-fullwidth is-narrow fflp-table-min-mobile">
    <thead>
                <th>Team</th><th>Win %</th><th>Record</th><th>Points <span style="font-size:.8em">(ppg)</span></th><th>Opp Points <span style="font-size:.8em">(ppg)</span></th>
    </thead>
<?php foreach ($divs as $key => $d):?>
    <tbody>
        <?php if (isset($d['name'])): ?>
            <tr><td colspan=5 style="background-color:#DDDDDD;"><strong><?=$d['name']?></strong></td></tr>
        <?php endif;?>
        <?php foreach ($d['standings'] as $t): ?>
            <?php if ($t->total_games == 0)
                {
                    $winptc=.000;
                    $avgpts=0;
                    $avgopp=0;
                }
                else{
                    $winptc = ($t->wins+($t->ties/2)) / $t->total_games;
                    $avgpts = round($t->points/$t->total_games,1);
                    $avgopp = round($t->opp_points/$t->total_games,1);
                }?>
            <tr>
                <td><?=isset($t->notation_symbol) ? $t->notation_symbol.' ' : ''?><a href="<?=site_url('league/teams/view/'.$t->team_id)?>"><?=$t->team_name?></a></td>
                <td><?=str_replace('0.','.',number_format($winptc,3))?></td>
                <td><?=$t->wins?>-<?=$t->losses?>-<?=$t->ties?></td>
                <td><?=$t->points?> <span style="font-size:.8em">(<?=$avgpts?>)</span></td>
                <td><?=$t->opp_points?> <span style="font-size:.8em">(<?=$avgopp?>)</span></td>
            </tr>
        <?php endforeach;?>
    </tbody>
<?php endforeach;?>
</table>
<?php foreach($defs as $def): ?>
    <?=$def->symbol.' - '.$def->text?><br>
<?php endforeach;?>