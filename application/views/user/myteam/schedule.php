<div class="container">
    <div class="page-heading">
        <h3>Schedule</h3>
        </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table light-bg table-condensed table-striped table-border">
                <thead>
                    <th>Week</th><th>Opponent</th><th>Result</th>
                </thead>
                <tbody>
                <?php foreach($schedule as $week): ?>
                <?php $outcome = null ?>
                <tr>
                    <td><?=$week->week?></td>
                    <td><span class="small-text">
                    <?php if($week->home_id == $teamid): ?>
                            <?php echo $week->away_name; //Opponent is away team
                                  $o = $week->away_score; 
                                  $t = $week->home_score;
                            ?>
                    <?php else: ?>
                            <?php echo $week->home_name; //Opponent is home team
                                  $o = $week->home_score;
                                  $t = $week->away_score;
                            ?>    
                    <?php endif; ?>
                    </span></td>
                    <?php if ($week->win_id == $teamid){$outcome = 'Win';} ?>
                    <?php if ($week->loss_id == $teamid){$outcome = 'Loss';} ?>
                    <?php if ($week->tie == 1){$outcome = 'Tie';} ?>
                    <?php if (isset($outcome)){$outcome.='('.$t.' - '.$o.')';}
                          else{$outcome = '-';}?>
                    <td><?=$outcome?></td>        
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>

        </div>
    </div>
</div>