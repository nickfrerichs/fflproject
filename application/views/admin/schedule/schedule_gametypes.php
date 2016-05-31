
<div class="row">
    <div class="columns">
        <h5>Game Types</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <table>
            <th class="text-left">Game Type</th><th class="text-left">default</th><th></th>
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
    </div>
</div>

<div class="row">
    <div class="columns">
            New game type
            <?=form_open(current_url())?>
            <table>
                <tr>
                    <td><?=form_input('text_id')?></td>
                    <td><?=form_submit('add', 'Add type')?></td>
                </tr>
            </table>
        <?=form_close()?>
    </div>
</div>
