<html>
<head>
<?php
// This detects the view and redirects to the proper one.  There could be a better way.
$this->load->view('template/head_css.php');
?>
</head>
<body>
<?php
$this->load->view('template/body_js.php');
?>
<script>$(document).foundation();</script>
</body>
<script>
	console.log(Foundation.MediaQuery.atLeast('medium'));
	console.log(Foundation.MediaQuery.current);
    if (Foundation.MediaQuery.atLeast('large')){window.location = "<?=site_url('season/scores/live/standard')?>"; console.log("standard")}
    else{window.location = "<?=site_url('season/scores/live/compact')?>"; console.log("compact")}
</script>
</htmL>