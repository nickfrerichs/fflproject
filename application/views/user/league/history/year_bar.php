<?php foreach($years as $i => $y): ?>
    <?php if ($y->year == $selected_year):?>
        <b><?=$y->year?></b>
    <?php else:?>
        <a href="<?=site_url('league/history/'.$section.'/'.$y->year)?>"><?=$y->year?></a>
    <?php endif;?>
    <?php if ($i+1 != count($years)):?>
        |
    <?php endif;?>
<?php endforeach; ?>
<br><br>