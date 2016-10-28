<?php if(array_key_exists("previous_week",$schedule) || array_key_exists("current_week",$schedule)):?>
    <div class="row">
        <div class="columns callout">

            <?php if(array_key_exists("current_week",$schedule)): ?>
                <h5><b>Week <?=$this->session->userdata('current_week');?></b></h5>
                <table>
                    <thead>
                        <th style="width:40%">Home</th>

                        <th style="width:40%">Away</th>
                        <th style="width:10%">Score</th>
                        <th style="width:10%">Score</th>

                    </thead>
                    <tbody>
                        <?php foreach($schedule['current_week'] as $s): ?>
                            <tr>
                                <td><a href="<?=site_url('league/teams/view/'.$s->home_id)?>"><?=$s->home_name?></a></td>
                                <td><a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a></td>
                                <td><?=$s->home_score?></td>
                                <td><?=$s->away_score?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;?>

            <?php if(array_key_exists("previous_week",$schedule)): ?>
                <h5><b>Week <?=$this->session->userdata('current_week')-1;?></b></h5>
                <table>
                    <thead>
                        <th style="width:40%">Home</th>

                        <th style="width:40%">Away</th>
                        <th style="width:10%">Score</th>
                        <th style="width:10%">Score</th>

                    </thead>
                    <tbody>
                        <?php foreach($schedule['previous_week'] as $s): ?>
                            <tr>
                                <td><a href="<?=site_url('league/teams/view/'.$s->home_id)?>"><?=$s->home_name?></a></td>
                                <td><a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a></td>
                                <td><?=$s->home_score?></td>
                                <td><?=$s->away_score?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;?>


        </div>
    </div>
<?php endif;?>


<div class="row">
    <div class="columns callout" style="font-size:.9em">
        <div class="row">
            <div class="columns">
                <h5><b>Full Schedule & Results</b></h5>
            </div>
        </div>
        <div class="row">
        <?php foreach($schedule['weeks'] as $week => $schedule): ?>

            <div class="columns large-6 small-12" style="padding-bottom:20px;">
                <h6>Week <?=$week?></h6>
                <table class="table-condensed">
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

        <?php endforeach; ?>
        </div>
    </div>
</div>
