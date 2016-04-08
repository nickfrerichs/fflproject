<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FFL</title>

  <!-- Bootstrap and custom css-->
  <link href="<?=site_url('/css/bootstrap.min.css')?>" rel="stylesheet">
  <link href="<?=site_url('/css/jquery-ui.min.css')?>" rel="stylesheet">
  <link href="<?=site_url('/css/custom-bootstrap.css')?>" rel="stylesheet">
  <link href="<?=site_url('/css/fflproject.css')?>" rel="stylesheet">

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>

<body>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=site_url('js/bootstrap.min.js')?>"></script>
    <script>
    $(document).ready(function() {$.getScript("<?=site_url('js/fflproject.js')?>");});
    </script>

	<div>
     	<div class="view-wrap">
        	<?php $this->load->view($v); ?>
    	</div>
    </div>
</body>

</html>
