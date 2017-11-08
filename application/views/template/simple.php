<!DOCTYPE html>
<html lang="en">
    <?php $this->load->view('template/head'); ?>
    <body style="visibility: hidden;z-index:5" onload="js_Load()">
        <?php $this->load->view('template/body_js.php'); ?>

        <?php $this->load->view($v); ?>

        <div class="container has-text-right">
            <a href="https://github.com/nickfrerichs/fflproject">FFL Project</a>
        </div>

    </body>
    <script>
    function js_Load(){
        document.body.style.visibility='visible';
    }
    </script>
</html>
