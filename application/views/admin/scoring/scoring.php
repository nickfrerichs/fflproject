<div><?php //print_r($values); ?></div>

<div class="row">
    <div class="columns">
        <h5>Scoring - <?=$this->session->userdata('current_year')?> Season</h5>
    </div>
</div>

<div class="row">
    <div class="columns">
        <span>(<a href="<?=site_url('admin/scoring/add')?>">Add More</a>)</span>
        <span>(<a href="<?=site_url('admin/scoring/edit')?>">Edit Values</a>)</span>
        <table class="table table-condensed table-striped">
            <th>Statistic</th><th>Position</th><th>Points</th><th>Per</th><th>Round dec.</th><th>delete</th>
            <?php foreach ($scoring_defs as $type_text => $type_def): ?>

            <tr><th colspan="6" class="text-uppercase"><?=$type_text?></th></tr>
            <?php foreach ($type_def as $pos => $pos_def):?>
            <?php foreach ($pos_def as $id => $def): ?>

            <tr>
                <td><?=$def['long_text']?></td>
                <td><?=$def['pos_text']?></td>
                <td><?=$def['points']?></td>
                <td><?=$def['per']?></td>
                <td><?php if ($def['round'] == 0){echo 'down';}else{echo 'up';} ?></td>
                <td><a href="<?=site_url('admin/scoring/delete/'.$id)?>">X</a>
            </tr>
            <?php endforeach;?>

            <?php endforeach;?>
                <?php endforeach;?>
        </table>
    </div>
</div>
