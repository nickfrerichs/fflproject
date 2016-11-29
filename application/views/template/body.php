<body style="visibility: hidden;z-index:5" onload="js_Load()">
        <?php $this->load->view('template/body_js.php'); ?>

        <?php // Used to delay loading the page until all initial ajax loads are done?>
        <script>ajax_waits = {}; ajax_wait=false;</script>
        <?php if(isset($ajax_wait) && $ajax_wait):?>
            <script>ajax_wait=true;</script>
        <?php endif;?>

	<div id="body-wrap">
        <?php $this->load->view('template/header.php'); ?>
     	<div id="view-wrap" style="min-height:600px;">
            <?php $this->load->view('template/breadcrumbs.php'); ?>
            <?php $this->load->view('template/messages.php'); ?>
        	<?php $this->load->view($v); ?>
    	</div>
        <?php $this->load->view('template/footer.php'); ?>
    </div>
            <?php //$this->load->view('template/body_js.php'); ?>
<script>
function js_Load(){
    // Wait for all ajax_waits to be false so the page is only displayed when everything is loaded
    var max_check_time = 1000;
    var total_check_time = 0;
    var check_interval = 25;
    var check = function(){
        debug_out(ajax_waits);
        // Check each one, if one found, ajax_wait is true.
        for(var key in ajax_waits){
            // Set ajax_wait to false, it will get set to true if waits still exist.
            ajax_wait = false;
            if(ajax_waits[key] == true){ajax_wait=true; break;}
        }
        // Unless ajax_wait is false, repeat.  If false, we're all loaded, show the page.
        if(ajax_wait == false || total_check_time >= max_check_time){
            document.body.style.visibility='visible';
            return;
            // run when condition is met
        }
        else {
            total_check_time+=check_interval
            setTimeout(check, check_interval); // check again in a second
        }
    }
    check();
}
$(document).foundation();
</script>
</body>
