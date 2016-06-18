<!DOCTYPE html>
<html lang="en">
  <?php $this->load->view('template/head'); ?>
  <body style="visibility: hidden;z-index:5" onload="js_Load()">

        <?php $this->load->view('template/body_js.php'); ?>

  <div id="body-wrap">
      <div id="view-wrap">
          <?php $this->load->view($v); ?>
      </div>
    </div>
<script>
function js_Load(){
    document.body.style.visibility='visible';
}
$(document).foundation();
</script>
