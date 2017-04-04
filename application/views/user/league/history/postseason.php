<div class="row">
    <div class="columns text-center">
        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'postseason'));?>
        <h4><?=$selected_year?> Post Season</h4>
        <?php $this->load->view('user/season/postseason') ?>
    </div>
</div>