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
        
            <button class="navbar-item is-hidden-desktop chat-button">
                <span class="icon" style="color: #333;">
                Chat
                </span>
            </button>
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
                  <button id="league-chat-button" class="button is-link chat-button">
                    <span>chat</span><span id="unread-chat-count"></span>
                  </button>
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

<?php if($this->session->userdata('league_id')): ?>
<!-- Chat modal -->
<!-- <div class="modal" id="chat-modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">League Chat</p>
            <button class="delete modal-close-button" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <table>
                <tbody id="chat-history-ajax" class="chat-history-ajax">
                </tbody>
            </table>
        </section>
        <footer class="modal-card-foot">
        <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
        </footer>
    </div>
</div> -->


<div id="chat-modal" hidden>
    <div id="chat-history-table" class="chat-history-table">
        <table class="table is-fullwidth is-narrow is-striped">
            <tbody id="chat-history-ajax" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message" class="textarea" rows="2" placeholder="You put your trash talk in here..." autofocus></textarea>
    </div>
</div>
<?php endif;?>

 <!-- <span onclick="myModal.open();">Open jBox!</span>
 <span onclick="myModal.close();">Close jBox!</span>
 <span onclick="myModal.toggle();">Toggle jBox!</span> -->

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

// $('#league-chat-button').on('click',function(){
//     $("#chat-modal").addClass('is-active');
// });

var chatModal =   new jBox('Modal', {
    attach: $('#modal-drag-on-title'),
    width: 220,
    title: 'jBox',
    overlay: false,
    content: 'Drag me around by using the title',
    draggable: 'title',
    repositionOnOpen: false,
    repositionOnContent: false
});
 

<?php if($this->session->userdata('league_id')): ?>
// Start up an SSE stream to make things live
sse_stream_start();
<?php endif;?>

</script>