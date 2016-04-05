<div> 
    <?php if ($this->flexi_auth->is_admin() && stripos($v,'admin') === false) {echo '<a href = '.site_url().'admin> Admin </a>';} ?>
    <a href='<?php echo site_url(); ?>'>User</a>
</div>