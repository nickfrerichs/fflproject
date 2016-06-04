<div class="row">
    <div class="columns">
        <div>
            <a href="<?=site_url('admin/site/create_league')?>">Create new league</a>
        </div>
        <?php if(count($leagues) > 0): ?>
        <table class="table table-striped">
            <thead>
                <th>League Name</th>
                <th class="text-center">Teams</th>
                <th class="text-center">Active Teams</th>
                <th class="text-center">Admins</th>
                <th></th>
            </thead>
            <tbody>
                <?php foreach($leagues as $l):?>
                    <tr>
                        <td><?=$l['league']->league_name?></td>
                        <td class="text-center"><?=count($l['teams']);?></td>
                        <td class="text-center"><?=count($l['active_teams']);?></td>
                        <td class="text-center"><?=count($l['admins']);?></td>
                        <td>
                            <a href="<?=site_url('admin/site/manage_league/'.$l['league']->id)?>">Manage League</a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=5>
                            
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php endif;?>

        <?=debug($leagues,$this->session->userdata('debug'))?>
    </div>
</div>

<script>
</script>
