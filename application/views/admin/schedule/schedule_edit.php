<?php
// Data passed from controller
// $schedule - array of weeks > array of games
// $game_types - array of db objects
// $team_list -  array of db objects
?>
<?php //print_r($game_types);?>
<?php //print_r($team_list);?>
<?php //print_r($schedule); ?>

<div class="row">
    <div class="columns">
        <h5> Add Games </h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <?=form_open(current_url())?>
        <table>
            <tr>
                <td><?=form_label('# of Games to Add', 'num')?></td>
                <td><?=form_input('num')?></td>
                <td><?=form_label('Add to week', 'week')?></td>
                <td><?=form_input('week')?></td>
            </tr>
        </table>
        <?=form_submit('add', 'Add Games')?>
        <?=form_close()?>



        <?php // Fill arrays for dropdown options
            $team_options = array(0 => "None");
            $type_options = array();
            foreach($team_list as $t){$team_options[$t->team_id] = $t->team_name;}
            foreach($game_types as $t){$type_options[$t->id] = $t->text_id;}
        ?>
        <h5> Edit Schedule </h5>
        <?=form_open(current_url())?>
        <div class="row">
        <?php foreach ($schedule as $week_num => $week): ?>
            <div class="columns medium-12 large-6">
                <table>

                    <strong>Week <?=$week_num?></strong>
                    <th>Home</th><th></th><th>Away</th><th>Game Type</th>
                    <?php foreach ($week as $game_num => $game):?>
                    <tr>
                        <td><?=form_dropdown('home'.$week_num.'_'.$game_num,$team_options,$game['home_id'])?></td>
                        <td>at</td>
                        <td><?=form_dropdown('away'.$week_num.'_'.$game_num,$team_options,$game['away_id'])?></td>
                        <td><?=form_dropdown('type'.$week_num.'_'.$game_num,$type_options,$game['type_id'])?></td>
                        <td><a href='<?=site_url('admin/schedule/delete_game/'.$week_num.'/'.$game_num)?>'>X</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

        <?php endforeach; ?>
        </div>
        <div><?=form_submit('save_schedule', 'Save Schedule')?></div>
        <?=form_close()?>
    </div>
</div>
