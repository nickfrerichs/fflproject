<!-- Small menu bar for mobile devices -->
<div class="show-for-small-only small-top-bar row">
    <div class="title-bar small-9 columns align-left row" data-responsive-toggle="main-menu">
            <button class="menu-icon" type="button" data-toggle></button>
            <div class="title-bar-title site-title-small"><?=$this->session->userdata('site_name')?>
            <?php if($this->session->userdata('league_id')): ?>
                <?php if($this->session->userdata('live_scores')){$live="";}else{$live="hide";}?>
                <span>
                    <a class="live-scores <?=$live?>" href="<?=site_url('season/scores/live')?>">LIVE SCORES</a>
                </span>
            <?php endif;?>
            </div>
    </div>
    <div class="small-3 columns">
      <?php if($this->session->userdata('league_id')): ?>
          <button id="chat-button-small" class="button chat-button" type="button" data-toggle="chat-window-small">chat</button>
      <?php endif;?>

    </div>
</div>

<!-- Normal menu bar for normal computers -->
<div class="top-bar" id="main-menu">
    <div class="top-bar-left">

        <ul class="dropdown menu medium-horizontal drilldown vertical" data-responsive-menu="drilldown medium-dropdown">
          <li class="menu-text"><span class="site-title hide-for-small-only"><?=$this->session->userdata('site_name')?></span><br>
              <span class="league-name-title"><?=$this->session->userdata('league_name')?></span>
              <br>
              <?php if($this->session->userdata('league_id')): ?>
                  <?php if($this->session->userdata('live_scores')){$live="";}else{$live="hide";}?>
                  <span class="hide-for-small-only">
                      <a class="live-scores <?=$live?>" href="<?=site_url('season/scores/live')?>">LIVE SCORES</a>
                  </span>
              <?php endif;?>
          </li>
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
    <div class="top-bar-right align-right row">
        <div class="row">
            <div class="column small-12">
                <ul class="menu medium-horizontal">
                <?php if($this->session->userdata('league_id')): ?>
                    <li>
                        <button id="chat-button" class="button chat-button show-for-medium" type="button" data-toggle="chat-window">chat</button>
                    </li>
                <?php endif;?>

                </ul>
            </div>
        </div>
    </div>
</div>

<?php if($this->session->userdata('league_id')): ?>
<!-- <div id="chat-window" class="dropdown-pane" data-dropdown>
    <h4 class="text-center">League Chat</h4>
    <div id="chat-history-table">
        <table class="table table-striped table-condensed">
            <tbody id="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div class="form-group">
        <textarea id="chat-message" class="form-control" rows="3" placeholder="You put your trash talk in here..."></textarea>
    </div>
</div> -->

<div id="chat-window-small" class="dropdown-pane show-for-small-only chat-window" data-dropdown>
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
</div>
<?php endif;?>

<div id="messages">
</div>


<script>
// $(function() {
//   $('#main-menu').smartmenus({
//       subMenusSubOffsetX: 1,
//       subMenusSubOffsetY: -8,
//       showOnClick:true,
//       hideOnClick:true
//   });
//
//   menuLogic();
//   $('#menu-button-xs').click(function() {
//     $('#main-menu').slideToggle(200);
//   return false;
//   });
//
//   $(window).resize(function(){
//     if ($(window).width()>768){menuLogic();}
//   });
//   function menuLogic()
//   {
//       if ($("#menu-button-xs").css("display") == "none"){
//         $('#main-menu').show()
//       }
//       else{
//         $('#main-menu').hide();
//       }
//   }
// });

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

// Open stream of chat-key so we know when to update/append chats.
$("#chat-button, #chat-button-small").on('click', function(){

    var suffix = $(this).attr('id').replace('chat-button','');
    var chatwindow = $("#chat-window"+suffix);
    var chathistory = $("#chat-history-ajax"+suffix);
    //$("#chat-window").toggle();
    if(chatwindow.css('visibility') == "hidden")
    {
        if (typeof(evtSource) == "undefined")
        {
            //evtSource.close();console.log("closing evtsource")
            $(".chat-history-ajax").html("<i>Loading...</i>");
            // Update unread count on chat-button.
            $("#unread_count").text("");
            evtSource = new EventSource("<?=site_url('league/chat/stream_get_chat_key')?>");
        }

    	evtSource.onmessage = function(e)
        {

            console.log("Chat alive.")
    		if(($("#chat-window").data('chat-key') != e.data) || ($("#chat-window").data('chat-on') != true))
    		{updateChat(e.data);}

            //If medium and chat-window is not open. or small and chat-window-small not open. close evtsource and all chat windows
            if (whichChatIsActive() == "none")
            {
                closechat()
                $("#chat-window").removeClass("is-open");
                $("#chat-window-small").removeClass("is-open");
            }


        }
    }
    else
    {
        // Close chat stream
        closechat()

    }
});

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


// Post a new message to chat as long as there is
// non-whitespace in the textarea and you didn't press
// shift+enter
// $('#chat-message').keypress(function(event){
//     if(event.keyCode == 13 && !event.shiftKey){
//         if ($("#chat-message").val().trim() == "") {event.preventDefault(); return}
//         var url = "<?=site_url('league/chat/post')?>";
//         $.post(url,{'message' : $('#chat-message').val()}, function(){
//             $('#chat-message').val('');
//             //updateChat();
//             chatScrollBottom(true);
//         });
//         event.preventDefault();
//     }

// });

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

function updateChat(new_chat_key)
{
    console.log("doing update chat")
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
            console.log(bottom)
            $(".chat-history-ajax").append(data)
            if(bottom){chatScrollBottom(true);} // If we're at the bottom, snap back to the bottom
        });
    }
    $("#chat-window").data('chat-key',new_chat_key);

}

// return true if scrolled to bottom, false if not
// if true passed in, set scroll to bottom
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
    if(!$('#chat-window').is(':visible'))
    {
        var url = "<?=site_url('league/chat/unread')?>";
        $.post(url,{}, function(data){
            var count = parseInt(data);
            if (count > 0)
            {
                $("#unread_count").text("("+count+")");
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
