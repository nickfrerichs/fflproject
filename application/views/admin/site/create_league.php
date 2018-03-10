
    
<div class="section">
    <div class="columns is-centered" >
        <div class="column">
            <div class="container fflp-narrow-container">
                <table class="table is-fullwidth">
                    <tr>
                        <td>League Name</td>
                        <td>
                            <input class="input" type="text" id="league-name"></input>
                        </td>
                    </tr>
                    <tr>
                        <td colspan=2>
                            <button class="button small is-info" id="create-league">Create</button>
                        </td>
                </table>
            </div>

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
