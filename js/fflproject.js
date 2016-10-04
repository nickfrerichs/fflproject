//Custom functions used site-wide

function notice(text, noticetype)
{
	var setcolor = "black";
	var setx = 'left';
	var sety = 'bottom';
	var offx = 0;
	var offy = 0;
	switch(noticetype){
		case "success":
			setcolor = "green";
			break;
		case "warning":
			setcolor = "yellow";
		case "error":
			setcolor = "red";
		default:
	}
	new jBox('Notice', {
		content: text,
		attributes: {x: setx, y: sety},
	//	position:{x: offx, y: offy},
		color:setcolor,
		closeButton:'title',
		closeOnEsc: true,
		stack: true
	});
}

function showMessage(text, type)
{
	var html = '<div class="messages alert '+type+'" role="alert">'+text+'</div>';
	$('#messages').html(html);
	$('#messages').slideDown();
	setTimeout(function(){
//		$('#messages').addClass('hide');
	$('#messages').slideUp();
	},4000);
}

// A control to toggle some boolean option stored in the database
//$(".toggle-control").on('click', function(e){
// url: the ajax url, item: use it to specify an option to the ajax call (ex: what to toggle)
$(document).on('click','.toggle-control',function(e){
	var element = $(this);
	var url = $(this).data('url');
	var item = $(this).data('item');
	var item2 = $(this).data('item2');
	$.post(url,{"item":item,"item2":item2},function(data){
		var d = $.parseJSON(data);
		if(d.success)
		{
			if ((d.currentValue == 1 && element.is(':checked')) || (d.currentValue == 0 && !element.is(':checked')))
			{return}
			location.reload();
		}
	});
});

// A control to update a setting stored in the database using a text value
// .change-control and .cancel-control classes for editing a single setting/database value at a time.
$(document).on('click',".change-control", function(e){
    // An early attempt to make some resusable javascript
    // Classes change-control and cancel-control with a *-field *-control *-cancel
    // change-control element needs data-url, value, optional data-var1, data-var2, data-var3
    var name = $(this).attr('id').replace('-control','');
    var control = $("#"+name+"-control");
    var cancel = $("#"+name+"-cancel");
    var field = $("#"+name+"-field");
    var newvalue = $("#"+name+"-edit");
	var type = $(this).data('type');
    var url = $(this).data('url');
    var var1 = $(this).data('var1');
    var var2 = $(this).data('var2');
    var var3 = $(this).data('var3');

    if($(this).text() == "Change")
    {
		var current = field.text();
		control.data('current',current);
        field.html(
            '<input id="'+name+'-edit" type="'+type+'" placeholder="'+current+'">'
        );
        control.text('Save');
        cancel.text('Cancel');
		e.preventDefault();
		return;
    }
    if($(this).text() == 'Save')
    {
        $.post(url,{type:name,value:newvalue.val(),var1:var1, var2:var2, var3:var3}, function(data){
            if(data)
            {

                var d = $.parseJSON(data);
                if(d.success){field.html(newvalue.val()); notice('Saved.','success')}
				else
				{field.html(newvalue.attr('placeholder'));}
            }
        });
        //field.text(control.data('current'));
        control.text('Change');
        cancel.text('');
		e.preventDefault();
    }


});

$(document).on('click', '.cancel-control', function(e){
    var name = $(this).attr('id').replace('-cancel','');
    var control = $("#"+name+"-control");
    var field = $("#"+name+"-field");
    control.text('Change');
    field.text(control.data('current'));
    $(this).text('');
	e.preventDefault();
})



// PLAYER SEARCH JS FUNCTIONS
//
// REQUIRED:
// 		tbody with ID to identify the unique list (ex: your-tbody-id)
//			data-url="http://mysite.com/player_search/ajax_url"  -- specify url to post to
//		corresponding element named *-data to store/pass values through ajax, ex: your-tbody-id-data
//
// OPTIONAL:
//		class="player-list-text-input" data-for="your-tbody-id"
//		class="player-list-position-select" data-for="your-tbody-id"
//		class="player-list-a-sort" data-for="your-tbody-id" data-order="asc" data-by="club_id"
//		class="player-list-total" data-for="your-tbody-id"
//		class="player-list-prev" data-for="your-tbody-id"
//		class="player-list-next" data-for="your-tbody-id"

