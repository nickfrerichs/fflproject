
<?php //print_r($matchups); ?>

<div class="row">
    <div class="columns">
        <div class="callout">
        <h5><?=$template->teams.' teams - '.$template->divisions.' divisions'?></h5>
        <?=form_open(current_url())?>
        <table>
            <tr>
                <td><?=form_label('Template name', 'name')?></td>
                <td><?=form_input('name', $template->name)?></td>
            </tr>
            <tr>
                <td><?=form_label('Template description', 'description')?></td>
                <td><?=form_input('description', $template->description)?></td>
            </tr>
            <tr>
                <td><?=form_label('Number of teams', 'teams')?></td>
                <td><?=form_input('teams', $template->teams)?></td>
            </tr>
            <tr>
                <td><?=form_label('Number of divisions', 'divisions')?></td>
                <td><?=form_input('divisions', $template->divisions)?></td>
            </tr>
            <tr>
                <td><?=form_label('Regular season weeks', 'weeks')?></td>
                <td><?=form_input('weeks', $template->weeks)?></td>
            </tr>
            <tr>
                <td><?=form_label('Total games per week', 'per_week')?></td>
                <td><?=form_input('per_week', $template->per_week)?></td>
            </tr>

        </table>
        <input class="button small" type="submit" name="update" value="Update info"  />
        <?=form_close()?>
        </div>
        <div class="callout">
        <h5>Edit Matchups</h5>
        <small>Use numbers to denote teams (Ex: 1, 2, 3, 4)</small>
        <br><br>
        <?=form_open(current_url())?>
        <?php $count=0; ?>
        <div class="row">
        <?php for($w=1; $w<=$template->weeks; $w++): ?>

            <div class="columns medium-6 small-12">
                <table>
                <h5>Week <?=$w?></h5>
                <?php for($g=1; $g<=$template->per_week; $g++): ?>
                <tr>
                    <td>
                        <?=form_label('Home','home'.$count.'_'.$w.'_'.$g)?>
                        <?php if(isset($matchups[$w][$g]['home'])): ?>
                            <?=form_input('home'.$count.'_'.$w.'_'.$g,$matchups[$w][$g]['home'])?>
                        <?php else: ?>
                            <?=form_input('home'.$count.'_'.$w.'_'.$g)?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?=form_label('Away','away'.$count.'_'.$w.'_'.$g)?>
                        <?php if(isset($matchups[$w][$g]['away'])): ?>
                            <?=form_input('away'.$count.'_'.$w.'_'.$g,$matchups[$w][$g]['away'])?>
                        <?php else: ?>
                            <?=form_input('away'.$count.'_'.$w.'_'.$g)?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $count++; ?>
                <?php endfor; ?>
                </table>
            </div>
        <?php endfor; ?>
        </div>
        <input class="button small" type="submit" name="save" value="Save matchups"  />
        <?=form_close()?>
        </div>
    </div>
</div>
