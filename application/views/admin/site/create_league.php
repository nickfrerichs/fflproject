
    
<div class="section">
    <div class="container">
            <div class="field">
                <label class="label">League Name</label>
                <div class="control">
                    <input class="input" type="text" id="league-name"></input>
                </div>
            </div>
            <div class="box content">
                            League will be created as season: <b><?=$nfl_schedule_status->year?>, <?=$nfl_schedule_status->gt?></b><br>
							If this is incorrect, first update the NFL schedule in your database by running: <pre>update.py -schedule</pre>
            </div>
            <button class="button small is-link" id="create-league">Create</button>
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
