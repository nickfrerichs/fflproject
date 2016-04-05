<div class="container">
<span class="table-heading"><?php echo $leaguename; ?></span>

    <table class="table table-condensed">
        <tr>
            <th>Team</th><th>Roster</th><th>Division</th><th>Owner</th>
        </tr>
        <?php foreach ($teams as $team){ ?>

        <tr>
            <td><a href=<?php echo site_url().'admin/teams/show/'.$team->id.'>'.$team->team_name; ?></a></td>
            <td><a href="<?=site_url('admin/rosters/view/'.$team->id)?>">Edit Roster</td>
            <td>Division</td>
            <td><?php echo $team->first_name.' '.$team->last_name; ?></td>

        </tr>
        <?php }?>
    </table>
</div>