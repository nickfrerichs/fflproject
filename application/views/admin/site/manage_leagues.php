<div class="container">
    <div class="row">
        <h4>Manage Leagues</h4>
        <div>
            <a href="<?=site_url('admin/site/create_league')?>">Create new league</a>
        </div>
        <?php if(count($leagues) > 0): ?>
        <table class="table table-striped">
            <thead>
                <th>League Name</th>
                <th>Teams</th>
                <th>Active Teams</th>
                <th>Admins</th>
                <th></th>
            </thead>
            <tbody>
                <?php foreach($leagues as $l):?>
                    <tr>
                        <td><?=$l['league']->league_name?></td>
                        <td></td>
                        <td></td>
                        <td>
                            <?php foreach($l['admins'] as $a): ?>
                                <?=$a->username?>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <a href="<?=site_url('admin/site/edit_league/'.$l['league']->id)?>">Edit League</a>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php endif;?>

    </div>
</div>

<script>
</script>
