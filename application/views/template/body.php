<body style="visibility: hidden;z-index:5" onload="js_Load()">
    <?php $this->load->view('template/body_js.php'); ?>

    <?php // Used to delay loading the page until all initial ajax loads are done?>
    <script>ajax_waits = {}; ajax_wait=false;</script>
    <?php if(isset($ajax_wait) && $ajax_wait): // If ajax_wait passed in from the controller, set it in JS?>
        <script>ajax_wait=true;</script>
    <?php endif;?>

    <div class="fflp-content">

        <?php $this->load->view('template/header.php'); ?>
        <?php $this->load->view('template/breadcrumbs.php'); ?>
        <?php $this->load->view('template/notifications.php'); ?>
        <?php $this->load->view($v); ?>
    </div>
    <?php $this->load->view('template/footer.php'); ?>

    <?php //$this->load->view('template/body_js.php'); ?>
    <script>
    function js_Load(){
        // Wait for all ajax_waits to be false so the page is only displayed when everything is loaded
        var max_check_time = 5000;
        var total_check_time = 0;
        var check_interval = 25;
        var check = function(){
            debug_out("ajax_waits array");
            debug_out(ajax_waits);
            // Check each one, if one found, ajax_wait is true.
            for(var key in ajax_waits){
                // Set ajax_wait to false, then if val is still true, set it back to true.
                ajax_wait = false;
                if(ajax_waits[key] == true){ajax_wait=true; break;}
            }
            // If ajax_wait is now false or we've hit max check time (tired of waiting), just show the body.
            if(ajax_wait == false || total_check_time >= max_check_time){
                document.body.style.visibility='visible';
                return;
                // run when condition is met
            }
            else {
                // Else, check again after check_interval using setTimeout
                total_check_time+=check_interval
                setTimeout(check, check_interval); // check again in a second
            }
        }
        check();
    }
    </script>
</body>
