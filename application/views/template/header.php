<nav class="navbar is-link">
    <div class="container">
        <div class="navbar-brand">
            <a class="navbar-item" href="">
                <b>MyLanparty FFL</b>
            </a>
            <a class="navbar-item is-hidden-desktop" href="">
                <span class="icon" style="color: #333;">
                Live
                </span>
            </a>
        
            <a class="navbar-item is-hidden-desktop" href="">
                <span class="icon" style="color: #333;">
                Chat
                </span>
            </a>
            <div class="navbar-burger burger" data-target="navMenuTransparentExample">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    
      
        <div id="nav-menu" class="navbar-menu">
            <div class="navbar-start">
                <?php foreach($menu_items as $button => $subitem): ?>
                <div class="navbar-item has-dropdown is-hoverable">
                        <?php if(!is_array($subitem)): ?>
                        <a class="navbar-link" href="<?=site_url($subitem)?>">
                            <?=$button?>
                        </a>
                        <?php continue;?>
                        <?php endif;?>
                    <a class="navbar-link">
                        <?=$button?>
                    </a>
                    <div class="navbar-dropdown is-boxed">
                        <?php foreach($subitem as $subtext => $url): ?>
                            <a class="navbar-item" href="<?=site_url($url)?>">
                                <?=$subtext?>
                            </a>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</nav>