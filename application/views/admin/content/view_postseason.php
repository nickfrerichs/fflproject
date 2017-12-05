

<?php //print_r($years); ?>
<div class="row">
    <div class="columns">
    <?php if($selected_year == 0): ?>
        <span style="font-size:.8em"><a href="<?=site_url('admin/content/postseason/'.$this->session->userdata('current_year'))?>">All years</a></span>
    <?php else: ?>
        <?php $this->load->view('admin/content/year_bar.php') ?>
    <?php endif;?>
    </div>
</div>

<?php if (count($content) > 0): ?>
    <div class="row">
        <div class="columns">
            <h5><?=$content->title?></h5>
        </div>
    </div>
    <div class="row">
        <div class="columns">
            <?=$content->data?>
        </div>
    </div>
    <div class="row">
        <div class="columns">
            <a href="<?=site_url('admin/content/edit_postseason/'.$selected_year)?>">Edit</a>
        </div>
    </div>
<?php else:?>
    <div class="row">
        <div class="columns">
        page not created yet, do you want to create it? (<a href="<?=site_url('admin/content/create_postseason/'.$selected_year)?>">Yes</a>)
        </div>
    </div>
<?php endif; ?>

