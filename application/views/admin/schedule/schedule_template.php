<div class="container">
    <div class="is-size-5">Schedule templates</div>
    <div class="columns">
        <div class="column">
            <a href="<?=site_url('admin/schedule_templates/gametypes')?>">Manage Game Types</a> <br>
            <a href="<?=site_url('admin/schedule_templates/titles')?>">Manage Titles Defintions</a> <br>
            <table class="table is-fullwidth is-narrow">
                <th>Name</th>
                <th>Teams</th>
                <th>Divisions</th>
                <th>Weeks</th>
                <th>Per Week</th>
                <th>Description</th>
                <th></th>
                <th></th>

            <?php foreach($templates as $t): ?>
                <tr>
                    <td><?=$t->name?></td>
                    <td><?=$t->teams?></td>
                    <td><?=$t->divisions?></td>
                    <td><?=$t->weeks?></td>
                    <td><?=$t->per_week?></td>
                    <td><?=$t->description?></td>
                    <td><a href="<?=site_url('admin/schedule_templates/edit/'.$t->id)?>">edit</a></td>
                    <td><a href="<?=site_url('admin/schedule_templates/delete/'.$t->id)?>">delete</a></td>

                </tr>
            <?php endforeach; ?>
            </table>
            <hr>
            <div class="is-size-5">Create new template</div>
            <?=form_open(current_url())?>
            <table class="table is-fullwidth is-narrow">
                <tr>
                    <td><?=form_label('Template name', 'name')?></td>
                    <td><?=form_input('name')?></td>
                </tr>
                <tr>
                    <td><?=form_label('Template description', 'description')?></td>
                    <td><?=form_input('description')?></td>
                </tr>
                <tr>
                    <td><?=form_label('Number of teams', 'teams')?></td>
                    <td><?=form_input('teams')?></td>
                </tr>
                <tr>
                    <td><?=form_label('Number of divisions', 'divisions')?></td>
                    <td><?=form_input('divisions')?></td>
                </tr>
                <tr>
                    <td><?=form_label('Regular season weeks', 'weeks')?></td>
                    <td><?=form_input('weeks')?></td>
                </tr>
                <tr>
                    <td><?=form_label('Total games per week', 'weeks')?></td>
                    <td><?=form_input('per_week')?></td>
                </tr>

            </table>
            <input class="button small" type="submit" name="create" value="Create"  />
            <?=form_close()?>
        </div>
    </div>
</div>
