<?php //print_r($message); ?>

<pre>
<?=date("D, M j - g:i a",$message->unix_date)?>

Subject: <?=$message->subject?>
<br>
From: <?=$message->first_name.' '.$message->last_name?>
<br>
<?=nl2br($message->body)?>
</pre>
<div id="message-buttons" data-message-id="<?=$message->id?>">
	<button id="message-reply" class="btn btn-default" name="<?=$message->id?>">Reply</button>
	<button id="message-delete" class="btn btn-default" name="<?=$message->id?>">Delete</button>
	<button id="message-close" class="btn btn-default">Close</button>
</div>