
<div class="section">
    <div class="container">
        <?php if(isset($content)): ?>
            <?php if(isset($content->title)):?>
            <div class="title"><?=$content->title?></div>
            <?php endif;?>
            <?php if(isset($content->title)):?>
            <div class="content">
                <?=$content->data?>
            </div>
            <?php endif; ?>
        <?php else: ?>
        <div class="is-size-5">Post season is not available yet.</div>

        <?php endif;?>
    </div>
</div>
