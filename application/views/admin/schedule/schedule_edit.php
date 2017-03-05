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
        <div class="callout">
        <h5> Add Games </h5>
        <?=form_open(current_url())?>
        <table>
            <tr>
                <td><?=form_label('# of Games to Add', 'num')?></td>
                <td><?=form_input('num')?></td>
                <td><?=form_label('Add to week', 'week')?></td>
                <td><?=form_input('week')?></td>
            </tr>
        </table>
        <input class="button small" type="submit" name="add" value="Add Games"  />
        <?=form_close()?>
    </div>
    </div>
</div>


<div class="row">
    <div class="columns">

        <?php // Fill arrays for dropdown options
            $team_options = array(0 => "None");
            $type_options = array();
            $title_options = array(0 => "None");
            foreach($team_list as $t){$team_options[$t->team_id] = $t->team_name;}
            foreach($game_types as $t){$type_options[$t->id] = $t->text_id;}
            foreach($titles as $t){$title_options[$t->id] = $t->text;}
        ?>
        <h5> Edit Schedule </h5>
        <?=form_open(current_url())?>
        <div class="row">
        <?php foreach ($schedule as $week_num => $week): ?>
            <div class="columns small-12 medium-12 large-12">
                <div class="callout">
                <table>

                    <strong>Week <?=$week_num?></strong>
                    <th>Home</th><th></th><th>Away</th><th>Game Type</th><th>Title</th><th>Del</th>
                    <?php foreach ($week as $game_num => $game):?>
                    <tr>
                        <td><?=form_dropdown('home'.$week_num.'_'.$game_num,$team_options,$game['home_id'])?></td>
                        <td>at</td>
                        <td><?=form_dropdown('away'.$week_num.'_'.$game_num,$team_options,$game['away_id'])?></td>
                        <td><?=form_dropdown('type'.$week_num.'_'.$game_num,$type_options,$game['type_id'])?></td>
                        <td><?=form_dropdown('title'.$week_num.'_'.$game_num,$title_options,$game['title_id'])?></td>
                        <td><a href='<?=site_url('admin/schedule/delete_game/'.$week_num.'/'.$game_num)?>'>X</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                </div>
            </div>

        <?php endforeach; ?>
        </div>
        <div><input class="button small" type="submit" name="save_schedule" value="Save Schedule"  /></div>

        <?=form_close()?>
    </div>
</div>
