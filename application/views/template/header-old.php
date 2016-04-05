<div class="container">
    <div >
        <!-- <div class="site-heading">FFL Project</div> -->
        <nav class="navbar navbar-default" role="navigation">
                <div class="navbar-header">
                    <button type="button" data-target="#navbarCollapse" data-toggle="collapse" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    
                    <a href="#" class="navbar-brand"><span class="glyphicon glyphicon-home"></span></a>
                </div>
                <div id="navbarCollapse" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                    <?php foreach($menu_items as $button => $subitem): ?>
                        <li class="dropdown">
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#"><?=$button?><b class="caret"></b></a>
                            <ul role="menu" class="dropdown-menu">
                            <?php foreach($subitem as $subtext => $url): ?>
                                <li><a href="<?=site_url($url)?>"><?=$subtext?></a></li>
                            <?php endforeach; ?>
                            </ul>                  
                        </li>
               
                    <?php endforeach; ?>
                    </ul>
                </div>
        </nav>
    </div>
    <div class="hidden xs-nav-pad"></div>
    <div class="hidden">
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
