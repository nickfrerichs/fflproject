<?php //print_r($inbox); ?>
<div class="container">
	<div>
		<h3>Messages</h3>
	</div>
	<div class="col-md-2">
	<div><a href="<?=site_url('myteam/messages/compose')?>"><h4>New Message</a></h4></div>
	<div id="folder_0" class="folder"><a href="#"><h5>Inbox</h5></a></div>
	<div id="folder_1" class="folder"><a href="#"><h5>Sent Items</h5></a></div>
	<div id="folder_2" class="folder"><a href="#"><h5>Trash</h5></a></div>
	</div>
	<div class="col-md-10">
		<table id="message-table" class="table table-striped table-border">
				<h4><div id="current_0" class="folder-name">Current Folder</div></h4>
			<tbody id="message-list">
			</tbody>
		</table>
		<div id="message-display">
		</div>
	</div>
</div>

<script>
$(document).ready(function(){

	load_messages(0);
	$("#message-list").on("click","tr",function(){
		if (this.id == "") {return}
		if (this.id == $("#message-buttons").data("message-id"))
		{
			$("#message-display").text("");
		}
		else
		{
			url = "<?=site_url('myteam/messages/ajax_get_message')?>";
			$.post(url,{'id' : this.id }, function(data){
				$("#message-display").html(data);
			});
		}
	});

	$(".folder").on("click",function(){
		var id = this.id.replace("folder_","");
		load_messages(id);
	});

	// Reply button click
	$("#message-display").on("click","#message-reply",function(){
		url = "<?=site_url('myteam/messages/')?>";
		window.location.replace("<?=site_url('myteam/messages/compose/reply')?>"+"/"+this.name);
		
	});
	// Delete button click
	$("#message-display").on("click","#message-delete",function(){
		url = "<?=site_url('myteam/messages/delete_message')?>";
		$.post(url,{'id' : this.name},function(){ load_messages(current_folderid)});
	});
	// Close button click
	$("#message-display").on("click","#message-close",function(){
		$("#message-display").text("");
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
	}

});

</script>