$(document).on("click",".player-list-next, .player-list-prev",function(e){

	e.preventDefault();
	if($(this).hasClass('disabled') || $(this).attr('disabled'))
	{
		return;
	}

	tbody = $(this).data('for');
	if($(this).hasClass('player-list-next'))
	{$("#"+tbody+"-data").data('page',$("#"+tbody+"-data").data('page')+1);}
	else
	{$("#"+tbody+"-data").data('page',$("#"+tbody+"-data").data('page')-1);}
	updatePlayerList(tbody);
});

$(document).on("input",".player-list-text-input",function(event){
	var playerSearchTimer;
	clearTimeout(playerSearchTimer);
	var delay = 500;
	var tbody = $(this).data('for');
	playerSearchTimer = setTimeout(function(){
		resetPlayerSearch(tbody,0,0);
		updatePlayerList(tbody);
	},delay);
});

$(document).on("change",".player-list-position-select",function(){
	var tbody = $(this).data('for');
	resetPlayerSearch(tbody,1,0);
	resetPlayerSort(tbody);
	updatePlayerList(tbody);
});

$(document).on("change",".player-list-year-select",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);

});

$(document).on("change",".player-list-starter-select",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);
});

$(document).on("change",".player-list-custom-select",function(){
	var tbody = $(this).data('for');
	updatePlayerList(tbody);
});

// player sort using links: by, order  -- stored in tbody-player-list.data('by') and ('order')
$(document).on('click',".player-list-a-sort", function(e){
	var tbody = $(this).data('for');
	// If we've already sorted on this "by", toggle the order
	if ($("#"+tbody).data('by') == $(this).data('by'))
		{$("#"+tbody).data('order', $("#"+tbody).data('order') == 'asc' ? 'desc' : 'asc');}
	else
		{$("#"+tbody).data('order','asc');}
	$("#"+tbody).data('by',$(this).data('by'));

	updatePlayerList(tbody);
	e.preventDefault();
});

function updatePlayerList(tbody)
{
	var page = $('#'+tbody+'-data').data('page');
	var pos = $('.player-list-position-select[data-for="'+tbody+'"]').val();
	var year = $('.player-list-year-select[data-for="'+tbody+'"]').val();
	var starter = $('.player-list-starter-select[data-for="'+tbody+'"]').val();
	var custom = $('.player-list-custom-select[data-for="'+tbody+'"]').val();
	var by = $("#"+tbody).data('by');
	var order = $("#"+tbody).data('order');
	var search = $('.player-list-text-input[data-for="'+tbody+'"]').val();
	var per_page = $("#"+tbody+"-data").data('perpage');
	var url = $("#"+tbody).data('url');
	var var1 = $("#"+tbody).data('var1');
	//resetPlayerPage(tbody);
	$.post(url, {'page':page, 'pos':pos, 'by':by, 'order':order, 'search' : search, 'per_page': per_page,
	 			 'year':year, 'starter':starter, 'custom':custom, 'var1':var1}, function(data){
		$("#"+tbody).html(data);

		// Display count currently on screen (1-10 of 500)
		var pagelow = ($("#"+tbody+"-data").data('page') * $("#"+tbody+"-data").data('perpage'));
		var pagehigh = pagelow+$("#"+tbody+"-data").data('perpage');
		var total = $("#"+tbody+"-data").data('total');
		if (pagehigh > total){pagehigh = total;}

		$(".player-list-total[data-for='"+tbody+"']").text((pagelow+1)+" - "+pagehigh+" of "+total);

		// Disable prev button if first page
		if($("#"+tbody+"-data").data('page') < 1)
		{$('.player-list-prev[data-for="'+tbody+'"]').attr('disabled',true); $('.player-list-prev[data-for="'+tbody+'"]').addClass("disabled");}
		else{$('.player-list-prev[data-for="'+tbody+'"]').attr('disabled',false); $('.player-list-prev[data-for="'+tbody+'"]').removeClass("disabled");}

		// Disable next button if last page
		if(pagehigh+1 > total)
		{$('.player-list-next[data-for="'+tbody+'"]').attr('disabled',true); $('.player-list-next[data-for="'+tbody+'"]').addClass("disabled");}
		else{$('.player-list-next[data-for="'+tbody+'"]').attr('disabled',false); $('.player-list-next[data-for="'+tbody+'"]').removeClass("disabled");}
	})
}


