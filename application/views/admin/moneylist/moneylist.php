<?php print_r($types); ?>
<?php //echo $weeks; ?>
<div class="container">
    <div class="row">
        <h4>Money List</h4>
        <table class="table" style="max-width:600px;">
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
            <input id="amount" type="textbox" placeholder="0.00"></input>
        </td>
        <td>
            <select id="type">
                <?php foreach($types as $type): ?>
                    <option value="<?=$type->id?>"><?=$type->short_text?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input id="text" type="textbox" placeholder="Description"></input>
        </td>
    </tr>
        </table>
        <button id="add" class="btn btn-default">Add</button>
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
        console.log(data);
        //location.reload();
    });
});
</script>
