<?php 
    // League admins modal
    $body = '<table class="table is-fullwidth">
                <tbody id="league-owners-list">

                </tbody>
            </table>';
    $this->load->view('components/modal', array('id' => 'set-admins-modal',
                                                          'title' => 'Set League Admins',
                                                          'body' => $body,
                                                         'reload_on_close' => True));
?>

<div class="section">
    <div class="columns">
        <div class="column container fflp-lg-container">
            <h4><?=$info->league_name?></h4>
            <h5>Settings</h5>
        </div>
    </div>

    <div class="columns">
        <div class="column container fflp-lg-container">
            <table class="table is-fullwidth fflp-table-fixed">
                <tr>
                    <td style="width: 150px;"><b>Join Password</b></td>
                    <td colspan=2>
                        <?php $this->load->view('components/editable_text',array('id' => 'join-password', 
                                                                                          'value' => $settings->join_password,
                                                                                          'url' => site_url('admin/site/ajax_change_item'),
                                                                                          'var1' => $info->id));?>
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
                    <td class="text-center"><a href="#" id="set-admins-button">Manage</a></td>
                </tr>
                <tr class="fflp-overflow">
                    <?php $inviteurl = site_url('joinleague/invite/'.$info->mask_id); ?>
                    <td><b>Invite URL</b></td>
                    <td colspan=2 style="word-wrap:break-word">
                        <div>
                            <a href="<?=$inviteurl?>"><?=$inviteurl?></a>
                        </div>
                    </td>
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

    // Show modal and load list of admins
    $("#set-admins-button").on('click',function(){
        load_admins_and_owners();
        $("#set-admins-modal").addClass('is-active');
    });

    $('#set-admins-modal').on('hidden.bs.modal', function (e) {
        location.reload();
    });


</script>
