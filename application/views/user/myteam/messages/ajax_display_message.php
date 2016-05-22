<?php //print_r($message); ?>


<p id="displayed-message" data-message-id="<?=$message->id?>" style="padding-left:20px;">
	<b>Date:</b> <?=date("D, M j - g:i a",$message->unix_date)?><br>
	<b>Subject:</b> <?=$message->subject?><br>
	<b>From:</b> <?=$message->first_name.' '.$message->last_name?><br><br>
<?=nl2br($message->body)?>
</p>
<hr>
