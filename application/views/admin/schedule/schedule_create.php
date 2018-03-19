    <div class="section">
        <div class="columns">
            <div class="column">
                <div class="is-size-5">Create new schedule?</div>
                <div><strong>(any current schedule for the current year will be erased)</strong></div>
                <div class="select">
                <select id="select-template">
                <?php foreach($templates as $t): ?>
                    <option value="<?=$t->id?>"><?=$t->name.' '.$t->teams.' teams, '.$t->divisions.' divisions'?></option>
                <?php endforeach;?>
                </select>
                </div>
                <br>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="columns">
            <div class="column">
                <h5>League Teams </h5>
                <table class="table is-striped is-fullwidth is-narrow is-bordered">
                    <tbody id="team-list">

                    </tbody>
                </table>
                <button class="button is-small is-link" type="submit" id="create-schedule-button">Create</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#select-template').on('change',function(){
       var url = "<?=site_url('admin/schedule/ajax_create_load_teams')?>";
       var template_id = $(this).val();
       $.post(url,{'template_id':template_id},function(data){
           if (data.success)
           {
               $('#team-list').html(data.html);
           }
        },'json');
    });

    $('#create-schedule-button').on('click',function(){
        var team_array = new Array();
        var url = "<?=site_url('admin/schedule/ajax_create_schedule_from_template')?>";
        var template_id = $('#select-template').val();
        $('.schedule-template-team').each(function(){
            var team_id = $(this).data('id');
            var num = $(this).val();
            
            team_array.push({'team_id':team_id, 'num':num}); 
        });
        $.post(url,{'team_array':team_array, 'template_id':template_id},function(data){
            if (data.success)
            {
                window.location.replace("<?=site_url('admin/schedule')?>");
            }
        },'json');


    });
</script>
