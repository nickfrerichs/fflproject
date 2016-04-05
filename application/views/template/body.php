<body>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
    <script src="<?=site_url('js/jquery.smartmenus.min.js')?>"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=site_url('js/bootstrap.min.js')?>"></script>
    <!-- <script src="http://web.mylanparty.net:90/fflproject/js/jquery-ui.min.js"></script> -->
    <script src="<?=site_url('js/jquery.flexnav.min.js')?>"></script>
    <script src="<?=site_url('js/jquery.slimmenu.min.js')?>"></script>
    <script>
    $(document).ready(function() {$.getScript("<?=site_url('js/fflproject.js')?>");});
    </script>

	<div>
        <?php $this->load->view('template/header.php'); ?>
     	<div class="view-wrap">
        	<?php $this->load->view($v); ?>
    	</div>
        <?php $this->load->view('template/footer.php'); ?>
    </div>
</body>
