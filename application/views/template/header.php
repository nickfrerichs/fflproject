<!-- Small menu bar for mobile devices -->
<div class="show-for-small-only small-top-bar row">
    <div class="title-bar small-9 columns align-left row" data-responsive-toggle="main-menu">
            <button class="menu-icon" type="button" data-toggle></button>
            <?php if($this->session->userdata('league_id')): ?>
                <?php if($this->session->userdata('live_scores')){$live="";$title="hide";}else{$live="hide";$title="";}?>
                <span class="<?=$live?>">
                    <a class="live-scores" href="<?=site_url('season/scores/live')?>">LIVE SCORES</a>
                </span>
                <span class="<?=$title?>">
                    <?=$this->session->userdata('site_name')?>
                </span>
            <?php endif;?>
    </div>
    <div class="small-3 columns text-right">
      <?php if($this->session->userdata('league_id')): ?>
         <button id="chat-button-small" class="button chat-button">chat</button>
      <?php endif;?>

    </div>
</div>

<!-- Normal menu bar for normal computers -->
<div id="title-bar-row" class="row align-spaced align-middle">
    <div class="columns">
        <div class="row align-left align-middle">
            <!-- At one point, I was going to put in a logo, this is where it was.
            <div id="site-logo" class="columns show-for-medium shrink">

            </div>
            -->
            <div id="league-site-names" class="columns shrink">
                <div class="site-title hide-for-small-only hide-for-small-custom"><?=$this->session->userdata('site_name')?></div>
                <div class="league-name-title hide-for-small-only hide-for-small-custom"><?=$this->session->userdata('league_name')?></div>

                    <?php if($this->session->userdata('league_id')): ?>
                        <?php if($this->session->userdata('live_scores')){$live="";}else{$live="hide";}?>
                        <span class="hide-for-small-only">
                            <a class="live-scores <?=$live?>" href="<?=site_url('season/scores/live')?>">LIVE SCORES</a>
                        </span>
                    <?php endif;?>
            </div>
        </div>
    </div>

    <div class="top-bar columns shrink" id="main-menu">
        <ul class="menu show-for-small-only site-title-small"><?=$this->session->userdata('league_name')?></ul>
        <ul class="dropdown menu medium-horizontal drilldown vertical" data-responsive-menu="drilldown medium-dropdown">
            <?php foreach($menu_items as $button => $subitem): ?>
                <?php if (!is_array($subitem)): ?>
                    <li><a href="<?=site_url($subitem)?>"><?=$button?></a></li>
                <?php continue;?>
                <?php endif;?>
              <li><a href="#"><?=$button?></a>
                  <ul class="menu vertical">
                  <?php foreach($subitem as $subtext => $url): ?>
                      <li><a href="<?=site_url($url)?>"><?=$subtext?></a></li>
                  <?php endforeach; ?>
                  </ul>
              </li>
            <?php endforeach; ?>
            <li class="show-for-small-only">
                <a href="<?=site_url('auth/logout')?>">Logoff</a>
            </li>
        </ul>
    </div>
    <div class="align-right columns shrink">
        <?php if($this->session->userdata('league_id')): ?>
                <button id="chat-button" class="button chat-button show-for-medium">chat<span class="unread-count"></span></button>
        <?php endif;?>
    </div>
    <div class="hide-for-small-only">
        <a href="<?=site_url('auth/logout')?>"><i class="fi-power columns"></i></a>
    </div>
</div>

<?php if($this->session->userdata('league_id')): ?>

<div id="chat-modal" hidden>
    <div id="chat-history-table" class="chat-history-table">
        <table>
            <tbody id="chat-history-ajax" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message" rows="1" placeholder="You put your trash talk in here..."></textarea>
    </div>
</div>
<?php endif;?>

<div id="livedata" class="hide">
</div>

<script>

<?php if($this->session->userdata('league_id')): ?>
//
// CHAT BOX STUFF, only load if league_id is set
//
$(function() {
    // Check every Xs to see some things were updated.
    setInterval(function(){updateLiveElements();}, 7000);
    updateLiveElements();
});

$(".chat-button").on('click', function(){

    if (typeof(evtSource) == "undefined")
    {
        $("#chat-history-ajax").html("<i>Loading...</i>");
        // Erase unread count on chat-button...we just read them.
        $(".unread-count").text("");

        // Start getting a stream of data so we can know if new chats came in.
        evtSource = new EventSource("<?=site_url('league/chat/stream_get_chat_key')?>");
        evtSource.onmessage = function(e)
        {
            // Check every heartbeat, if the stored chat key does not equal the new one, we need to update
            if(($("#chat-modal").data('chat-key') != e.data) || ($("#chat-modal").data('chat-on') != true))
            {updateChat(e.data);}
        }

        // Create the jBox from the chat-modal element, then open it.
        // onClose, run the closechat() function
        cb = new jBox('Modal',{
            content: $("#chat-modal"),
            blockScroll: false,
            draggable: 'title',
            overlay: false,
            title: "League Chat",
            addClass: 'jBox-chat',
            position: {x: 'right', y:'bottom'},
            onClose: function() {
                closechat();
            }
        });
        cb.open();

        // Set the focus to the textarea.. may change to only do this on large displays.
        $("#chat-message").focus();
    }
    else
    {
        // Chat button was clicked and the evtSource was already active..close the jBox
        cb.close();
    }
});

