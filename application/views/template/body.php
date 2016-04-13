<body>
    <?php $this->load->view('template/body_js.php'); ?>

	<div>
        <?php $this->load->view('template/header.php'); ?>
     	<div class="view-wrap">
        	<?php $this->load->view($v); ?>
    	</div>
        <?php $this->load->view('template/footer.php'); ?>
    </div>
</body>
