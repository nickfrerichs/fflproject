
<?php //print_r($positions); ?>
<?php echo form_open(site_url('admin/rosters/addplayer/'.$teamid)); $options = array(0 => 'All');?>

<div class="container">
    <div>
    <?php foreach($positions as $p){
        $options[$p->id] = $p->text_id;}
        echo form_dropdown('selected_pos',$options,$this->session->userdata('psearch_pos'));
        echo form_submit('select','Select');
        echo form_close();
        ?>
    </div>
    <?php echo $links; ?>
    <table class="table table-condensed table-striped">
        <td>Name</td><td>Position</td><td>Team</td><td></td>
    <?php foreach($players as $player){ ?>
        <tr>
            <td><?php echo $player->last_name.' '.$player->first_name; ?></td>
            <td><?php echo $player->position; ?></td>
            <td><?php echo $player->club_id; ?></td>
            <td><a href='<?php echo site_url('admin/rosters/doaddplayer/'.$teamid.'/'.$player->id) ?>'>add </a></td>
                
        </tr>
    <?php } ?>
    </table>
</div>