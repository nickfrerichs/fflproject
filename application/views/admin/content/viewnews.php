
<?php //print_r($content);?>
<div class="row">
    <div class="columns">
        <a href="<?=site_url('admin/content/create/news')?>">New entry</a>
    </div>
</div>
<?php if (count($content) > 0): ?>
    <div class="row">
        <div class="columns">
            <?php foreach($content as $c): ?>
            <div class="row callout">
                <div class="columns">
                    <h5><?=$c->title?></h5>
                    <small><?=$c->date_posted?></small>
                    <hr>
                    <?=$c->data?>

                    <a href="<?=site_url('admin/content/edit_news/'.$c->id)?>">Edit</a> |
                    <a href="<?=site_url('admin/content/delete_news/'.$c->id)?>">Delete</a>
                </div>
            </div>
            <?php endforeach;?>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="columns">
            No news content exists, do you want to create one? (<a href="<?=site_url('admin/content/create/news')?>">Yes</a>)
        </div>
    </div>
<?php endif;?>
