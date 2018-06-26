<div class="section">

        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'postseason'));?>
        <div class="is-size-4"><?=$selected_year?> Post Season</div>
        <div class="content">
        <?php $this->load->view('user/season/postseason') ?>
        </div>

</div>