<div class="section">
    <div class="container">
            <?php if(count($leagues) > 0): ?>
                <table class="table is-fullwidth is-striped">
                    <thead>
                        <th>League Name</th>
                        <th class="text-center">Teams</th>
                        <th class="text-center">Active Teams</th>
                        <th class="text-center">League Admins</th>
                        <th></th>
                    </thead>
                    <tbody>
                        <?php foreach($leagues as $l):?>
                            <tr>
                                <td><?=$l['league']->league_name?></td>
                                <td class="text-center"><?=count($l['teams']);?></td>
                                <td class="text-center"><?=count($l['active_teams']);?></td>
                                <td class="text-center"><span
                                    <?php if (count($l['admins']) == 0): ?>
                                        style = "color:red; font-weight:bold"
                                    <?php endif;?>
                                    ><?=count($l['admins']);?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?=site_url('admin/site/manage_league/'.$l['league']->id)?>">Manage League</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
                <a href="<?=site_url('admin/site/create_league')?>" class="button is-link">Create new league</a>

            <?php else:?>
                No leagues currently exist.
            <?php endif;?>

    </div>
</div>

<script>
</script>
