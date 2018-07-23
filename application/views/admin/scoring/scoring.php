<div><?php //print_r($scoring_defs); ?></div>

<div class="section">
    <?php $this->load->view('admin/scoring/year_bar.php');?>
    <br><br>

        <div class="is-size-5"><?=$selected_year?> Season</div>
        <br>
        Scoring definition year range: <?=$def_range['end']?> - <?php if($def_range['start'] == 0){echo "League Origin";}else{echo $def_range['start'];}?>
        <br><br>


        <span>(<a href="<?=site_url('admin/scoring/add/'.$selected_year)?>">Add More</a>)</span>
        <span>(<a href="<?=site_url('admin/scoring/edit/'.$selected_year)?>">Edit Values</a>)</span>
        <table class="table is-fullwidth is-narrow is-striped">
            <th class="text-left">Statistic</th><th>Position</th><th>Points</th><th>Per</th><th>Round</th><th>Range Start</th><th>Range End</th><th>delete</th>
            <?php foreach ($scoring_defs as $type_text => $type_def): ?>

            <tr><th colspan="8" class="text-uppercase"><?=$type_text?></th></tr>
            <?php foreach ($type_def as $pos => $pos_def):?>
            <?php foreach ($pos_def as $id => $def): ?>

            <tr>
                <td class="text-left">
                    <?=$def['long_text']?>
                    <?php if($def['is_range']): ?>
                        <br><a href="#" class="add-range" data-catid="<?=$def['cat_id']?>" data-posid="<?=$def['pos_id']?>">Add another range</a>
                    <?php endif;?>
                </td>
                <td><?php if($def['pos_text'] == ""){echo "All";}else{echo $def['pos_text'];}?></td>
                <td><?=$def['points']?></td>
                <td><?php if(!$def['is_range']){echo $def['per'];}?></td>
                <td><?php if ($def['round'] == 0){echo 'down';}else{echo 'up';} ?></td>
                <td><?php if($def['is_range']){echo $def['range_start'];}?></td>
                <td><?php if($def['is_range']){echo $def['range_end'];}?></td>
                <td><a href="<?=site_url('admin/scoring/delete/'.$selected_year.'/'.$id)?>">X</a>
            </tr>
            <?php endforeach;?>

            <?php endforeach;?>
                <?php endforeach;?>
        </table>
        <span style="font-style:italic">Use this URL to display to your league (ex: in League Rules): </span><?=site_url('league/rules/scoring')?>

</div>

<script>
$(".add-range").on("click",function(){
    var year = "<?=$selected_year?>";
    var url='<?=site_url("admin/scoring/ajax_add_scoring_def")?>';
    $.post(url,{"cat_id":$(this).data("catid"),"is_range":"1",'year':year,'pos_id':$(this).data('posid')},function(data){
        if (data.success)
        {
            location.reload();
        }
    },'json');
    //console.log($(this).data("posid"));
});
</script>
