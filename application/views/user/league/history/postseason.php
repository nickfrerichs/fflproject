<div class="section">
        <div class="container">

                <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'postseason'));?>
                <div class="title"><?=$selected_year?> Post Season</div>
                <div class="f-scrollbar">
                        <div class="content">
                        <?php $this->load->view('user/season/postseason') ?>
                        </div>
                </div>
        </div>
</div>