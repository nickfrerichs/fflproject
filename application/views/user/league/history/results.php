<?php print_r($titles); ?>
<div class="row">
    <div class="columns text-center">
        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'results'));?>
        <div class="row">
            <div class="columns callout">
                <h5><?=$selected_year?> Titles</h5>
            </div>
        </div>
        <?php $this->load->view('user/season/standings.php'); ?>
    </div>
</div>