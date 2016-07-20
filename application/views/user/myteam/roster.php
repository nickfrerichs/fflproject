
<?php $this->load->view('template/modals/stat_popup');?>

<?php if($keepers_num > 0):?>
    <div class="reveal small" id="set-keepers-modal" data-reveal data-overlay="true">
        <h5>Keepers</h5>
        <table>
            <thead>
            </thead>
            <tbody id="keepers-table">
            </tbody>
        </table>
    	<button class="close-button" data-close aria-label="Close modal" type="button">
    	  <span aria-hidden="true">&times;</span>
    	</button>
    </div>
<?php endif;?>

<div class="row callout">
    <div class="columns medium-3 text-center small-12">
        <div><h4><?=$teamname?></h4></div>
        <?php if ($info->logo): ?>
            <div><img src="<?=$logo_thumb_url?>"></div>
        <?php endif; ?>
        <div class="mt-r-teamstats-style">
            <div>Record: <?=$record->wins?>-<?=$record->losses?>-<?=$record->ties?></div>
            <div>Win Pct: <?=str_replace('0.0','.0',number_format($record->winpct,3))?></div>
            <div>Points: <?=$record->points?></div>
            <div>Week <?=$this->session->userdata('current_week')?></div>
        </div>
        <hr class="show-for-small-only">
    </div>
    <div class="columns medium-9 mt-r-schedule-style small-12">
        <?php
              $cols = array();
              if (count($schedule) > 7){
              $cols[] = array_slice($schedule, 0, count($schedule) / 2);
              $cols[] = array_slice($schedule, count($schedule) / 2);
            }else{$cols[] = $schedule;}
        ?>
        <?php if (count($schedule) != 0): ?>
            <h5 class="text-center">Schedule</h5>
        <?php endif; ?>
        <div class="row align-center">
            <?php foreach ($cols as $col):?>
            <?php if (count($col) == 0){continue;} ?>
            <?php if (count($cols) == 1){$medcols=12;}else{$medcols=6;} ?>
                <div class="columns small-12 medium-<?=$medcols?>">
                <table>
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
</div>

<?php if($this->session->userdata('offseason')): ?>
    <?php $this->load->view('user/offseason');?>
<?php else: ?>
<div class="row">
    <div class="columns callout">
        <div class="row">
            <div class="columns medium-2">
                <?php if ($keepers_num > 0): ?>
                    <a id="set-keepers" href="#"> Edit Keepers</a>

                <?php endif;?>
                <select id="selected-week">
                    <?php foreach($weeks as $w): ?>
                        <?php if($w->week == $this->session->userdata('current_week')): ?>
                            <option selected value="<?=$w->week?>">Week <?=$w->week?></option>
                        <?php else: ?>
                        <option value="<?=$w->week?>">Week <?=$w->week?></option>
                        <?php endif;?>
                    <?php endforeach; ?>
                </select>

            </div>
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
            <div class="large-6 columns small-12">
                <div><h5>Starting Lineup</h5></div>
                <table>
                    <thead>
                        <th>Pos</th><th>Player</th><th>Opponent</th><th>Bye</th><th class="text-center hide-for-extra-small">Points</th><th class="text-center">Sit</th>
                    </thead>
                    <tbody id ="starter-tbody">
                    </tbody>
                </table>
            </div>
            <div class="large-6 columns small-12">
                <div><h5>Bench</h5></div>
                <table>
                    <thead>
                        <th>Player</th><th>Opponent</th><th>Bye</th><th class="text-center hide-for-extra-small">Points</th><th class="text-center">Start as</th>
                    </thead>
                    <tbody id="bench-tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
$(document).ready(function(){
    loadTables();

    $('#starter-tbody').on('click','.roster-sit-btn',function(){
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/start')?>";
        $.post(url,{'player_id' : $(this).val(),'week' : week, 'pos_id':0}, function(data){
            console.log(data);
            loadTables();
        });
    });

    $('#bench-tbody').on('click','.roster-start-btn', function(){
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/start')?>";
        var data = $(this).val().split("_")
        $.post(url,{'player_id' : data[0], 'pos_id' : data[1], 'week':week},function(data){
            console.log(data);
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

    <?php if($keepers_num > 0): ?>
        $("#set-keepers").on('click',function(){
            var url = "<?=site_url('myteam/roster/ajax_keeper_table')?>";
            $.post(url,{},function(data){
                $("#keepers-table").html(data);
                keeper_max_check();
            });
            $("#set-keepers-modal").foundation('open');
        });

        $("#keepers-table").on('click','.keeper-toggle',function(){
            var id = $(this).attr('id').replace('keeper-','');
            var url = "<?=site_url('myteam/roster/toggle_keeper')?>";
            $.post(url,{'id':id},function(){

            });
            keeper_max_check();
        });

        $(document).on("closed.zf.reveal",function(){
            loadTables();
        });

        function keeper_max_check()
        {
            var len = $(".keeper-toggle:checked").length;
            if (len >= <?=$keepers_num?>)
            {$(".keeper-toggle:not(:checked)").attr('disabled',true);}
            else {$(".keeper-toggle:not(:checked)").attr('disabled',false);}
        }
    <?php endif;?>

});

</script>
