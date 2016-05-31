<body style="visibility: hidden;z-index:5" onload="js_Load()">

        <?php $this->load->view('template/body_js.php'); ?>

	<div id="body-wrap">
        <?php $this->load->view('template/header.php'); ?>
     	<div id="view-wrap">
            <?php $this->load->view('template/breadcrumbs.php'); ?>
        	<?php $this->load->view($v); ?>
    	</div>
        <?php $this->load->view('template/footer.php'); ?>
    </div>
            <?php //$this->load->view('template/body_js.php'); ?>
<script>
function js_Load(){
    document.body.style.visibility='visible';
    $(document).foundation();
}
</script>
</body>