function closechat()
{
    if (typeof(evtSource) != "undefined")
    {
        evtSource.close();
        delete evtSource;
        $("#chat-modal").data('chat-on',false);
    }
    if (Foundation.MediaQuery.current == 'small')
    {$(window).scrollTop(0);}
}

// Post a new message to chat as long as there is non-whitespace in the textarea and you didn't press shift+enter
$('#chat-message').keypress(function(event){
    if(event.keyCode == 13 && !event.shiftKey){
        if ($(this).val().trim() == "") {event.preventDefault(); return}
        var url = "<?=site_url('league/chat/post')?>";
        $.post(url,{'message' : $(this).val()}, function(){
            $("#chat-message").val('');
            chatScrollBottom(true);
        });
        event.preventDefault();
    }

});

function updateChat(new_chat_key)
{
    //var chatwindow = $("#chat-window"+$(this).attr('id').replace('chat-button',''));
    //var chathistory = $("#chat-history-ajax"+$(this).attr('id').replace('chat-button',''));
    chat_key = $("#chat-modal").data('chat-key');

    var url = "<?=site_url('league/chat/get_messages')?>";
    // If chat-on != true, get all messages, set chat-on = true, update stored chat-key
    if ($("#chat-modal").data('chat-on') != true)
    {
        $.post(url,{}, function(data){
            $("#chat-modal").data('chat-on',true);
            $(".chat-history-ajax").html(data);
            $(".chat-history-table").each(function(){$(this).scrollTop($(this).prop('scrollHeight'));});
        });
    }
    else if (chat_key != new_chat_key)
    {
        // Else If chat-key != chat_key, get all messages newer than current chat-key and append
        $.post(url,{'chat_key':chat_key}, function(data){
            bottom = chatScrollBottom();
            $(".chat-history-ajax").append(data)
            if(bottom){chatScrollBottom(true);} // If we're at the bottom, snap back to the bottom
        });
    }
    $("#chat-modal").data('chat-key',new_chat_key);

}

// return true if scrolled to bottom, false if not
// if true passed in, set scroll to bottom
function chatScrollBottom(set)
{
    // Return true if the currently active chat is near the bottom, false if not.
    if(set == undefined)
    {
        var chat_history_table_id = "#chat-history-table";
        var h = $(chat_history_table_id).height()+$(chat_history_table_id).scrollTop();
        if (h > $(chat_history_table_id).prop('scrollHeight')-25)
        {return true;}
        return false;
    }
    else
    {
        $("#chat-history-table").scrollTop($("#chat-history-table").prop('scrollHeight'));
    }
}

// Function to update the new chat count on the chat button.
// It only checks if the Chat window is not visible
function updateLiveElements()
{
    var last_check_in = $("#livedata").data("last_check_in");
    var chat_key = $("#livedata").data("chat_key");
    var url = "<?=site_url('common/liveElements')?>";
    if (last_check_in !== undefined){url +=("/"+last_check_in);}

    $.post(url,{'last_chat_key':chat_key},function(data){
        <?php if($this->session->userdata('debug')): ?>
            console.log(data);
        <?php endif; ?>
        d = $.parseJSON(data);

        if (parseInt(d.T) > 1)
        {
            $("#livedata").data("last_check_in",d.T);

            // Update unread message count
            if (parseInt(d.ur) > 0)
                {$(".unread-count").text(" ("+d.ur+")");}
            else{$(".unread-count").text("");}
            // Update live scores (this might go away)
            if (d.ls == "1")
                {$(".live-scores").removeClass('hide');}
            else
                {$(".live-scores").addClass('hide');}

            if ($("#livedata").data("chat_key") == undefined)
                {$("#livedata").data("chat_key",d.ck);}

            // chat message
            if (d.cm !== undefined && d.cm.length > 0)
            {
                var new_chat_key = parseInt(chat_key);
                $.each(d.cm,function(i, msg){
                    new_chat_key = Math.max(new_chat_key, parseInt(msg.message_id));
                    // Don't show these for mobile view.
                    if ($("#chat-modal").data('chat-on') != true && $("#chat-button").is(":visible"))
                    {
                        var text = "<b>"+msg.chat_name+"</b><br><i>"+msg.message_text+"</i>";

                        var chat_jbox = new jBox('Tooltip', {
                            content: text,
                            target: $("#chat-button"),
                            width: 200,
                            addClass: 'Tooltip-chat',
                            stack: false
                        });
                        chat_jbox.open();
                        setTimeout(function(){chat_jbox.close(); console.log('closed');},4000);

                        return
                    }
                });
                $("#livedata").data("chat_key",new_chat_key);
            }


        }
    });
}
<?php endif;?>

</script>
