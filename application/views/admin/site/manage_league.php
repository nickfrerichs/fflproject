
<div class="reveal" id="set-admins-modal" data-reveal data-overlay="true">
    <h4>Set League Admins</h4>
    <table class="table">
        <tbody id="league-owners-list">

        </tbody>
    </table>
    <button class="close-button" data-close aria-label="Close modal" type="button">
      <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="row">
    <div class="columns">
        <h4><?=$info->league_name?></h4>
        <h5>Settings</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <div>
            <table>
                <tr>
                    <td><b>Join Password</b></td>
                    <td id="joinpassword-field" class="text-center">
                    <?php if($settings->join_password != ""):?>
                        <?=$settings->join_password?>
                    <?php else: ?>
                        (not set)
                    <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="#" id="joinpassword-control" class="change-control" data-url="<?=site_url('admin/site/ajax_change_item')?>"
                            data-var1="<?=$info->id?>">Change</a>
                        <a href="#" id="joinpassword-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><b>League Admins</b></td>
                    <?php if(count($admins) > 0): ?>
                        <td class="text-center">
                            <?php foreach($admins as $a): ?>
                            <?=$a->first_name.' '.$a->last_name?><br>
                            <?php endforeach?>
                        </td>
                    <?php else: ?>
                        <td class="text-center">(none)</td>
                    <?php endif;?>
                    <td class="text-center"><a href="#" id="set-admins-button">Assign</a></td>
                </tr>
                <tr>
                    <?php $inviteurl = site_url('joinleague/invite/'.$info->mask_id); ?>
                    <td><b>Invite URL</b></td><td colspan=2><a href="<?=$inviteurl?>"><?=$inviteurl?></a></td>
                </tr>

            </table>
        </div>
    </div>
</div>

<script>
    function load_admins_and_owners()
    {
        var url = "<?=site_url('admin/site/ajax_get_owners')?>";
        var leagueid = "<?=$info->id?>";
        $.post(url,{'leagueid':leagueid},function(data){
            $("#league-owners-list").html(data);
        });
    }

    $("#set-admins-button").on('click',function(){
        load_admins_and_owners();
        $("#set-admins-modal").foundation('open');
    });

    // $("tbody").on('click','.admin-button',function(){
    //     var url = "<?=site_url('admin/site/ajax_toggle_admin')?>";
    //     var userid = $(this).data('id');
    //     var leagueid = $(this).data('leagueid');
    //     var action = $(this).data('action');
    //     $.post(url,{'userid':userid,'leagueid':leagueid, 'action':action},function(){
    //         load_admins_and_owners();
    //     });
    // });

    $('#set-admins-modal').on('hidden.bs.modal', function (e) {
        location.reload();
    });


</script>
