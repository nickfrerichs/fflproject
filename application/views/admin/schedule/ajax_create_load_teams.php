<?php if(!isset($template) || $template->teams != count($teams)): ?>
    <strong>Number of teams do not match. Cannot use this template.</strong>
        <?php foreach($teams as $team): ?>
            <tr>
                <td><?=$team->division_name?></td>
                <td><?=$team->team_name?></td>
            </tr>
        <?php endforeach; ?>
<?php else: ?>
    <?php foreach($teams as $team): ?>
        <tr>
            <td><?=$team->division_name?></td>
            <td><?=$team->team_name?></td>
            <td>
                <div class="select">
                    <select class="schedule-template-team" data-id="<?=$team->team_id?>">
                        <?php for ($i=1; $i<=$template->teams; $i++): ?>
                        <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor;?>
                    </select>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>