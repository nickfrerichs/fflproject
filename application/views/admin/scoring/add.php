<!-- <script>
function SetStatID(stat_id) {
    document.getElementsByName("stat_id").item(0).value = stat_id;
};
</script> -->

<div class="section">

        <div class="is-size-5"><?=$selected_year?> Season</div>

        <br>
        <div class="is-size-5"><a href="<?=site_url('admin/scoring')?>">Done</a></div>
        <br>
        <table class="table is-fullwidth is-narrow is-striped">
            <tr>
                <td>
                    <?php if($selected_pos == 0):?>
                        All
                    <?php else: ?>
                        <a href='<?=site_url('admin/scoring/add/'.$selected_year)?>'>All</a>
                    <?php endif; ?>
                    <?php foreach ($nfl_positions as $p): ?>
                        <?php if ($selected_pos == $p->id): ?>
                            <?=$p->text_id?>
                        <?php else: ?>
                            <a href='<?=site_url('admin/scoring/add/'.$selected_year.'/'.$p->id)?>'><?=$p->text_id?></a>
                        <?php endif; ?>
                    <?php endforeach;?>
                </td>
            </tr>

            <?php $current_type = ""; ?>
            <?php foreach ($cats as $cat): ?>
            <?php if ($current_type != $cat->type_text): ?>

            <tr> <th colspan='3' height='50' class="text-uppercase"><strong><?=$cat->type_text?></strong></th></tr>

            <?php $current_type = $cat->type_text; ?>
            <?php endif; ?>
                <tr>
                    <td><?=$cat->long_text?></td>
                    <td>
                        <button class="button is-link is-small add-cat" data-cat-id="<?=$cat->id?>" data-is-range="0">Per unit</button>
                        <!-- <input class="button is-link is-small" type="submit" name="type" value="Per unit"  onClick="SetStatID(<?=$cat->id?>);" /> -->
                    </td>
                    <td>
                        <button class="button is-link is-small add-cat" data-cat-id="<?=$cat->id?>" data-is-range="1">Unit range</button>
                        <!-- <input class="button is-link is-small" type="submit" name="type" value="Unit range"  onClick="SetStatID(<?=$cat->id?>);" /> -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

</div>

<script>
$('.add-cat').one('click',function(){
    var url = "<?=site_url('admin/scoring/ajax_add_scoring_def')?>"
    var cat_id = $(this).data('cat-id');
    var is_range = $(this).data('is-range');
    var year = "<?=$selected_year?>";
    var pos_id = "<?=$selected_pos?>";
    $.post(url,{'cat_id':cat_id, 'is_range':is_range, 'year':year, 'pos_id':pos_id},function(data){
        console.log(data);
        if (data.success)
        {
            location.reload();
        }
    },'json');
});
</script>