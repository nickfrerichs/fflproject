<div class="container">
    <div id='teamlist'>
        <?php //print_r($roster); ?>
        <?php echo '<a href = "'.site_url().'admin/rosters/addplayer/'.$teamid.'/all">Add player </a>'; ?>
        <table class="table table-condensed table-striped">        
            <tr>
                <td>Player</td><td>Team</td><td>Position</td>
            </tr>
            <?php foreach ($roster as $player){ ?>
            <tr>
                <td><?php echo $player->short_name; ?></td>
                <td><?php echo $player->club_id; ?></td>
                <td><?php echo $player->position; ?></td>
                <td>
                    <a href='<?php echo site_url('admin/rosters/removeplayer/'.$teamid.'/'.$player->player_id); ?>'>remove</a>
                </td>
            </tr>


            <?php }?>
        </table>
    </div>
</div>
