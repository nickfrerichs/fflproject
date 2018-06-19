<?php //print_r($message); ?>


<div id="displayed-message" data-message-id="<?=$message->id?>" style="padding-left:20px;" class="content">
	<b>Date:</b> <?=date("D, M j - g:i a",$message->unix_date)?><br>
	<b>Subject:</b> <?=$message->subject?><br>
	<b>From:</b> <?=$message->first_name.' '.$message->last_name?><br><br>
<?=nl2br($message->body)?>
</div>
