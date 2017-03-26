<?php foreach($years as $i => $y): ?>
    <?php if ($y->year == $this->session->userdata('current_year')):?>
        <?php continue; ?>
    <?php elseif (isset($selected_year) && $y->year == $selected_year): ?>
        <?=$y->year?>
    <?php else:?>
        <a href="<?=site_url('admin/past_seasons/year/'.$y->year)?>"><?=$y->year?></a>
    <?php endif;?>
    <?php if ($i+1 != count($years)):?>
        |
    <?php endif;?>
<?php endforeach; ?>