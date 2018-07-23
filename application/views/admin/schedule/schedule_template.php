<div class="section">
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

            <table class="table is-fullwidth is-narrow">
                <tr>
                    <td>Template Name</td>
                    <td><input id="template-name" class="input" type="text"></td>
                </tr>
                <tr>
                    <td>Template Description</td>
                    <td><input id="template-description" class="input" type="text"></td>
                </tr>
                <tr>
                    <td>Number of Teams</td>
                    <td><input id="template-num-teams" class="input" type="text"></td>
                </tr>
                <tr>
                    <td>Number of Divisions</td>
                    <td><input id="template-num-divisions" class="input" type="text"></td>
                </tr>
                <tr>
                    <td>Number of Regular Season Weeks</td>
                    <td><input id="template-num-reg-weeks" class="input" type="text"></td>
                </tr>
                <tr>
                    <td>Number of Games per Week</td>
                    <td><input id="template-num-games-per-week" class="input" type="text"></td>
                </tr>

            </table>
            <button id="create-button" class="button is-small is-link">Create</button>
        </div>
    </div>
</div>

<script>
    $('#create-button').on('click',function(){
        var url = "<?=site_url('admin/schedule_templates/ajax_create_template')?>";
        var name = $('#template-name').val();
        var desc = $('#template-description').val();
        var num_teams = $('#template-num-teams').val();
        var num_divs = $('#template-num-divisions').val();
        var num_reg_weeks = $('#template-num-reg-weeks').val();
        var num_games_per_week = $('#template-num-games-per-week').val();

        $.post(url,{'name':name,'desc':desc,'num_teams':num_teams,'num_divs':num_divs,'num_reg_weeks':num_reg_weeks,
                    'num_games_per_week':num_games_per_week}, function(data){
            if (data.success)
            {
                location.reload();
            }

        },'json');
    });
</script>