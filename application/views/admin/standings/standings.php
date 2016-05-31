<div class="row">
    <div class="columns">
        <h5>Standings</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <?php //print_r($teams); ?>
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
                                    <option value="<?=$n->id?>"><?=$n->symbol.' ('.$n->text.')'?></option>
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
        console.log('updated teamid '+teamid+' with '+notationid);
    });


});
</script>
