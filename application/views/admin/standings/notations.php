<div class="container">
    <div class="row">
        <h4>Standings</h4>
    </div>

    <table class="table" style="max-width:400px;">
        <thead>
            <th>Symbol</th><th>Text</th>
        </thead>
        <?php foreach($defs as $def): ?>
            <tr>
                <td>
                    <?=$def->symbol?>
                </td>
                <td>
                    <?=$def->text?>
                </td>
                <td>
                    <a href="<?=site_url('admin/standings/notations/edit/'.$def->id)?>">Edit</a>
                </td>
                <td>
                    <a href="<?=site_url('admin/standings/notations/delete/'.$def->id)?>">Delete</a>
                </td>
            </tr>
        <?php endforeach;?>
    </table>

    <a href="<?=site_url('admin/standings/notations/add')?>">Add Notation</a>

</div>
