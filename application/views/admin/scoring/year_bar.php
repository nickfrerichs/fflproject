<?php foreach($years as $i => $y): ?>
    <?php if (isset($selected_year) && $y->year == $selected_year): ?>
        <?=$y->year?>
    <?php else:?>
        <a href="<?=site_url('admin/scoring/year/'.$y->year)?>"><?=$y->year?></a>
    <?php endif;?>
    <?php if ($i+1 != count($years)):?>
        |
    <?php endif;?>
<?php endforeach; ?>