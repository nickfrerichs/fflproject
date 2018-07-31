// #############################
// Chat box stuff
// #############################
$(document).on('click','.chat-button',function(){
    if (typeof(cb) == "undefined")
    {
        $("#chat-history-ajax").html("<i>Loading...</i>");
        // onOpen Erase unread count on chat-button...we just read them.
        // Create the jBox from the chat-modal element, then open it.
        // onClose, scroll back to bottom
        cb = new jBox('Modal',{
            content: $("#chat-modal"),
            blockScroll: false,
            draggable: 'title',
            overlay: false,
            title: "League Chat",
            addClass: 'jBox-chat',
            position: {x: 'right', y:'bottom'},
            offset: {x: -25, y: -10},
            closeButton: "title",
			onOpen: function(){
                $('#chat-modal').data('open',true);
                markChatsRead();
                chatScrollBottom(true);
			},
            onClose: function() {
                $('#chat-modal').data('open',false);
                chatScrollBottom(true);
            }
        });
        cb.open();
        // Set the focus to the textarea.. only on non-small displays.
        $("#chat-message").focus();
        populateChat();
    }
    else {
        cb.toggle();
        chatScrollBottom(true);
    }
});

// Post a new message to chat as long as there is non-whitespace in the textarea and you didn't press shift+enter
$(document).on('keypress','#chat-message',function(event){
    if(event.keyCode == 13 && !event.shiftKey){

        if ($(this).val().trim() == "") {event.preventDefault(); return}
        var url = BASE_URL+"league/chat/post";
		var message_text = $(this).val();
		$("#chat-message").val('');
        $.post(url,{'message' : message_text}, function(){
            $("#chat-message").val('');

            chatScrollBottom(true);
        });
        event.preventDefault();
    }
});

// Add messages to empty chat.
function populateChat()
{
    var url = BASE_URL+"league/chat/get_messages";
    $.post(url,{}, function(data){
        $(".chat-history-ajax").html(data);
        $(".chat-history-table").each(function(){$(this).scrollTop($(this).prop('scrollHeight'));});
    });

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
        debug_out('Chat has scrolled to bottom.')
        $("#chat-history-table").scrollTop($("#chat-history-table").prop('scrollHeight'));
    }
}

function chatOpen()
{
//    return $('#chat-modal').data('open');
	return $('#chat-modal').is(':visible');
}

function markChatsRead()
{
	var url = BASE_URL+"league/chat/ajax_chats_read";
	$.post(url,{},function(){
        $("#unread-chat-count").text("");
    });
    debug_out('All chats marked as read.')
}