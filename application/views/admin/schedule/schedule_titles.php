<div class="row">
    <div class="columns">
        <h5>Titles</h5>
    </div>
</div>
<?php //print_r($titles);?>
<div class="row">
    <div class="columns">
        <table>
            <thead>
                <th>Title Text</th><th></th><th>Display Order</th><th></th><th>Delete</th>
            </thead>
            <tbody>
                <?php foreach($titles as $t): ?>
                    <tr>
                        <td id="text<?=$t->id?>-field"> <?=$t->text?></td>
                        <td>
                            <a href="#" id="text<?=$t->id?>-control" class="change-control" data-var1="<?=$t->id?>" data-url="<?=site_url('admin/schedule_templates/ajax_title_text_edit')?>">Change</a>
                            <a href="#" id="text<?=$t->id?>-cancel" class="cancel-control"></a>
                        </td>
                        <td id="order<?=$t->id?>-field"> <?=$t->display_order?></td>
                        <td>
                            <a href="#" id="order<?=$t->id?>-control" class="change-control" data-var1="<?=$t->id?>" data-url="<?=site_url('admin/schedule_templates/ajax_title_order_edit')?>">Change</a>
                            <a href="#" id="order<?=$t->id?>-cancel" class="cancel-control"></a>
                        </td>
                        <td><a href="#" class="delete-title" data-id="<?=$t->id?>">delete</a></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="columns">
        <h6>Add Title</h6>
        <table>
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td><input id="add-title-text" type="text"></td>
                    <td><input id="add-title" class="button small" value="Add"  /></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#add-title').on('click',function(){
        var url="<?=site_url('admin/schedule_templates/ajax_edit_title')?>";
        var text = $('#add-title-text').val();

        $.post(url, {'text': text}, function(data){
            if (data.success)
            {
                location.reload();
            }
        },'json')
    });

    $('.delete-title').on('click',function(){
        var url = "<?=site_url('admin/schedule_templates/ajax_delete_title')?>";
        var id = $(this).data('id');
        $.post(url, {'id': id}, function(data){
            if (data.success)
            {
                location.reload();
            }
        },'json');
    });
</script>