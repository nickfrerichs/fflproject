    <div class="row">
        <div class="columns">
            <div class="callout">
            <h5>Create new schedule?</h5>
            <div><strong>(any current schedule for the current year will be erased)</strong></div>


            <?php $data = array(); ?>
            <?php foreach($templates as $t): ?>
                <?php $data[$t->id] = $t->name.' '.$t->teams.' teams, '.$t->divisions.' divisions'; ?>
            <?php endforeach; ?>
            <?=form_open(current_url())?>
            <?=form_dropdown('template', $data)?>
            <div><input class="button small" type="submit" name="select_template" value="Select Schedule"  /></div>
            <?=form_close()?>
            <br>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="columns">
            <div class="callout">
            <h5>League Teams </h5>
            <table>
            <?php if(!isset($template) || $template->teams != count($teams)): ?>
               <strong>Number of teams do not match. Cannot use this template.</strong>
                <?php foreach($teams as $team): ?>
                    <tr>
                        <td><?=$team->division_name?></td>
                        <td><?=$team->team_name?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                    <?=form_open(current_url())?>
                    <?=form_hidden('template_id', $template->id); ?>
                    <?php $data = array(); ?>
                    <?php for($i=1; $i<=$template->teams; $i++){$data[$i] = $i;} ?>
                    <?php foreach($teams as $team): ?>
                    <tr>
                        <td><?=$team->division_name?></td>
                        <td><?=$team->team_name?></td>
                        <td><?=form_dropdown($team->team_id, $data)?></td>
                    </tr>
                    <?php endforeach; ?>

            </table>
            <input class="button small" type="submit" name="create_schedule" value="Create"  />
                    <?=form_close()?>
            <?php endif; ?>
        </div>
    </div>
</div>
