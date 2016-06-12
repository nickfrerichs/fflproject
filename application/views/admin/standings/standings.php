
<div class="row">
    <div class="columns">
        <table class="table table-striped table condensed">
            <thead>
                <th>Team Name</th>
                <th>Owner</th>
                <th>Notation</th>
            </thead>
            <tbody>
                <?php foreach ($teams as $t): ?>
                    <tr>
                        <td><?=$t->team_name?></td>
                        <td><?=$t->first_name.' '.$t->last_name?></td>
                        <td>
                            <select class="team-notation" data-teamid="<?=$t->team_id?>">
                                <option value='0'>None</option>
                                <?php foreach($notations as $n): ?>
                                    <option value="<?=$n->id?>"
                                    <?php if($t->notation_id==$n->id): ?>
                                        selected
                                    <?php endif;?>
                                    ><?=$n->symbol.' ('.$n->text.')' ?></option>
                                <?php endforeach;?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <br><br>

        <a href="<?=site_url('admin/standings/notations')?>">View/Edit Notations</a>

    </div>
</div>

<script>
$(".team-notation").on('change',function(){
    var teamid = $(this).data('teamid');
    var notationid = $(this).val();

    var url = "<?=site_url('admin/standings/set_team_notation')?>";
    $.post(url,{'teamid':teamid, 'notationid':notationid}, function(data){
        notice('Setting saved.','success')
        console.log('updated teamid '+teamid+' with '+notationid);
    });


});
</script>
