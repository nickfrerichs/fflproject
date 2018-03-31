
<?php //print_r($content);?>
<div class="section">

        <a href="<?=site_url('admin/content/create/news')?>">New entry</a>

<?php if (count($content) > 0): ?>

            <?php foreach($content as $c): ?>

                    <div class="is-size-5"><?=$c->title?></div>
                    <small><?=$c->date_posted?></small>
                    <hr>
                    <div class="content">
                    <?=$c->data?>
                    </div>
                    <a href="<?=site_url('admin/content/edit_news/'.$c->id)?>">Edit</a> |
                    <a href="<?=site_url('admin/content/delete_news/'.$c->id)?>">Delete</a>

            <?php endforeach;?>

<?php else: ?>

            No news content exists, do you want to create one? (<a href="<?=site_url('admin/content/create/news')?>">Yes</a>)

<?php endif;?>

</div>