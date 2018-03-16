<?php //print_r($types); ?>
<?php //echo $weeks; ?>

<div class="section">
    <div class="columns is-multiline">
        <div class="column">
            <div class="select is-fullwidth">
                <select id="team">
                    <?php foreach($teams as $t): ?>
                        <option value="<?=$t->team_id?>">
                            (<?=$t->first_name?>) <?=$t->team_name?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="column">
            <div class="select is-fullwidth">
                <select id="week">
                    <?php for($w=1; $w<=$weeks; $w++): ?>
                        <option value="<?=$w?>">Week <?=$w?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="column">
            <input id="amount" class="input" type="text" placeholder="0.00"></input>
        </div>
        <div class="column">
            <div class="select is-fullwidth">
                <select id="type">
                    <option value="0">Misc</option>
                    <?php foreach($types as $type): ?>
                        <option value="<?=$type->id?>"><?=$type->short_text?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="column">
            <input id="text" class="input" type="text" placeholder="Description"></input>
        </div>
    </div>
    <button id="add" class="button is-link">Add</button>


<br>


    <div class="columns">
        <div class="column">
            <table class="table is-fullwidth is-narrow fflp-table-fixed is-striped is-bordered">
                <thead>
                    <th>Week</th><th>Amount</th><th>Score</th><th>Type</th><th>Team</th><th>Owner</th>
                </thead>
                <tbody>
                    <?php foreach($list as $l): ?>
                        <tr>
                            <td><?=$l->week?></td>
                            <td>$<?=number_format($l->amount,2)?></td>
                            <td><?=$l->team_score?></td>
                            <?php if($l->text != ""):?>
                                <td><span data-tooltip class="has-tip top" title="<?=$l->text?>"><?=$l->short_text?></span></td>
                            <?php else: ?>
                                <td><?=$l->short_text?></td>
                            <?php endif;?>

                            <td><?=$l->team_name?></td>
                            <td style="font-size:.9em;"><?=$l->first_name.' '.$l->last_name?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
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
