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
        <!-- <button id="chat-button-small-new" class="button chat-button" type="button" data-toggle="chat-window-small">chat</button> -->
         <button id="chat-button-small" class="button chat-button">chat</button>
      <?php endif;?>

    </div>
</div>

<!-- Normal menu bar for normal computers -->
<div id="title-bar-row" class="row align-spaced align-middle">
    <div class="columns">
        <div class="row align-left align-middle">
            <!--
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
        </ul>
    </div>
    <div class="align-right columns shrink">
        <?php if($this->session->userdata('league_id')): ?>
                <!-- <button id="chat-button" class="button chat-button show-for-medium" type="button" data-toggle="chat-window">chat</button> -->
                <button id="chat-button" class="button chat-button show-for-medium">chat<span class="unread-count"></span></button>
        <?php endif;?>
    </div>
    </div>
</div>


<?php if($this->session->userdata('league_id')): ?>
<!-- <div id="chat-window-small" class="dropdown-pane show-for-small-only chat-window" data-dropdown>
    <div class="text-center">League Chat</div>
    <div id="chat-history-table-small" class="chat-history-table">
        <table>
            <tbody id="chat-history-ajax-small" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message-small" class="chat-message" rows="3" placeholder="You put your trash talk in here..."></textarea>
    </div>
</div>

<div id="chat-window" class="dropdown-pane hide-for-small-only chat-window" data-dropdown>
    <div class="text-center">League Chat</div>
    <hr>
    <div id="chat-history-table" class="chat-history-table">
        <table>
            <tbody id="chat-history-ajax" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message" class="chat-message" rows="3" placeholder="You put your trash talk in here..."></textarea>
    </div>
</div> -->




<div class="reveal" id="chat-modal" data-reveal data-overlay="false" data-v-offset="0" data-h-offset="0" data-close-on-click="false">
    <div id="chat-title-bar" class="text-center column">League Chat
        <button class="close-button" data-close aria-label="Close modal" type="button" style="margin-top:-14px;color:#BBB">
          <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div id="chat-history-table-new" class="chat-history-table">
        <table>
            <tbody id="chat-history-ajax" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message" class="chat-message" rows="3" placeholder="You put your trash talk in here..."></textarea>
    </div>

</button>
</div>

<?php endif;?>

<div id="messages">
</div>

<script>
//
// CHAT BOX STUFF
//
$(function() {
    // Check every 10s to see if new chat messages were added.
    setInterval(function(){updateNewChatIcon();}, 10000);
    setInterval(function(){updateLiveIcon();}, 10000);
    updateNewChatIcon();
    updateLiveIcon();
});

// This makes the escape key hide the chat box.
// $(document).keyup(function(event){
//     if((event.keyCode == 27) && $('#chat-window').css('visibility') != "hidden")
//     {
//         $( "#chat-button" ).trigger( "click" );
//         event.preventDefault();
//     }
// });

$(".chat-button").on('click', function(){

    if (typeof(evtSource) == "undefined")
    {
        //evtSource.close();console.log("closing evtsource")
        $("#chat-history-ajax").html("<i>Loading...</i>");
        // Update unread count on chat-button.
        $(".unread-count").text("");
        evtSource = new EventSource("<?=site_url('league/chat/stream_get_chat_key')?>");

        evtSource.onmessage = function(e)
        {
            if(($("#chat-modal").data('chat-key') != e.data) || ($("#chat-modal").data('chat-on') != true))
            {updateChatNew(e.data);}

            //If medium and chat-window is not open. or small and chat-window-small not open. close evtsource and all chat windows
            // if (whichChatIsActive() == "none")
            // {
            //     closechat()
            //     $("#chat-window").removeClass("is-open");
            //     $("#chat-window-small").removeClass("is-open");
            // }
        }
        $("#chat-modal").foundation("open");
        // Added so you can still use the scroll wheen on the body.
        document.body.style.overflow = "visible";
        $("#chat-modal").draggable();
        //$("#chat-modal").resizable();
        $("#chat-modal").on('closed.zf.reveal',function(){
            closechatnew()
        });
    }
    else
    {
        $("#chat-modal").foundation("close");
    }
});

$("#chat-close-button").on('click',function(){
    $("#chat-modal").foundation("close");
});


