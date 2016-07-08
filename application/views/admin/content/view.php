

    <?php $predefined = array('playoffs','rules'); ?>
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
                <a href="<?=site_url('admin/content/edit/'.$content->text_id)?>">Edit</a>
            </div>
        </div>
    <?php elseif(in_array($text_id,$predefined)): ?>
        <div class="row">
            <div class="columns">
        <?=$text_id?> page not created yet, do you want to create it? (<a href="<?=site_url('admin/content/create/'.$text_id)?>">Yes</a>)
            </div>
        </div>
    <?php endif;?>
