<div class="row">
    <div class="columns">
        <h5>Add a division</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <table>
        <?=form_open(current_url())?>
            <tr>
                <td><?=form_label('Division Name','name')?></td>
                <td><?=form_input('name')?></td>
            </tr>
            <tr>
                <td colspan=2>
                    <?=form_submit('add','Add')?>
                </td>
            </tr>

        <?=form_close()?>
        </table>
        <p></p>
        <h5>Divisions</h5>
        <table>
            <?php foreach ($divisions as $div): ?>
            <tr>
                <td><?=$div->name?></td>
                <td><a href="<?=site_url('admin/divisions/delete/'.$div->id)?>">delete</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>