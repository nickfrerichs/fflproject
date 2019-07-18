<?php if (count($totals) == 0 && count($list) == 0): ?>
    No money has been awarded this season.
<?php else: ?>

    <div class="title is-size-5">Totals</div>
    <div class="f-scrollbar">
        <table class="table is-fullwidth is-striped is-size-7-mobile">
            <thead>

            </thead>
            <tbody>
                <?php foreach($totals as $t): ?>
                    <tr>
                        <td><?=$t->team_name?></td>
                        <td>$<?=number_format($t->total,2)?></td>
                        <td style="font-size:.9em;"><?=$t->first_name.' '.$t->last_name?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="is-divider"></div>
    <div class="title is-size-5">Log</div>
    
    <div class="f-scrollbar">
        <table class="table is-fullwidth is-striped is-size-7-mobile">
            <thead>
                <th>Week</th><th>Amount</th><th>Score</th><th>Desc.</th><th>Team</th><th>Owner</th>
            </thead>
            <tbody>
                <?php foreach($list as $l): ?>
                    <tr>
                        <td><?=$l->week?></td>
                        <td>$<?=number_format($l->amount,2)?></td>
                        <td><?=$l->team_score?></td>
                        <?php if($l->text != ""):?>
                            <td><span data-tooltip class="has-tip top" title="<?=$l->text?>"><?=$l->short_text?></span></td>
                        <?php else: ?>
                            <td><?=$l->short_text?></td>
                        <?php endif;?>
                        <td><?=$l->team_name?></td>
                        <td style="font-size:.9em;"><?=$l->first_name.' '.$l->last_name?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif;?>
