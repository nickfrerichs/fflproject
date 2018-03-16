
<div class="section">
    <div class="columns">
        <div class="column">
            <table class="table is-striped is-fullwidth is-narrow fflp-table-fixed">
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
                                <div class="select">
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
                                </div>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
            <a href="<?=site_url('admin/standings/notations')?>">View/Edit Notations</a>
        </div>
    </div>
</div>

<script>
$(".team-notation").on('change',function(){
    var teamid = $(this).data('teamid');
    var notationid = $(this).val();

    var url = "<?=site_url('admin/standings/set_team_notation')?>";
    $.post(url,{'teamid':teamid, 'notationid':notationid}, function(data){
        //notice('Setting saved.','success')
        console.log('updated teamid '+teamid+' with '+notationid);
    });


});
</script>
