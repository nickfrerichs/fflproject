<?php if(count($news) == 0): ?>
    There is no news.
    <br>
    <br>
<?php else: ?>


    <?php foreach($news as $n): ?>
    <div class="message">
        <div class="message-header">
            <div><?=date("M j g:i a",$n->date_posted)?></div>
        </div>
        <div class="message-body">
            <div class="title is-size-4"><?=$n->title?></div>
            <div class="content">
                <?=$n->data?>
            </div>
        </div>
    </div>
    <?php endforeach;?>

<?php endif;?>