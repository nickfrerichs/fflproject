<div class="container">
    <div class="row">
        <h4>Create New League</h4>
        <div class="form-group">
            <table class="table" style="width:300px;">
                <tr>
                    <td>League Name</td>
                    <td><input type="text" id="league-name"></input></td>
                </tr>
                <tr>
                    <td colspan=2>
                        <button class="btn btn-default" id="create-league">Create</button>
                    </td>
            </table>
        </div>
    </div>
</div>

<script>
    $("#create-league").on('click',function(){
        var url = "<?=site_url('admin/site/do_create_league')?>";
        var name = $("#league-name").val();
        $.post(url,{'name' : name}, function(data){
            window.location.replace("<?=site_url('admin/site/manage_leagues')?>");
        });
    });
</script>
