<?php if ($this->session->userdata('team_id') == 11)
{
    //print_r($weeks);
}
?>
<div class="container">
    <div class="row">
        <h3>Schedule</h3>
        <?php foreach($weeks as $week => $schedule): ?>
            <h4>Week <?=$week?></h4>
        <table class="table table-condensed table-striped">
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
        <hr>
        <br>
        <?php endforeach; ?>
    </div>
</div>
