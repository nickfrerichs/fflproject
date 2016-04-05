

<?php if($this->session->userdata('team_id') == 11)
{
    //xprint_r($schedule);
}
?>

<?php $this->load->view('template/modals/stat_popup');?>

<div class="container">
    <div class="row">
        <div class="col-sm-2 text-center" style="padding-top:40px;">
            <div><h3><?=$teamname?></h3></div>

            <?php if ($info->logo): ?>
                <div><img src="<?=$logo_thumb_url?>"></div>
            <?php endif; ?>

            <h5>Record: <?=$record->wins?>-<?=$record->losses?>-<?=$record->ties?></h5>
            <h5>Win Pct: <?=str_replace('0.0','.0',number_format($record->winpct,3))?></h5>
            <h5>Points: <?=$record->points?>
            <h4>Week <?=$this->session->userdata('current_week')?></h4>
        </div>
        <div class="col-sm-10">
            <h4 class="text-center">Schedule</h4>
            <?php
                  $cols = array();
                  $cols[] = array_slice($schedule, 0, count($schedule) / 2);
                  $cols[] = array_slice($schedule, count($schedule) / 2);
                  ?>
            <?php foreach ($cols as $col):?>
                <div class="col-sm-6">
            <table class="table table-condensed, table-striped table-border" style="font-size:.8em">
                <thead>
                    <th>Week</th>
                    <th>Opponent</th>
                    <th>Result</th>
                </thead>
                <tbody>
            <?php foreach($col as $key => $s): ?>
                <?php if($s->week == $this->session->userdata('current_week')):?>
                    <tr style="background-color:#E0ECF8">
                    <?php else:?>
                        <tr>
                    <?php endif;?>

                    <td><?=$s->week?></td>
                    <?php if($s->home_id != $this->session->userdata('team_id')): ?>
                        <td><a href="<?=site_url('league/teams/view/'.$s->home_id)?>">@<?=$s->home_name?></a></td>
                        <?php if($s->away_win == '1'):?>
                            <td>Win (<?=$s->away_score?> - <?=$s->home_score?>)</td>
                        <?php elseif ($s->away_win === '0'): ?>
                            <td>Loss (<?=$s->away_score?> - <?=$s->home_score?>)</td>
                        <?php else:?>
                            <td></td>
                        <?php endif;?>
                    <?php else: ?>
                        <td><a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a></td>
                        <?php if($s->home_win == '1'):?>
                            <td>Win (<?=$s->home_score?> - <?=$s->away_score?>)</td>
                        <?php elseif ($s->home_win === '0'): ?>
                            <td>Loss (<?=$s->home_score?> - <?=$s->away_score?>)</td>
                        <?php else:?>
                            <td></td>
                        <?php endif;?>
                    <?php endif;?>
                </tr>

            <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endforeach;?>
        </div>
    </div>
    <hr>
    <div class="row text-center">
        <form class="form-inline text-center" style="padding-top: 10px;">
    		<div class="form-group">
                <select id="selected-week" class="form-control">
                    <?php foreach($weeks as $w): ?>
                        <?php if($w->week == $this->session->userdata('current_week')): ?>
                            <option selected value="<?=$w->week?>">Week <?=$w->week?></option>
                        <?php else: ?>
                        <option value="<?=$w->week?>">Week <?=$w->week?></option>
                        <?php endif;?>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    <!--
    <div class="row text-center">
        <div class="checkbox">
          <label>
            <input id="future-weeks" type="checkbox"> Set Future Weeks
          </label>
        </div>
    </div>
    -->
    <div class="row">
        <div class="col-md-6">
            <div><h4>Starting Lineup</h4></div>
            <table class="table table-border table-condensed text-center table-striped">
                <thead>
                    <th class="text-center">Pos</th><th class="text-center">Player</th><th class="text-center">Opponent</th><th class="text-center hidden-xxs">Points</th><th class="text-center">Sit</th>
                </thead>
                <tbody id ="starter-tbody">
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <div><h4>Bench</h4></div>
            <table class="table table-border table-condensed text-center table-striped">
                <thead>
                    <th class="text-center">Player</th><th class="text-center">Opponent</th><th class="text-center hidden-xxs">Points</th><th class="text-center">Start as</th>
                </thead>
                <tbody id="bench-tbody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    loadTables();

    $('#starter-tbody').on('click','.roster-sit-btn',function(){
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/sit')?>";
        $.post(url,{'player_id' : $(this).val(),'week' : week}, function(data){
            console.log(data);
            loadTables();
        });
    });

    $('#bench-tbody').on('click','.roster-start-btn', function(){
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/start')?>";
        var data = $(this).val().split("_")
        $.post(url,{'player_id' : data[0], 'pos_id' : data[1], 'week':week},function(){
            loadTables();
        });
    });

    $('#selected-week').on('change',function(){
        loadTables();
    });

    function loadTables(){
        //alert('loadtables');
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/ajax_starter_table')?>";
        $.post(url,{'week':week}, function(data){
            $("#starter-tbody").html(data);
        });
        url = "<?=site_url('myteam/roster/ajax_bench_table')?>";
        $.post(url,{'week':week}, function(data){
            $("#bench-tbody").html(data);
        });
    }


});

</script>
