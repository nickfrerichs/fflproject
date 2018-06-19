<?php if(array_key_exists("previous_week",$schedule) || array_key_exists("current_week",$schedule)):?>
    <div class="section">


        <?php if(array_key_exists("current_week",$schedule)): ?>
            <div class="is-size-5"><b>Week <?=$this->session->userdata('current_week');?></b></div>
            <table class="table is-fullwidth fflp-table-fixed is-bordered">
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
            <div class="is-size-5"><b>Week <?=$this->session->userdata('current_week')-1;?></b></div>
            <table class="table is-fullwidth fflp-table-fixed is-bordered">
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
<?php endif;?>



<div class="section">

    <div class="is-size-5"><b>Full Schedule & Results</b></div>

    <div class="columns is-multiline">
    <?php foreach($schedule['weeks'] as $week => $schedule): ?>

        <div class="column is-half-desktop" style="padding-bottom:20px;">
            <div class="is-size-6">Week <?=$week?></div>
            <table class="table is-fullwidth is-narrow is-bordered fflp-table-fixed is-striped">
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
