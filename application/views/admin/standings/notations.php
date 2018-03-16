
<div class="section">
    <div class="columns is-centered">
        <div class="column">
            <div class="container fflp-med-container">
        <table class="table is-fullwidth">
            <thead>
                <th>Symbol</th><th>Text</th><th></th><th></th>
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
        </div>
    </div>
</div>
