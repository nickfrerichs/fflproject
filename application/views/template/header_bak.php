
<div class="container">
    <div class="hidden-xs ">
        <div class="site-heading"> FFL Project</div>
        <div class="btn-group btn-group-justified">
            <?php $count = 1; ?>
            <?php foreach($menu_items as $button => $subitem): ?>
            <div class="btn-group ">
                <button class="btn header-btn btn-default dropdown-toggle" type="button" id="lg-dropdown<?=$count?>" data-toggle="dropdown"><?=$button?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu header-menu " role="menu" aria-labelledby="lg-dropdown<?=$count?>">
                    <?php foreach($subitem as $subtext => $url): ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="<?=site_url($url)?>"><?=$subtext?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php $count++; ?>   
            </div>   
            <?php endforeach; ?>   
        </div>
    </div>
    <div class="visible-xs xs-nav-pad"></div>
    <div class="visible-xs">
        <div class="btn-group btn-group-justified header-menu-xs">
            <?php $count = 1; ?>
            <?php foreach($menu_items as $button => $subitem): ?>
            <div class="btn-group">
                <button class="btn header-btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><?=$button?>
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu menu-dropdown-xs" role="menu">
                    <?php foreach($subitem as $subtext => $url): ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="<?=site_url($url)?>"><?=$subtext?></a></li>
                    <?php endforeach; ?>
                </ul>
            <?php $count++; ?>   
            </div>   
            <?php endforeach; ?>   
        </div>
    </div>
</div>
