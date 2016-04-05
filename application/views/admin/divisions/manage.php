<div class="container">
    <div class="table-heading">Add a division</div>
    <table class="table table-condensed">
    <?=form_open(current_url())?>
        <tr>
            <td><?=form_label('Division Name','name')?></td>
            <td><?=form_input('name')?></td>
        </tr>
        <tr>
            <td>
                <?=form_submit('add','Add')?>
            </td>
        </tr>
    <?=form_close()?>
    </table>
    <p></p>
    <span class="table-heading">Divisions</span>
    <table class="table table-condensed">
        <?php foreach ($divisions as $div): ?>
        <tr>
            <td><?=$div->name?></td>
            <td><a href="<?=site_url('admin/divisions/delete/'.$div->id)?>">delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>