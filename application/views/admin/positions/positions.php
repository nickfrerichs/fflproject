<div class="container">
    <div class="page-heading"> League Positions </div>
    <a href='<?php echo site_url('admin/positions/add'); ?>'>Add Position</a>

    <table class="table">
        <tr><th>Name</th><th>Position</th><th>NFL Pos</th><th>Roster Max</th><th>Roster Min</th>
            <th>Start Max</th><th>Start Min</th><th></th></tr>
    <?php foreach($league_positions as $lp){ ?>
        
        <tr>
            
            <td><?php echo $lp->long_text; ?></td>
            <td><?php echo $lp->text_id;?></td>
            <td>
                <?php foreach(explode(",",$lp->nfl_position_id_list) as $nfl_id){
                    //echo $nfl_id." ";
                    echo $nfl_positions[$nfl_id]." ";
                }?>
            </td>
            <td><?php echo $lp->max_roster;?></td>
            <td><?php echo $lp->min_roster;?></td>
            <td><?php echo $lp->max_start;?></td>
            <td><?php echo $lp->min_start;?></td>
            <td>
                <a href='<?php echo site_url('admin/positions/edit/'.$lp->id); ?>'>edit</a> 
                <a href='<?php echo site_url('admin/positions/delete/'.$lp->id); ?>'>delete</a>
            </td>
        </tr>
    <?php }?>
    </table>
</div>