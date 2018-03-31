
<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-lg-container">
            <div class="is-size-5">Titles</div>
            <br>
            <table class="table is-fullwidth is-narrow is-striped">
                <thead>
                    <th>Title Text</th><th>Display Order</th><th>Delete</th>
                </thead>
                <tbody>
                    <?php foreach($titles as $t): ?>
                        <tr>
                            <td>
                            <?php $this->load->view('components/editable_text',
                                    array(  'id' => "text".$t->id,
                                            'var1' => $t->id, 
                                            'value' => $t->text,
                                            'url' => site_url('admin/schedule_templates/ajax_title_text_edit')));?>

                            </td>


                            <td>
                            <?php $this->load->view('components/editable_text',
                                    array(  'id' => "order".$t->id,
                                            'var1' => $t->id, 
                                            'value' => $t->display_order,
                                            'url' => site_url('admin/schedule_templates/ajax_title_order_edit')));?>

                            </td>
                            <td><a href="#" class="delete-title" data-id="<?=$t->id?>">delete</a></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>

        <div class="is-size-6">Add Title</div>
        <table class="table is-fullwidth is-narrow fflp-table-fixed">
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td><input id="add-title-text" class="input" type="text"></td>
                    <td><input id="add-title" class="button is-link" value="Add"  /></td>
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