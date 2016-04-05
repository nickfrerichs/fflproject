<div class="container">
    <div class="row">
        <h4><?=$info->league_name?></h4>

        Add/remove owners?<br>
        Assign league admins<br>
        Edit settings, set join code ... when joining, check if already have an account, then if already an owner.  This way
        the site admin should be able to join the league.
    </div>
    <div class="row">
        <div class="form-group">
            <table class="table" style="width:300px">
                <tr>
                    <td><b>Join Password</b></td>
                    <td id="joinpassword-field"><?=$settings->join_password?></td>
                    <td>
                        <a href="#" id="joinpassword-control" class="change-control" data-url="<?=site_url('admin/site/ajax_change_item')?>"
                            data-var1="<?=$info->id?>">Change</a>
                        <a href="#" id="joinpassword-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td colspan=3><button class="btn btn-default">Save Settings</button></td>
                </tr>
            </table>
        </div>
    </div>
</div>
