

<?php if (count($result) > 0): ?>
    <div class="columns is-multiline">
        <?php $cur_date = "";?>
        <?php foreach($result as $i=>$w): ?>
            <?php if($cur_date != date('Y-m-d',$w->transaction_date)):?>
                <?php $cur_date = date('Y-m-d',$w->transaction_date);?>
                <div class="column is-12">
                    <br>
                    <h2 class="title is-size-5"><?=$cur_date?></h2>
                </div>
            <?php endif;?>
            <div class="column is-12">
                <div class="message">
                    <div class="message-header">
                    <div><b><?=date("M j g:i a",$w->transaction_date)?></b></div>
                    </div>
                    <div class="message-body">
                        <div class="columns is-desktop">
                            <div class="column has-text-weight-bold">
                                
                                <div>Owner: <?=$w->owner_first.' '.$w->owner_last?></div>
                                <div>Team: <?=$w->team_name?></div>
                            </div>
                            <div class="column">           
                                <div class="message is-success">
                                    <div class="message-body">
                                        <div class="columns is-mobile">
                                            <div class="column is-narrow">
                                                <?php if($w->pickup_photo != ""): ?>
                                                    <figure class="image is-64x64">
                                                        <img src="<?=site_url('images/'.$w->pickup_photo)?>">
                                                    </figure>
                                                <?php endif;?>
                                            </div>
                                            <div class="column">
                                                <b>Add <?=$w->pickup_pos?></b><br>
                                                <?php if($w->pickup_id): ?>
                                                    <?=$w->pickup_first.' '.$w->pickup_last?><br>
                                                    <?=$w->pickup_club_name?>
                                                <?php else:?>
                                                    No One 
                                                    <br><br>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="column">             
                                <div class="message is-danger">
                                    <div class="message-body">
                                        <div class="columns is-mobile">
                                            <div class="column is-narrow">
                                                <?php if($w->drop_photo != ""): ?>
                                                    <figure class="image is-64x64">
                                                        <img src="<?=site_url('images/'.$w->drop_photo)?>">
                                                    </figure>
                                                <?php endif;?>
                                            </div>
                                            <div class="column">
                                                <b>Drop <?=$w->drop_pos?></b><br>
                                                <?php if($w->drop_id): ?>
                                                    <?=$w->drop_first.' '.$w->drop_last?><br>
                                                    <?=$w->drop_club_name?>
                                                <?php else:?>
                                                    No One
                                                    <br><br>
                                                <?php endif;?>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
<?php else: ?>
<div>Nothing to report</div>
<?php endif;?>