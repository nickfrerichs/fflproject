
<?php $this->load->view('components/stat_popup');?>

<?php if($keepers_num > 0):?>
    <?php 

        $body = '<div class="is-size5">Keepers</div>
                <table class="table is-fullwidth">
                    <thead>
                    </thead>
                    <tbody id="keepers-table">
                    </tbody>
                </table>';

    // Keepers modal

        $this->load->view('components/modal', array('id' => 'set-keepers-modal',
                                                            'title' => 'Keepers',
                                                            'body' => $body,
                                                            'reload_on_close' => True));
    ?>

<?php endif;?>


<div class="hero is-link is-bold is-small">
    <div class="hero-body">
        <div class="columns is-centered">

            <div class="column is-narrow is-2">
                <div><h2 class="title"><?=$teamname?></h2></div>

                <div class="subtitle">
                    <div>Record: <?=$record->wins?>-<?=$record->losses?>-<?=$record->ties?></div>
                    <div>Win Pct: <?=str_replace('0.0','.0',number_format($record->winpct,3))?></div>
                    <div>Points: <?=$record->points?></div>
                    <div>Week <?=$this->session->userdata('current_week')?></div>
                </div>
            </div>
            <div class="column is-narrow is-3">
                <?php if ($info->logo): ?>
                <figure class="image is-128x128">
                    <img src="<?=$logo_thumb_url?>">
                </figure>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="section">

    <div class="tabs is-small is-boxed fflp-tabs-active">
        <ul>
            <li class="is-active" data-for="myteam-roster-tab"><a>Roster/Starting Lineup</a></li>
            <li class="" data-for="myteam-schedule-tab"><a>Schedule</a></li>
        </ul>
    </div>

    <div id="myteam-schedule-tab" class="is-hidden">
        <div class="container">
            <?php
                $col = $schedule;
            ?>
            <div class="title">Schedule</div>
            <div class="f-scrollbar">
                <table class="table table-border is-fullwidth is-hoverable fflp-table-mobile">
                <thead>
                    <th class="has-text-centered">Week</th>
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

                        <td class="has-text-centered"><?=$s->week?></td>
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
        </div>
    </div>

    <?php if($this->session->userdata('offseason')): ?>
        <?php $this->load->view('user/offseason');?>
    <?php else: ?>

    <div id="myteam-roster-tab">
        <div class="container">
            <?php if ($keepers_num > 0): ?>
                <a id="set-keepers" href="#">Edit Keepers</a><br>

            <?php endif;?>
            <div class="select">

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

            <br><br>
            
            <div class="columns is-multiline">
                <div class="column">
                    <div class="title is-size-4">Starting Lineup</div>
                    <div class="f-scrollbar">
                        <table class="table is-fullwidth is-narrow table-border is-size-7-mobile is-hoverable">
                            <thead>
                                <th class="has-text-centered">Pos</th><th>Player</th><th>Opponent</th><th class="has-text-centered">Bye</th><th class="is-hidden-mobile">Points</th><th class="has-text-centered">Sit</th>
                            </thead>
                            <tbody id ="starter-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="column">
                    <div class="title is-size-4">Bench</div>
                    <div class="f-scrollbar">
                        <table class="table is-fullwidth is-narrow table-border is-size-7-mobile is-hoverable">
                            <thead>
                                <th>Player</th><th>Opponent</th><th class="has-text-centered">Bye</th><th class="has-text-centered">Points</th><th class="has-text-centered">Start as</th>
                            </thead>
                            <tbody id="bench-tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
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
            loadTables();
        });
    });

    $('#bench-tbody').on('click','.roster-start-btn', function(){
        var week = $("#selected-week").val();
        url = "<?=site_url('myteam/roster/start')?>";
        var data = $(this).val().split("_")
        $.post(url,{'player_id' : data[0], 'pos_id' : data[1], 'week':week},function(data){
            loadTables();
        });
    });

    $('#selected-week').on('change',function(){
        loadTables();
    });

    function loadTables(){
        //alert('loadtables');
        var week = $("#selected-week").val();
        ajax_waits['roster_st'] = true; ajax_waits['roster_bt'] = true;
        url = "<?=site_url('myteam/roster/ajax_starter_table')?>";
        $.post(url,{'week':week}, function(data){
            $("#starter-tbody").html(data);
            ajax_waits['roster_st'] = false;
        });
        url = "<?=site_url('myteam/roster/ajax_bench_table')?>";
        $.post(url,{'week':week}, function(data){
            $("#bench-tbody").html(data);
            ajax_waits['roster_bt'] = false;
        });
    }

    <?php if($keepers_num > 0): ?>
        $("#set-keepers").on('click',function(){
            var url = "<?=site_url('myteam/roster/ajax_keeper_table')?>";
            $.post(url,{},function(data){
                $("#keepers-table").html(data);
                //keeper_max_check();
            });
            $("#set-keepers-modal").addClass('is-active');
        });

        // $('.modal-close, .modal-close-button, .modal-background').on('click', function(){
        //     $(this).closest($('.modal')).removeClass('is-active');
        // });

        // $("#keepers-table").on('click','.keeper-toggle',function(){
        //     var id = $(this).attr('id').replace('keeper-','');
        //     var url = "<?=site_url('myteam/roster/toggle_keeper')?>";
        //     $.post(url,{'id':id},function(){

        //     });
        //     keeper_max_check();
        // });

        // // $(document).on("closed.zf.reveal",function(){
        // //     loadTables();
        // // });

        // function keeper_max_check()
        // {
        //     var len = $(".keeper-toggle:checked").length;
        //     if (len >= <?=$keepers_num?>)
        //     {$(".keeper-toggle:not(:checked)").attr('disabled',true);}
        //     else {$(".keeper-toggle:not(:checked)").attr('disabled',false);}
        // }
    <?php endif;?>

});

</script>
