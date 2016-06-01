<?php //print_r($types); ?>
<?php //echo $weeks; ?>

<div class="row">
    <div class="columns">
        <table>
            <tr><td>
        <select id="team">
            <?php foreach($teams as $t): ?>
                <option value="<?=$t->team_id?>">
                    (<?=$t->first_name?>) <?=$t->team_name?>
                </option>
            <?php endforeach;?>
        </select>
        </td>
        <td>
            <select id="week">
                <?php for($w=1; $w<=$weeks; $w++): ?>
                    <option value="<?=$w?>">Week <?=$w?></option>
                <?php endfor; ?>
            </select>
        </td>
        <td>
            <input id="amount" type="text" placeholder="0.00"></input>
        </td>
        <td>
            <select id="type">
                <?php foreach($types as $type): ?>
                    <option value="<?=$type->id?>"><?=$type->short_text?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input id="text" type="text" placeholder="Description"></input>
        </td>
    </tr>
        </table>
        <button id="add" class="button small">Add</button>
    </div>
</div>
<br>
<div class="row">
    <div class="columns">
        <table>
            <thead>
                <th>Week</th><th>Amount</th><th>Score</th><th></th><th>Team</th><th>Owner</th>
            </thead>
            <tbody>
                <?php foreach($list as $l): ?>
                    <tr>
                        <td><?=$l->week?></td>
                        <td>$<?=number_format($l->amount,2)?></td>
                        <td><?=$l->team_score?></td>
                        <td><?=$l->short_text?></td>
                        <td><?=$l->team_name?></td>
                        <td style="font-size:.9em;"><?=$l->first_name.' '.$l->last_name?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$('#add').on('click', function(){
    var url = "<?=site_url('admin/moneylist/ajax_add')?>";
    var teamid = $('#team').val();
    var week = $('#week').val();
    var amount = $('#amount').val();
    var text = $('#text').val();
    var typeid = $('#type').val();
    $.post(url,{'teamid':teamid, 'week':week, 'amount':amount, 'text':text, 'typeid':typeid}, function(data){
        location.reload();
    });
});
</script>
