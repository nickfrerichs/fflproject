<?php //print_r($owners); ?>

<div class="container">
	<div class="page-heading">
		<h3>Messages</h3>
	</div>
	<form class="form-inline">
		<select id="message-to" class="form-control">
			<option value="0">To:</option>
			<?php foreach($owners as $o): ?>
			<?php if ($team != $o->team_id): ?>
				<?php if (isset($reply_teamid) && $reply_teamid == $o->team_id): ?>
				<option value="<?=$o->team_id?>" selected><?=$o->first_name.' '.$o->last_name?></option>
				<?php else: ?>
				<option value="<?=$o->team_id?>"><?=$o->first_name.' '.$o->last_name?></option>
				<?php endif; ?>
			<?php endif; ?>
			<?php endforeach; ?>
		</select>
		<?php if (isset($reply_subject)): ?>
		<input id="message-subject" type="text" class="form-control" value="<?=$reply_subject?>">
		<?php else: ?>
		<input id="message-subject" type="text" class="form-control" placeholder="Subject">
		<?php endif; ?>
	</form>
	<textarea id="message-body" class="form-control" rows="10"></textarea>
	<button id="send-message" class="btn btn-default">Send Message</button>
</div>

<script>
$(document).ready(function(){

	$("#send-message").on("click",function(){
		var to = $("#message-to").val();
		var subject = $("#message-subject").val();
		var body = $("#message-body").val();
		var url = "<?=site_url('myteam/messages/send_message')?>";
		$.post(url,{'to' : to, 'subject' : subject, 'body': body}, function(){
		window.location.replace("<?=site_url('myteam/messages')?>");
		});
	});

});
</script>