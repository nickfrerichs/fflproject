<?php
// Data passed from controller
// $schedule - array of weeks > array of games
// $game_types - array of db objects
// $team_list -  array of db objects
?>
<?php //print_r($game_types);?>
<?php //print_r($team_list);?>
<?php //print_r($schedule); ?>




<div class="section">
    <div class="is-size-5">Add Games</div>
    <br>
    <div class="columns is-multiline">
        <div class="column">  
            <div class="field">
                <label class="label"># of Games to Add</label>
                <div class="control">
                    <input id="add-num" class="input" type="text">
                </div>
            </div>
        </div>
        <div class="column">
            <div class="field">
                <label class="label">Add to week</label>
                <div class="control">
                    <input id="week-num" class="input" type="text">
                </div>
            </div>

        </div>
    </div>
    <div class="columns">
        <div class="column">
            <button id="add-games" class="button is-link">Add Games</buton>
        </div>
    </div>
    <br>




<?php // Fill arrays for dropdown options
    $team_options = array(0 => "None");
    $type_options = array();
    $title_options = array(0 => "None");
    foreach($team_list as $t){$team_options[$t->team_id] = $t->team_name;}
    foreach($game_types as $t){$type_options[$t->id] = $t->text_id;}
    foreach($titles as $t){$title_options[$t->id] = $t->text;}
?>
    <div class="is-size-5"> Edit Schedule </div>
    <div class="columns is-multiline">
        <?php foreach ($schedule as $week_num => $week): ?>
            <div class="column is-12">
                <table class="table is-fullwidth is-narrow is-striped fflp-table-fixed">
                    <strong>Week <?=$week_num?></strong>
                    <th>Home</th><th style="width:40px"></th><th>Away</th><th>Game Type</th><th>Title</th><th style="width:100px;">Del</th>
                    <?php foreach ($week as $game_num => $game):?>
                    <tr class="schedule-game" data-id="<?=$game['id']?>">
                        <td>
                            <div class="select">
                                <select class="home">
                                    <option value="0">None</option>
                                    <?php foreach($team_list as $t): ?>
                                        <option value = "<?=$t->team_id?>"<?php if($game['home_id'] == $t->team_id){echo " selected";}?>><?=$t->team_name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </td>
                        <td>at</td>
                        <td>
                            <div class="select">
                                <select class="away">
                                    <option value="0">None</option>
                                    <?php foreach($team_list as $t): ?>
                                        <option value = "<?=$t->team_id?>"<?php if($game['away_id'] == $t->team_id){echo " selected";}?>><?=$t->team_name?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="select">
                                <select class="type">
                                    <?php foreach($game_types as $t): ?>
                                        <option value = "<?=$t->id?>"<?php if($game['type_id'] == $t->id){echo " selected";}?> ><?=$t->text_id?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="select">
                                <select class="title">
                                    <option value="0">None</option>
                                    <?php foreach($titles as $t): ?>
                                        <option value = "<?=$t->id?>"<?php if($game['title_id'] == $t->id){echo " selected";}?> > <?=$t->text?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <a class="delete-game" href="#" data-id="<?=$game['id']?>">X</a>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
        </div>
        <div><button id="save-schedule" class="button is-small is-link">Save Schedule</button></div>
    </div>
</div>

<script>
    $('#add-games').on('click',function(){
        var url="<?=site_url('admin/schedule/ajax_add_games')?>";
        var num = $('#add-num').val();
        var week = $('#week-num').val();
        <?php if(isset($selected_year)){echo 'var year = '.$selected_year."\n";}else{echo 'var year = '.$this->session->userdata('current_year')."\n";}?>
        $.post(url,{'num': num, 'week': week, 'year': year},function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('.delete-game').on('click',function(){
        var url="<?=site_url('admin/schedule/ajax_delete_game')?>";
        var id = $(this).data('id');
        $.post(url,{'id':id},function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('#save-schedule').on('click',function(){
        var url="<?=site_url('admin/schedule/ajax_save_schedule')?>";
        var schedule = [];
        var week = {};
        $('.schedule-game').each(function(){
            game = {}
            game['home'] = $(this).find('.home').val();
            game['away'] = $(this).find('.away').val();
            game['type'] = $(this).find('.type').val();
            game['title'] = $(this).find('.title').val();
            game['id'] = $(this).data('id');
            schedule.push(game);
        });
        
        $.post(url,{'schedule':schedule},function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
        // POST THE ARRAY

    });
</script>