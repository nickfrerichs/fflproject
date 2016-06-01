

<div class="row">
    <div class="columns">
        <table class="table table-condensed">
            <thead>
                <th>Team</th><th>Roster</th><th>Division</th><th>Owner</th><th class="text-center" style="width:200px">Active</th>
            </thead>
            <tbody id="team-list">
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    update_teams();
});

$(document).on('click','.blah',function(e){
    console.log("Blah clicked");
});

function update_teams()
{
    var url = "<?=site_url('admin/teams/ajax_get_teams')?>";
    $.post(url,{},function(data){
        $("#team-list").html(data);
    });
}

// $("#team-list").on('click','.btn-active',function(){
//     var action = $(this).data("action");
//     var teamid = $(this).data("id");
//     var url = "<?=site_url('admin/teams/ajax_toggle_active')?>";
//     $.post(url,{"action":action, "teamid":teamid},function(){
//         update_teams();
//     })
// })

</script>
