<div class="section">
    <div class="columns">
        <div class="column">
        <?php if(count($title_defs) == 0): ?>
            No titles defined.
        <?php endif; ?>

        <?php if (count($titles) > 0): ?>
            <div class="is-size-5">Titles from Schedule</div>
            <table class="table is-striped is-fullwidth is-narrow fflp-table-fixed">
                <thead></thead>
                <tbody>
                    <?php foreach($titles as $t): ?>
                        <?php if(!$t->schedule_id){continue;}?>
                        <tr>
                            <td><?=$t->week?></td>
                            <td><?=$t->title_text?></td>
                            <?php if($t->team_id == $t->h_team_id):?>
                                <td><b><?=$t->h_team_name?></b> (<a class="delete-title-link" href="#" data-title-id="<?=$t->title_id?>">X</a>)</td>
                            <?php else: ?>
                                <td><?=$t->h_team_name?> (<a class="assign-title-link" href="#"
                                                                    data-title-def-id="<?=$t->title_def_id?>" 
                                                                    data-schedule-id="<?=$t->schedule_id?>" 
                                                                    data-team-id="<?=$t->h_team_id?>">assign</a>)</td>
                            <?php endif; ?>
                            <td><?=$t->h_team_score?></td>
                            <?php if($t->team_id == $t->a_team_id): ?>
                                <td><b><?=$t->a_team_name?></b> (<a class="delete-title-link" href="#" data-title-id="<?=$t->title_id?>">X</a>)</td>
                            <?php else:?>
                                <td><?=$t->a_team_name?> (<a class="assign-title-link" href="#" 
                                                                    data-title-def-id="<?=$t->title_def_id?>" 
                                                                    data-schedule-id="<?=$t->schedule_id?>" 
                                                                    data-team-id="<?=$t->a_team_id?>">assign</a>)</td>
                            <?php endif;?>
                            <td><?=$t->a_team_score?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif;?>
        <?php if (count($title_defs) > 0): ?>
            <div class="is-size-5">Assign Titles</div>
            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <thead><th>Title</th><th>Team</th><th></th></thead>
                <tbody>
                    <?php foreach($other_titles as $t):?>
                    <tr>
                        <td><?=$t->text?></td>
                        <td><?=$t->team_name?></td>
                        <td><button class="delete-title-link button is-small is-link" data-title-id="<?=$t->title_id?>">X</button></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td>
                            <div class="select">
                                <select id="other-title-def-id">
                                <?php foreach($title_defs as $def): ?>
                                    <option value="<?=$def->id?>"><?=$def->text?></option>
                                <?php endforeach;?>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="select">
                                <select id="other-title-team-id">
                                <?php foreach($teams as $team): ?>
                                    <option value="<?=$team->team_id?>"><?=$team->team_name?></option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </td>
                        <td width="10%"><button id="add-other-title" class="button is-small is-link">Add</button></td>
                    </tr>
                </tbody>
            </table>
        <?php endif;?>
    </div>
</div>


<script>

    $('.assign-title-link').on('click',function(){
        var url = '<?=site_url("admin/schedule/ajax_assign_title")?>';
        var title_def_id = $(this).data('title-def-id');
        var schedule_id = $(this).data('schedule-id');
        var team_id = $(this).data('team-id');
        var args = {'title_def_id' : title_def_id,
                    'schedule_id':schedule_id,
                    'team_id':team_id,
                    'year': <?=$selected_year?>};
        $.post(url,args, function(data){
            if (data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('.delete-title-link').on('click',function(){
        var url = '<?=site_url("admin/schedule/ajax_delete_title")?>';
        var args = {'title_id': $(this).data('title-id')};
        $.post(url,args,function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('#add-other-title').on('click',function(){
        var url = '<?=site_url("admin/schedule/ajax_assign_title")?>';
        var args = {'title_def_id' : $('#other-title-def-id').val(),
                    'team_id' : $('#other-title-team-id').val(),
                    'year' : <?=$selected_year?>};
        $.post(url,args,function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('.assign-title-select').on('change',function(){
        var url = '<?=site_url("admin/schedule/ajax_assign_title")?>';
        var args = {'title_def_id':$(this).data('title-def-id'),
                    'team_id':$(this).val(),
                    'year':<?=$selected_year?>};

        $.post(url,args,function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

</script>