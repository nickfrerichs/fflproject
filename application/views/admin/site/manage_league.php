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
    <div class="container">
        <div class="title">Settings</div>
        <div class="title is-size-5"><?=$info->league_name?></div>

        <div class="is-divider"></div>
        <div class="columns">
            <div class="column is-one-third">
                Join Password
            </div>
            <div class="column">
                <?php $this->load->view('components/editable_text',array('id' => 'sitename', 
                                                                                'value' => $settings->join_password,
                                                                                'url' => site_url('admin/site/ajax_change_item')));?>
            </div>
        </div>
        <hr>
        <div class="columns">
            <div class="column is-one-third">
                League Admins
            </div>
            <div class="column is-italic is-size-7">
                <div class="box has-background-light">
                    <?php if(count($admins) > 0): ?>
                        <?php foreach($admins as $a): ?>
                        <?=$a->first_name.' '.$a->last_name?><br>
                        <?php endforeach?>
                    <?php else: ?>
                        (none)
                    <?php endif;?>
                </div>
            </div>
            <div class="column has-text-right">
                <a href="#" id="set-admins-button">Manage Admins</a>
            </div>
        </div>
        <hr>
        <div class="columns">
            <div class="column">
                
                Invite URL
            </div>
            <div class="column">
                <?php $inviteurl = site_url('joinleague/invite/'.$info->mask_id); ?>
                <a href="<?=$inviteurl?>"><?=$inviteurl?></a>
            </div>

        </div>
        <div class="is-divider"></div>
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
