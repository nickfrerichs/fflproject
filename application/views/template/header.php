<nav class="navbar is-dark">
    <div class="container is-fluid">
        <div class="navbar-brand">
            <div class="navbar-item">
                <span class="has-text-link is-size-4" >
                    <div class="title has-text-link is-size-5"><?=$this->session->userdata('site_name')?></div>
                    <div class="has-text-white is-size-6 subtitle" ><?=$this->session->userdata('league_name')?></div>
            </span>
            </div>

            <!-- <a class="navbar-item is-hidden-desktop" href="">
                <span class="icon" style="color: #333;">
                Live
                </span>
            </a> -->
        
            <!-- <button class="navbar-item is-hidden-desktop chat-button">
                <span class="icon" style="color: #333;">
                Chat
                </span>
            </button> -->
            
            <div id="fflp-navbar-burger" class="navbar-burger burger" data-target="navMenuTransparentExample">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div id="nav-menu" class="navbar-menu" style="padding:0px;">
            <div class="navbar-start">
                <div class="navbar-item is-hoverable livescores-link is-hidden" >
                    <a href="<?=site_url('season/scores/live')?>" style="color:#0CB805;}" class="livescore-link-text">Live</a>
                </div>
                <?php foreach($menu_items as $button => $subitem): ?>
                <div class="navbar-item has-dropdown is-hoverable">
                        <?php if(!is_array($subitem)): ?>
                        <a class="navbar-link" href="<?=$subitem?>">
                            <?=$button?>
                        </a>
                        <?php continue;?>
                        <?php endif;?>
                    <a class="navbar-link">
                        <?=$button?>
                    </a>
                    <div class="navbar-dropdown" style="border-top-width:0px;">
                        <?php foreach($subitem as $subtext => $url): ?>
                            <?php if($subtext == '_divider'): ?>
                                <hr class="navbar-divider">
                            <?php else: ?>
                            <a class="navbar-item" href="<?=$url?>">
                                <?=$subtext?>
                            </a>
                            <?php endif;?>
                        <?php endforeach;?>
                    </div>
                </div>
                <?php endforeach;?>

            </div>

            <div class="navbar-end">
            <?php if($this->session->userdata('league_id')): ?>
            <div class="navbar-item">
                  <button id="league-chat-button" class="button is-link chat-button">
                    <span>chat</span><span id="unread-chat-count"></span>
                  </button>
            </div>
            <?php endif;?>
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


<?php if(count($this->session->userdata('leagues')) > 0)
{
// Change League modal

    $body = '<div class="is-size-5 has-text-left">Select a league to switch to</div>';
    $body .= '<div class="field">';
        $body .= '<div class="control is-expanded"><div class="select is-fullwidth"><select id="change-league-select">';
        foreach($this->session->userdata('leagues') as $l_id => $l_name)
        {
            if ($this->session->userdata('league_id') != $l_id)
                $body .= '<option value="'.$l_id.'">'.$l_name.'</option>';
        }
        $body .= '</select></div></div></div>';
        $body .= '<div class="field"><div class="control"><button id="change-league-button" class="button is-link">Switch Leagues</button></div>';

    $body .= "</div><br>";


    $this->load->view('components/modal', array('id' => 'change-league-modal',
                                                        'title' => 'Change League',
                                                        'body' => $body,
                                                        'reload_on_close' => True));


}
?>

<?php if(count($this->session->userdata('leagues')) > 0):?>
<script>
        // Change League javascript
        function fflp_change_league(){$("#change-league-modal").addClass("is-active");}

        $('#change-league-button').on('click',function(){
            var url="<?=site_url('myteam/settings/ajax_change_current_league')?>";
            var leagueid = $("#change-league-select").val();
            $.post(url,{'leagueid' : leagueid},function(data)
            {
                $("#change-league-modal").removeClass('is-active');
                window.location.href = "<?=site_url()?>";
            });
        });
</script>
<?php endif;?>

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