// Open stream of chat-key so we know when to update/append chats.
// $("#chat-button, #chat-button-small").on('click', function(){
//
//     var suffix = $(this).attr('id').replace('chat-button','');
//     var chatwindow = $("#chat-window"+suffix);
//     var chathistory = $("#chat-history-ajax"+suffix);
//     //$("#chat-window").toggle();
//     if(chatwindow.css('visibility') == "hidden")
//     {
//         if (typeof(evtSource) == "undefined")
//         {
//             //evtSource.close();console.log("closing evtsource")
//             $(".chat-history-ajax").html("<i>Loading...</i>");
//             // Update unread count on chat-button.
//             $("#unread_count").text("");
//             evtSource = new EventSource("<?=site_url('league/chat/stream_get_chat_key')?>");
//         }
//
//     	evtSource.onmessage = function(e)
//         {
//             console.log("Chat alive.")
//     		if(($("#chat-window").data('chat-key') != e.data) || ($("#chat-window").data('chat-on') != true))
//     		{updateChat(e.data);}
//
//             //If medium and chat-window is not open. or small and chat-window-small not open. close evtsource and all chat windows
//             if (whichChatIsActive() == "none")
//             {
//                 closechat()
//                 $("#chat-window").removeClass("is-open");
//                 $("#chat-window-small").removeClass("is-open");
//             }
//         }
//     }
//     else
//     {
//         // Close chat stream
//         closechat()
//     }
// });

function whichChatIsActive()
{
    if (Foundation.MediaQuery.atLeast('medium') && $("#chat-window").hasClass("is-open") == true)
        {return "medium";}
    if (Foundation.MediaQuery.current == "small" && $("#chat-window-small").hasClass("is-open") == true)
        {return "small";}
    return "none";
}

function closechat()
{
    evtSource.close();
    delete evtSource;
    $("#chat-window").data('chat-on',false);
}

function closechatnew()
{
    if (typeof(evtSource) != "undefined")
    {
        evtSource.close();
        delete evtSource;
        $("#chat-modal").data('chat-on',false);
    }
}


// Post a new message to chat as long as there is
// non-whitespace in the textarea and you didn't press
// shift+enter
$('.chat-message').keypress(function(event){
    if(event.keyCode == 13 && !event.shiftKey){
        if ($(this).val().trim() == "") {event.preventDefault(); return}
        var url = "<?=site_url('league/chat/post')?>";
        $.post(url,{'message' : $(this).val()}, function(){
            $(".chat-message").val('');
            //updateChat();
            chatScrollBottom(true);
        });
        event.preventDefault();
    }

});


function updateChatNew(new_chat_key)
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
            bottom = chatScrollBottomnew();
            $(".chat-history-ajax").append(data)
            if(bottom){chatScrollBottomnew(true);} // If we're at the bottom, snap back to the bottom
        });
    }
    $("#chat-modal").data('chat-key',new_chat_key);

}

function updateChat(new_chat_key)
{
    //var chatwindow = $("#chat-window"+$(this).attr('id').replace('chat-button',''));
    //var chathistory = $("#chat-history-ajax"+$(this).attr('id').replace('chat-button',''));
    chat_key = $("#chat-window").data('chat-key');

    var url = "<?=site_url('league/chat/get_messages')?>";
    // If chat-on != true, get all messages, set chat-on = true, update stored chat-key
    if ($("#chat-window").data('chat-on') != true)
    {
        $.post(url,{}, function(data){
            $("#chat-window").data('chat-on',true);
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
    $("#chat-window").data('chat-key',new_chat_key);

}

// return true if scrolled to bottom, false if not
// if true passed in, set scroll to bottom

function chatScrollBottomnew(set)
{
    // Return true if the currently active chat is near the bottom, false if not.
    if(set == undefined)
    {
        var chat_history_table_id = "#chat-history-table-new";
        var h = $(chat_history_table_id).height()+$(chat_history_table_id).scrollTop();
        if (h > $(chat_history_table_id).prop('scrollHeight')-25)
        {return true;}
        return false;
    }
    else
    {
        $("#chat-history-table-new").scrollTop($("#chat-history-table-new").prop('scrollHeight'));
    }
}

function chatScrollBottom(set)
{
    // Return true if the currently active chat is near the bottom, false if not.
    if(set == undefined)
    {
        var chat_history_table_id = "#chat-history-table";
        if (whichChatIsActive() == "small"){chat_history_table_id = "#chat-history-table-small";}

        var h = $(chat_history_table_id).height()+$(chat_history_table_id).scrollTop();
        if (h > $(chat_history_table_id).prop('scrollHeight')-25)
        {return true;}
        return false;
    }
    else
    {
        $(".chat-history-table").each(function(){
            $(this).scrollTop($(this).prop('scrollHeight'));
        });
    }
}

// Function to update the new chat count on the chat button.
// It only checks if the Chat window is not visible
function updateNewChatIcon()
{
    if(!$('#chat-modal').is(':visible'))
    {

        var url = "<?=site_url('league/chat/unread')?>";
        $.post(url,{}, function(data){
            var count = parseInt(data);
            if (count > 0)
            {
                $(".unread-count").text(" ("+count+")");
            }
        });
    }
}

function updateLiveIcon()
{
    var url = "<?=site_url('season/scores/live_scores')?>"
    $.post(url,{},function(data){
        if(data == "1")
        {
            $(".live-scores").removeClass('hide');
        }
        else {
            $(".live-scores").addClass('hide');
        }
    });
}

</script>
