
<div class="row">
    <div class="columns">
        <h5>Schedule</h5>
    </div>
</div>

<?php foreach($weeks as $week => $schedule): ?>
<div class="row">
    <div class="columns">
        <h6>Week <?=$week?></h6>
    </div>
</div>
<div class="row">
    <div class="columns">
        <table>
            <thead>
                <th style="width:40%">Home</th>

                <th style="width:40%">Away</th>
                <th style="width:10%">Score</th>
                <th style="width:10%">Score</th>

            </thead>
            <tbody>
                <?php foreach($schedule as $s): ?>
                    <tr>
                        <td><a href="<?=site_url('league/teams/view/'.$s->home_id)?>"><?=$s->home_name?></a></td>
                        <td><a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a></td>
                        <td><?=$s->home_score?></td>
                        <td><?=$s->away_score?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="columns">
        <hr>
    </div>
</div>
<?php endforeach; ?>
