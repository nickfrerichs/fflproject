<nav class="navbar is-dark">
    <div class="container is-fluid">
        <div class="navbar-brand">
            <div class="navbar-item">
                <span class="has-text-link is-size-4" >
                    <div class="title has-text-link is-size-5">MyLanparty FFL</div>
                    <div class="has-text-white is-size-6 subtitle" >The FFL</div>
            </span>
            </div>

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
                    <div class="navbar-dropdown">
                        <?php foreach($subitem as $subtext => $url): ?>
                            <a class="navbar-item" href="<?=site_url($url)?>">
                                <?=$subtext?>
                            </a>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
            <div class="navbar-end">
            <div class="navbar-item">
                  <a class="button is-link" href="">
                    <span>chat</span>
                  </a>
            </div>
                <div class="navbar-item is-hoverable">
                    <span class="icon">
                    <a href="<?=site_url('auth/logout')?>">
                        <i class="fa fa-power-off has-text-light"></i>
                    </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</nav>


<script>
  $('.navbar-burger').on('click',function(){
    $('.navbar-dropdown').addClass('is-hidden-touch');
    $('.navbar-burger').toggleClass('is-active');
    $('.navbar-menu').toggleClass('is-active');
  });
$('.navbar-link').on('click',function(){
  $('.navbar-dropdown').not($(this).next('.navbar-dropdown')).addClass('is-hidden-touch');
  $(this).next('.navbar-dropdown').toggleClass('is-hidden-touch');
});

</script>