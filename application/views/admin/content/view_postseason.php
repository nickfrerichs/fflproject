

<?php //print_r($years); ?>
<div class="section">

    <?php if($selected_year == 0): ?>
        <span style="font-size:.8em"><a href="<?=site_url('admin/content/postseason/'.$this->session->userdata('current_year'))?>">All years</a></span>
    <?php else: ?>
        <?php $this->load->view('admin/content/year_bar.php') ?>
    <?php endif;?>


<?php if (count($content) > 0): ?>

            <div class="is-size-5"><?=$content->title?></div>

            <div class="content">
                <?=$content->data?>
            </div>


            <a href="<?=site_url('admin/content/edit_postseason/'.$selected_year)?>">Edit</a>

<?php else:?>

        page not created yet, do you want to create it? (<a href="<?=site_url('admin/content/create_postseason/'.$selected_year)?>">Yes</a>)

<?php endif; ?>
</div>

