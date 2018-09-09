<html>
<head>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$this->session->userdata('site_name')?></title>
    <?php $this->load->view('template/head_css.php'); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
<body>
<?php
$this->load->view('template/body_js.php');
?>
<div id="mobile-detector" class="is-hidden-mobile">a</div>


<script>
    if ($(window).width()>900){window.location = "<?=site_url('season/scores/live/standard')?>";}
    else{window.location = "<?=site_url('season/scores/live/compact')?>";}

    //if ($('#mobile-detector').is(':visible')){window.location = "<?=site_url('season/scores/live/standard')?>";}
    //else{window.location = "<?=site_url('season/scores/live/compact')?>";}
</script>
</body>
</html>