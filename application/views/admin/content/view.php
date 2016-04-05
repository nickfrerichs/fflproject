<div class="container">

    <?php $predefined = array('playoffs'); ?>
    <?php if (count($content) > 0): ?>
        <div class="row">
            <h3><?=$content->title?></h3>
        </div>
        <?=$content->data?>
        <a href="<?=site_url('admin/content/edit/'.$content->text_id)?>">Edit</a>
    <?php elseif(in_array($text_id,$predefined)): ?>
        <?=$text_id?> page not created yet, do you want to create it? (<a href="<?=site_url('admin/content/create/'.$text_id)?>">Yes</a>)
    <?php endif;?>
</div>
