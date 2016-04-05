<?php //print_r($totals); ?>
<div class="container">
    <div class="row">
<h3>Money List <?=$this->session->userdata('current_year')?></h3>
<br>
<h4>Totals</h4>
<table class="table table-striped">
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
<br>
<h4>Log</h4>

    <table class="table table-striped">
        <thead>
            <th>Week</th><th>Amount</th><th>Score</th><th></th><th>Team</th><th>Owner</th>
        </thead>
        <tbody>
            <?php foreach($list as $l): ?>
                <tr>
                    <td><?=$l->week?></td>
                    <td>$<?=number_format($l->amount,2)?></td>
                    <td><?=$l->team_score?></td>
                    <td><?=$l->short_text?></td>
                    <td><?=$l->team_name?></td>
                    <td style="font-size:.9em;"><?=$l->first_name.' '.$l->last_name?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
