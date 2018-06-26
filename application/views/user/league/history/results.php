<style>
.leaguetitle{
    padding-bottom: 20px;
}
</style>
<?php //print_r($other_titles); ?>
<div class="section">
        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'results'));?>
        <br><br>
        <div class="is-size-4"><?=$year?> Season</div>
        <br>
        <?php if(count($title_games)>0 || count($other_titles) > 0): ?>

        <div class="columns is-multiline">
            <?php foreach($title_games as $title): ?>
            <div class="column is-half-tablet leaguetitle">
                <b><?=$title['data']->title_text?></b><br>
                <?=$title['team_name']?>
            </div>
            <?php endforeach; ?>
            <?php foreach($other_titles as $other): ?>
                <div class="column is-half-tablet leaguetitle">
                    <b><?=$other->text?></b><br>
                    <?=$other->team_name?>
                </div>
            <?php endforeach;?>
        </div>

        <?php endif;?>
        <?php $this->load->view('user/season/standings.php'); ?>

</div>