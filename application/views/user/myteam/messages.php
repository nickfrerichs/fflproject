<style>
	.unread{font-weight:bold}
</style>

<div class="section">
	<div class="container">
	<div class="title">Messages</div>


	<div class="columns">
		<div class="column is-2-tablet">
			<div><a class="button is-link" href="<?=site_url('myteam/messages/compose')?>"><h6>Compose</h6></a></div>
			<br>
			<div id="folder_0" class="folder"><a href="#"><h6>Inbox</h6></a></div>
			<div id="folder_1" class="folder"><a href="#"><h6>Sent Items</h6></a></div>
			<div id="folder_2" class="folder"><a href="#"><h6>Trash</h6></a></div>
		</div>
		<div class="column is-10-tablet box">
			<div id="current_0" class="folder-name is-size-4 title"></div>
			<div style="overflow: auto;max-height:200px; background-color:#FFF">
				<table id="message-table" class="table is-fullwidth is-striped" style="border-color:#fff;">
					<tbody id="message-list">
					</tbody>
				</table>
			</div>
			<br>
			<div id="message-callout" class="is-hidden message">
				<div id="message-display" style="max-height:300px; overflow-x:scroll margin-bottom:0px" class="message-body">
				</div>
			</div>
			<div id="message-buttons" class="is-hidden">
				<button id="message-reply" class="button is-small is-link">Reply</button>
				<button id="message-delete" class="button is-small is-link">Move to Trash</button>
				<button id="message-forever" class="button is-small is-link">Delete Forever</button>
				<button id="message-close" class="button is-small is-link">Close</button>
			</div>
		</div>
	</div>
	</div>
</div>

<script>
$(document).ready(function(){

	load_messages(0);

	$("#message-list").on("click","tr",function(){
		$(this).removeClass('unread');
		if (this.id == "") {return}
		if (this.id == $("#displayed-message").data("message-id"))
		{
			$("#message-close").click();
			$("#message-callout").addClass("is-hidden");
		}
		else
		{
			url = "<?=site_url('myteam/messages/ajax_get_message')?>";
			$.post(url,{'id' : this.id }, function(data){
				$("#message-display").html(data);
				show_controls();
			});
			$("#message-list tr").removeClass("has-background-link")
			$("#message-list tr").removeClass("has-text-light")
			$(this).addClass("has-background-link");
			$(this).addClass("has-text-light")
			$("#message-callout").removeClass("is-hidden");
			if (current_folderid() == 2){$('#message-delete').addClass('is-hidden');}
			else{$('#message-delete').removeClass('is-hidden');}
		}
	});

	$(".folder").on("click",function(){
		var id = this.id.replace("folder_","");
		load_messages(id);
	});

	// Reply button click
	$("#message-reply").on("click",function(){
		url = "<?=site_url('myteam/messages/')?>";
		var id = $("#displayed-message").data("message-id");
		window.location.replace("<?=site_url('myteam/messages/compose/reply')?>"+"/"+id);
	});

	// Delete button click
	$("#message-delete").on("click",function(){
		url = "<?=site_url('myteam/messages/delete_message')?>";
		var id = $("#displayed-message").data("message-id");
		$.post(url,{'id' : id},function(data){
			result = $.parseJSON(data);
			if (result.success == true)
			{
				$("#message-display").text("");
				$("#message-callout").addClass("is-hidden");
				//notice(result.msg);
			}
			load_messages(current_folderid());

		});
	});

	// Delete forever button click
	$("#message-forever").on("click",function(){
		url = "<?=site_url('myteam/messages/delete_message')?>";
		var id = $("#displayed-message").data("message-id");
		$.post(url,{'id' : id,'forever':true},function(data){
			result = $.parseJSON(data);
			if (result.success == true)
			{
				$("#message-display").text("");
				$("#message-callout").addClass("is-hidden");
				//notice(result.msg);
			}
			load_messages(current_folderid());

		});
	});

	// Close button click
	$("#message-close").on("click",function(){
		$("#message-display").text("");
		$("#message-callout").addClass("is-hidden");
		hide_controls();
	});

	function current_folderid() {return $(".folder-name").attr('id').replace("current_","");}

	function load_messages(folder)
	{
		url = "<?=site_url('myteam/messages/ajax_message_list')?>";
		$.post(url, {'id' : folder},function(data){
			$("#message-list").html(data);
			$("#message-display").text("");
			$(".folder-name").attr('id','current_'+folder);
			$(".folder-name").text($("#folder_"+folder).text());
		});
		hide_controls()
	}

	function hide_controls()
	{$("#message-buttons").addClass("is-hidden"); $("#message-display").addClass("is-hidden");}
	function show_controls()
	{$("#message-buttons").removeClass("is-hidden"); $("#message-display").removeClass("is-hidden");}
});

</script>
