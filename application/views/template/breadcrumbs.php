<?php if ($bc): ?>
<div class="row">
    <div class="columns small-12 medium-11">
        <nav aria-label="You are here:" role="navigation">
            <ul class="breadcrumbs">
            <?php foreach($bc as $text => $url): ?>
                <?php if($url != ""):?>
                <li><a href="<?=$url?>"><?=$text?></a></li>
                <?php else: ?>
                <li><?=$text?></li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </nav>
    </div>
<!--
    <div class="columns small-12 medium-1">
        help
    </div>
-->
</div>
<?php endif;?>
