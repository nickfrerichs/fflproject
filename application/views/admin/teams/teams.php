
<div class="section">
    <div class="container">

        <table class="table table-condensed is-fullwidth is-size-7-mobile f-table-fixed">
            <thead>
                <th>Team</th><th>Division</th><th>Owner</th><th>Active</th>
            </thead>
            <tbody id="team-list">
            </tbody>
        </table>
        <small>Invite URL: <a href="<?=$invite_url?>"><?=$invite_url?></a></small>
        
    </div>
</div>


<script>
    $(document).ready(function(){
        update_teams();
    });

    function update_teams()
    {
        var url = "<?=site_url('admin/teams/ajax_get_teams')?>";
        $.post(url,{},function(data){
            $("#team-list").html(data);
        });
    }
</script>
