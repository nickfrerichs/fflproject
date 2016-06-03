<?php //print_r($inbox); ?>

<style>
	.activemessage{color:blue}
</style>

<div class="row">
	<div class="columns">
		<h4>Messages</h4>
	</div>
</div>

<div class="row callout">
	<div class=" columns medium-2 small-12">
		<div><a href="<?=site_url('myteam/messages/compose')?>"><h6>New Message</a></h6></div>
		<div id="folder_0" class="folder"><a href="#"><h6>Inbox</h6></a></div>
		<div id="folder_1" class="folder"><a href="#"><h6>Sent Items</h6></a></div>
		<div id="folder_2" class="folder"><a href="#"><h6>Trash</h6></a></div>
	</div>
	<div class="columns medium-10 small-12">
		<h4><div id="current_0" class="folder-name">Current Folder</div></h4>
		<div style="overflow-x: scroll;max-height:200px;">
			<table id="message-table" class="table">
				<tbody id="message-list">
				</tbody>
			</table>
		</div>
		<hr>
		<div id="message-display" style="max-height:300px; overflow-x:scroll">
		</div>
		<div id="message-buttons" class="hide">
			<button id="message-reply" class="button">Reply</button>
			<button id="message-delete" class="button">Delete</button>
			<button id="message-close" class="button">Close</button>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){

	load_messages(0);

	$("#message-list").on("click","tr",function(){
		if (this.id == "") {return}
		if (this.id == $("#displayed-message").data("message-id"))
		{
			$("#message-close").click();
		}
		else
		{
			url = "<?=site_url('myteam/messages/ajax_get_message')?>";
			$.post(url,{'id' : this.id }, function(data){
				$("#message-display").html(data);
				show_controls();
			});
			$("#message-list tr").removeClass("activemessage")
			$(this).addClass("activemessage");
		}
	});

	$(".folder").on("click",function(){
		var id = this.id.replace("folder_","");
		load_messages(id);
	});

	// Reply button click
	$("#message-reply").on("click",function(){
		url = "<?=site_url('myteam/messages/')?>";
		window.location.replace("<?=site_url('myteam/messages/compose/reply')?>"+"/"+this.name);

	});
	// Delete button click
	$("#message-delete").on("click",function(){
		url = "<?=site_url('myteam/messages/delete_message')?>";
		var id = $("#displayed-message").data("message-id");
		$.post(url,{'id' : id},function(data){
			result = $.parseJSON(data);
			if (result.success == true)
			{
				notice(result.msg);
			}
			load_messages(current_folderid());

		});
	});
	// Close button click
	$("#message-close").on("click",function(){
		$("#message-display").text("");
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
			console.log($(".folder-name").text());
		});
		hide_controls()
	}

	function hide_controls()
	{$("#message-buttons").addClass("hide");}
	function show_controls()
	{$("#message-buttons").removeClass("hide");}

});

</script>
