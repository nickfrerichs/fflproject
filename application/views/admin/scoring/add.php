<script>
function SetStatID(stat_id) {
    document.getElementsByName("stat_id").item(0).value = stat_id;
};
</script>

<div class="row">
    <div class="columns">
        <h5><?=$this->session->userdata('current_year')?> Season</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <a href="<?=site_url('admin/scoring')?>">Done</a>
        <table class="table table-condensed table-striped">
            <tr>
                <td>
                    <?php if($selected_pos == 0):?>
                        All
                    <?php else: ?>
                        <a href='<?=site_url('admin/scoring/add/0')?>'>All</a>
                    <?php endif; ?>
                    <?php foreach ($nfl_positions as $p): ?>
                        <?php if ($selected_pos == $p->id): ?>
                            <?=$p->text_id?>
                        <?php else: ?>
                            <a href='<?=site_url('admin/scoring/add/'.$p->id)?>'><?=$p->text_id?></a>
                        <?php endif; ?>
                    <?php endforeach;?>
                </td>
            </tr>

            <?php echo form_open();?>
            <?php echo form_hidden('stat_id','0'); ?>

            <?php $current_type = ""; ?>
            <?php foreach ($cats as $cat): ?>
            <?php if ($current_type != $cat->type_text): ?>

            <tr> <th colspan='3' height='50' class="text-uppercase"><strong><?=$cat->type_text?></strong></th></tr>

            <?php $current_type = $cat->type_text; ?>
            <?php endif; ?>
                <tr>
                    <td><?=$cat->long_text?></td>
                    <td><?=form_submit($cat->id,"Add",'onClick="SetStatID('.$cat->id.');"')?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