function resetPlayerSearch(tbody,text,pos)
{
	if (text == undefined){text = true;}
	if (pos == undefined){pos = true;}
	if (text == true){$('.player-list-text-input[data-for="'+tbody+'"]').val('');}
	if (pos == true){$('.player-list-position-select[data-for="'+tbody+'"]').val(0);}
	$("#"+tbody+"-data").data('page',0);
}

function resetPlayerSort(tbody)
{
	$("#"+tbody).data('by','last_name');
	$("#"+tbody).data('order','asc');
}

function resetPlayerPage(tbody) // This is not being used right now
{$("#"+tbody+"-data").data('page',0);}

//
// END PLAYER SEARCH FUNCTIONS
//

//
// MESSAGES: used site wide to present outstanding warnings/info to user/admin
//
$(document).on('click',"._message-close",function(){

	var ackurl = $(this).data('ackurl');

	$.post(ackurl);
});


// ###############################
//  Server Sent events
// ###############################

function sse_stream_start()
{
	if (typeof(evtSource) == "undefined")
    {
		console.log("Started sse stream.");
		evtSource = new EventSource(BASE_URL+"sse/stream");
        evtSource.onmessage = function(e)
        {
			var d = JSON.parse(e.data);

			// Show/Hide live score url
			if (d.ls != undefined)
			{
				if (d.ls == "on")
					{$(".live-scores").removeClass('hide');}
				else
					{$(".live-scores").addClass('hide');}
			}

			// Update who's online text
			if (d.wo != undefined)
			{
				var text = "Who's Here: ";
				$.each(d.wo,function(index, owner){
					if (owner.a == 1)
						{text+='<span class="wo-admin">'+owner.n+'</span>';}
					else
						{text+=owner.n;}
					if (d.wo.length-1 > index)
					{text+=", ";}
					$("#whos-online").html(text);
				});
			}

			if (d.chat != undefined)
			{
				$.each(d.chat,function(i, msg){
					// This is for the popup ballons.
					// Don't show these for mobile view.
					if (chatOpen() != true && $("#chat-button").is(":visible"))
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
						setTimeout(function(){chat_jbox.close();},4000);
					}
					// If chatbox exists, append the chats
					if (typeof(cb) != undefined)
					{
						bottom = chatScrollBottom();
						$(".chat-history-ajax").append(msg.html);
						if(bottom){chatScrollBottom(true);}
					}
				});
			}
			if (d.ur != undefined)
			{
				if (parseInt(d.ur) > 0)
					{$(".unread-count").text(" ("+d.ur+")");}
				else{$(".unread-count").text("");}
			}

			if (d.live != undefined)
			{
				//console.log(d.live);
				// Update team scores
				$.each(d.live.scores.teams,function(id, score){

				});
				// Update player scores
				$.each(d.live.scores.players,function(id, score){
					// check to see if live stat is available
					// if(d.live.players_live.hasOwnProperty(id))
					// {
					// 	$('.p_'+id+" .ls-c-gamestatus").text(d.live.players_live[id].text);
					// 	$('.p_'+id+" .ls-c-gamestatus").data('plive','1');
					// 	playerEvent(id);
					// }
				});

				// Go through each player in the dom because we need to set the live player text and team text
				$.each($('.ls-c-playerbox'),function(){

					var player_id = $(this).data('id');
					// If player box is empty, don't do anything.
					if (player_id == undefined){return;}
					var team = $(this).data('team');
					var delay = 0;
					// Is this player in live player?
					if (d.live.players_live.hasOwnProperty(player_id))
					{

						// Player has a live upate, show that text and delay showing team status
						playerEvent(player_id, d.live.players_live[player_id].text);
						delay = 10000;
					}
					playerTeamStatus(player_id,d.live.nfl_games[team],delay);


				});
			}

			if (d.debug != undefined)
			{
				console.log(d.debug);
			}

        }
	}
	else {
		console.log("Already started sse stream.");
	}
}

