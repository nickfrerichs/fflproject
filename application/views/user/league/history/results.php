<style>
.leaguetitle{
    padding-bottom: 20px;
}
</style>
<?php //print_r($other_titles); ?>
<div class="row">
    <div class="columns text-center">
        <?php $this->load->view('user/league/history/year_bar.php', array('section' => 'results'));?>
        <br><br>
        <h4><?=$year?> Season</h4>
        <?php if(count($title_games)>0 || count($other_titles) > 0): ?>
            <div class="row">
                <div class="columns callout">
                    <div class="row">
                        <?php foreach($title_games as $title): ?>
                        <div class="columns medium-6 leaguetitle">
                            <b><?=$title['data']->title_text?></b><br>
                            <?=$title['team_name']?>
                        </div>
                        <?php endforeach; ?>
                        <?php foreach($other_titles as $other): ?>
                            <div class="columns medium-6 leaguetitle">
                                <b><?=$other->text?></b><br>
                                <?=$other->team_name?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <?php $this->load->view('user/season/standings.php'); ?>
    </div>
</div>