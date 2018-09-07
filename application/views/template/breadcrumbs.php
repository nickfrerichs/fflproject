

<div class="column">
    <div class="level">
        <div class="level-left">
            <?php if ($bc): ?>
                <nav class="breadcrumb has-succeeds-separator" style="font-size:.75em">
                    <ul>
                    <?php foreach($bc as $text => $url): ?>
                        <?php if($url != ""):?>
                        <li><a href="<?=$url?>"><?=$text?></a></li>
                        <?php else: ?>
                        <li class="is-active"><a href=""><?=$text?></a></li>
                        <?php endif; ?>
                    <?php endforeach;?>
                    </ul>
                </nav>
            <?php endif;?>
        </div>
        <div class="level-right is-hidden-mobile" style="font-size:.75em">
            <div class="level-item has-text-link">
                <span id="whos-online"></span>
            </div>
        </div>
    </div>
</div>






<!-- <div style="border-bottom-style:solid; border-color:#3273dc; border-width:2px;"></div> -->


