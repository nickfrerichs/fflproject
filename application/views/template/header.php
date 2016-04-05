<div class="container">
    <div class="row">
    <a href="#"><button id="menu-button-xs" class="btn btn-block visible-xs sm-simple btn-menu">FFL <span id="menu-caret" class="caret"></span></button></a>

        <ul id="main-menu" class="sm sm-simple collapsed">
          <li style="float:left; width:200px;" class="hidden-xs"><a href="#" style="font-size:1.5em;">FFL</a></li>
          <?php //print_r($menu_items);?>
            <?php foreach($menu_items as $button => $subitem): ?>
                <?php if (!is_array($subitem)): ?>
                    <li><a href="<?=site_url($subitem)?>"><?=$button?></a></li>
                <?php continue;?>
                <?php endif;?>

              <li><a href="#"><?=$button?></a>
                  <ul>
                  <?php foreach($subitem as $subtext => $url): ?>
                      <li><a href="<?=site_url($url)?>"><?=$subtext?></a></li>
                  <?php endforeach; ?>
                  </ul>
              </li>
            <?php endforeach; ?>

        <?php if($this->session->userdata('league_id')): ?>
            <li id="chat-button" style="float:right;width:80px"><a id="unread_count" href="#" style="font-size:.8em">Chat</a></li>
            <?php if($this->session->userdata('live_scores')){$live="";}else{$live="hidden";}?>
            <li id="live-scores" style="float:right;width:100px" class="<?=$live?>">
                <a style="font-size:.7em;text-shadow: 1px 1px 0px red" href="<?=site_url('season/scores/live')?>">LIVE SCORES</a>
            </li>
        <?php endif;?>

        </ul>
        <?php if($this->session->userdata('league_id')): ?>
        <div id="chat-window">
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

        </div>
        <?php endif;?>

    </div>
</div>

  <div id="messages">
  </div>

<script>
$(function() {
  $('#main-menu').smartmenus({
      subMenusSubOffsetX: 1,
      subMenusSubOffsetY: -8,
      showOnClick:true,
      hideOnClick:true
  });

  menuLogic();
  $('#menu-button-xs').click(function() {
    $('#main-menu').slideToggle(200);
  return false;
  });

  $(window).resize(function(){
    if ($(window).width()>768){menuLogic();}
  });
  function menuLogic()
  {
      if ($("#menu-button-xs").css("display") == "none"){
        $('#main-menu').show()
      }
      else{
        $('#main-menu').hide();
      }
  }
});

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
$(document).keyup(function(event){
    if((event.keyCode == 27) && $('#chat-window').is(':visible'))
    {
        $( "#chat-button" ).trigger( "click" );
        event.preventDefault();
    }
});

// Open stream of chat-key so we know when to update/append chats.
$("#chat-button").on('click', function(){
    $("#chat-window").toggle();
    if($('#chat-window').is(':visible'))
    {
        $("#chat-history-ajax").html("<i>Loading...</i>");
        // Update unread count on chat-button.
        $("#unread_count").text("Chat");
        evtSource = new EventSource("<?=site_url('league/chat/stream_get_chat_key')?>");
    	evtSource.onmessage = function(e)
        {
    		if(($("#chat-window").data('chat-key') != e.data) || ($("#chat-window").data('chat-on') != true))
    		{updateChat(e.data);}
        }
    }
    else
    {
        // Close chat stream
        evtSource.close();
        $("#chat-window").data('chat-on',false);
    }
    // Makde the button look pressed.
    $("#chat-button").toggleClass('chat-button-pressed');
});

// Post a new message to chat as long as there is
// non-whitespace in the textarea and you didn't press
// shift+enter
$('#chat-message').keypress(function(event){
    if(event.keyCode == 13 && !event.shiftKey){
        if ($("#chat-message").val().trim() == "") {event.preventDefault(); return}
        var url = "<?=site_url('league/chat/post')?>";
        $.post(url,{'message' : $('#chat-message').val()}, function(){
            $('#chat-message').val('');
            //updateChat();
            chatScrollBottom(true);
        });
        event.preventDefault();
    }

});

function updateChat(new_chat_key)
{
    chat_key = $("#chat-window").data('chat-key');

    var url = "<?=site_url('league/chat/get_messages')?>";
    // If chat-on != true, get all messages, set chat-on = true, update stored chat-key
    if ($("#chat-window").data('chat-on') != true)
    {
        $.post(url,{}, function(data){
            $("#chat-window").data('chat-on',true);
            $("#chat-history-ajax").html(data);
            $('#chat-history-table').scrollTop($('#chat-history-table').prop('scrollHeight'));
        });
    }
    else if (chat_key != new_chat_key)
    {
        // Else If chat-key != chat_key, get all messages newer than current chat-key and append
        $.post(url,{'chat_key':chat_key}, function(data){
            bottom = chatScrollBottom();
            $("#chat-history-ajax").append(data)
            if(bottom){chatScrollBottom(true);} // If we're at the bottom, snap back to the bottom
        });
    }
    $("#chat-window").data('chat-key',new_chat_key);

}

// return true if scrolled to bottom, false if not
// if true passed in, set scroll to bottom
function chatScrollBottom(set)
{
    if(set == undefined)
    {
        var h = $('#chat-history-table').height()+$('#chat-history-table').scrollTop();
        if (h > $('#chat-history-table').prop('scrollHeight')-25)
        {return true;}
        return false;
    }
    else
    {
        $('#chat-history-table').scrollTop($('#chat-history-table').prop('scrollHeight'));
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
                $("#unread_count").text("Chat ("+count+")");
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
            $("#live-scores").removeClass('hidden');
        }
        else {
            $("#live-scores").addClass('hidden');
        }
    });
}

</script>
