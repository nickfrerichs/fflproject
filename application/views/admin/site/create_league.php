
    
<div class="section">
    <div class="columns is-centered" >
        <div class="column is-5-tablet">
            <div class="field">
                <label class="label">League Name</label>
                <div class="control">
                    <input class="input" type="text" id="league-name"></input>
                </div>
            </div>
            <button class="button small is-link" id="create-league">Create</button>

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
