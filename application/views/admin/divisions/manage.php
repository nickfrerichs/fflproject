<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-sm-container">
            <div class="is-size-5">Add a division</div>

            <table class="table is-fullwidth is-narrow">
                <tr>
                    <td>
                        <label>Division Name</label>
                        <input id="division_name" class="input" type="text"></input>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <button id="add-division-button" class="button is-small is-link">Add</button>
                    </td>
                </tr>
            </table>
            <p></p>
            <h5>Divisions</h5>
            <table class="table is-fullwidth is-narrow">
                <?php foreach ($divisions as $div): ?>
                <tr>
                    <td><?=$div->name?></td>
                    <td><a href="#" data-id="<?=$div->id?>" class="delete-division">delete</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</div>

<script>
    $('#add-division-button').on('click',function(){
        var url="<?=site_url('admin/divisions/ajax_add_division')?>";
        var name = $('#division_name').val();
        $.post(url,{'name':name},function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json');
    });

    $('.delete-division').on('click',function(){
        var url="<?=site_url('admin/divisions/ajax_delete_division')?>";
        var id = $(this).data('id');
        $.post(url,{'id':id},function(data){
            if(data.success)
            {
                location.reload();
            }
        },'json')
    });
</script>