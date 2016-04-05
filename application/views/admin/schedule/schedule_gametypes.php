<?php
// $types
?>

<?php //print_r($types); ?>
<div class="container">
    <span class="page-heading">Game Types</span>
<table class="table table-condensed">
    <th>Game Type</th><th>default</th><th></th>
<?php foreach($types as $type): ?>
    <tr>
        <td><?=$type->text_id?></td>
        <td>
            <?php if ($type->default):?>
                default
            <?php else: ?>
                <a href='<?=site_url('admin/schedule/gametypes/default/'.$type->id)?>'>set</a>
            <?php endif;?>
        </td>
        <td><a href='<?=site_url('admin/schedule/gametypes/delete/'.$type->id)?>'>delete</a></td>
    </tr>
<?php endforeach; ?>
</table>

<p></p>
<div>
    New game type
    <?=form_open(current_url())?>
    <table class="table table-condensed">
        <tr>
            <td><?=form_input('text_id')?></td>
            <td><?=form_submit('add', 'Add type')?></td>
        </tr>
    </table>
<?=form_close()?>
</div>