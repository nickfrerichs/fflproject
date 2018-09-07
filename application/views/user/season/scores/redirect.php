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

<script>
    if ($(window).width()>800){window.location = "<?=site_url('season/scores/live/standard')?>";}
    else{window.location = "<?=site_url('season/scores/live/compact')?>";}
</script>
</body>
</html>