// Stuff used for live scoring
function playerEvent(player_id, text)
{
	playerBoxFromTeam('p_'+player_id).addClass("ls-playerevent");
	$(".p_"+player_id+" .ls-c-gamestatus").text(text);
}

// Classes a 'playerbox' can be: ls-playeractive, ls-gameinactive (default to active game inactive player)
function playerTeamStatus(player_id,team,delay)
{
	// team.d = details

	setTimeout(function(){
		var id = "p_"+player_id;
		// There are details we should show with settimeout

		if (delay == 0 && team.d != undefined)
		{
			$(".p_"+player_id+" .ls-c-gamestatus").text(team.d);
			setTimeout(function(){
				$(".p_"+player_id+" .ls-c-gamestatus").text(team.s);
			},10000);
		}
		else {
			$(".p_"+player_id+" .ls-c-gamestatus").text(team.s);
		}

		$("."+id).removeClass("ls-playerevent");
		// This game is not live
		if (team.a == undefined)
		{
			gameInactive(id);
		}
		else
		{
			$("."+id+" .progress-meter").width(team.y+"%");
			if(team.y > 50){team.y=Math.abs(team.y-100);}
			$("."+id+" .progress-meter-text").text(team.y+" yl");
			// Live game and team/def.off is active
			if (team.a == 1)
			{
				playerActive(id);
			}
			else // Live game, but not active
			{
				playerInactive(id);
			}
		}
	},delay);

}


function gameInactive(id)
{
	playerBoxFromTeam(id).removeClass("ls-playeractive");
	playerBoxFromTeam(id).removeClass("ls-playerinactive");
	playerBoxFromTeam(id).addClass("ls-gameinactive");
	$("."+id+" .ls-c-drivebar").addClass("hide");

}

// Player is on the field
function playerActive(id)
{
	playerBoxFromTeam(id).removeClass("ls-gameinactive");
	playerBoxFromTeam(id).removeClass("ls-playerinactive");
	playerBoxFromTeam(id).addClass('ls-playeractive');
	$("."+id+" .progress").addClass('success');
	$("."+id+" .progress").removeClass('secondary')
}

// This is the default, remove both classes
function playerInactive(id)
{
	playerBoxFromTeam(id).removeClass("ls-gameinactive");
	playerBoxFromTeam(id).removeClass('ls-playeractive');
	playerBoxFromTeam(id).addClass("ls-playerinactive");
	$("."+id+" .progress").removeClass('success');
	$("."+id+" .progress").addClass('secondary')
}

function playerBoxFromTeam(id)
{
	return $("."+id+".ls-c-playerlight, ."+id+".ls-c-playerbox, ."+id+".ls-c-playerscore");
}

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
			onOpen: function(){
				markChatsRead();
			},
            onClose: function() {
                if (Foundation.MediaQuery.current == 'small')
                {$(window).scrollTop(0);}
                chatScrollBottom(true);
            }
        });
        cb.open();
        // Set the focus to the textarea.. may change to only do this on large displays.
        $("#chat-message").focus();
        populateChat();
    }
    else {
        cb.toggle();
    }
});

// Post a new message to chat as long as there is non-whitespace in the textarea and you didn't press shift+enter
$(document).on('keypress','#chat-message',function(event){

    if(event.keyCode == 13 && !event.shiftKey){

        if ($(this).val().trim() == "") {event.preventDefault(); return}
        var url = BASE_URL+"league/chat/post";

        $.post(url,{'message' : $(this).val()}, function(){
			console.log("Posted");
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
        $("#chat-history-table").scrollTop($("#chat-history-table").prop('scrollHeight'));
    }
}

function chatOpen()
{
	return $('#chat-modal').is(':visible');
}

function markChatsRead()
{
	var url = BASE_URL+"league/chat/ajax_chats_read";
	$.post(url,{},function(){
		$(".unread-count").text("");
	});
}
