    <div class="row">
        <div class="columns">
            <h5>Create new schedule?</h5>
            <div>(any current schedule for the current year will be erased)</div>
        </div>
    </div>
    <div class="row">
        <div class="columns">
            <?php $data = array(); ?>
            <?php foreach($templates as $t): ?>
                <?php $data[$t->id] = $t->name.' '.$t->teams.' teams, '.$t->divisions.' divisions'; ?>
            <?php endforeach; ?>
            <?=form_open(current_url())?>
            <?=form_dropdown('template', $data)?>
            <div><?=form_submit('select_template', 'Select Schedule')?></div>
            <?=form_close()?>
            <br>
            <hr>
            <br>
            <h5>League Teams </h5>
            <table>
            <?php if(!isset($template) || $template->teams != count($teams)): ?>
               Number of teams do not match.
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
            <?=form_submit('create_schedule', 'Create')?>
                    <?=form_close()?>
            <?php endif; ?>
    </div>
</div>
