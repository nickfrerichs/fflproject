<div class="container">
    <div class="row">
        <h3><?=$settings->league_name?></h3>

    </div>
    <div class="row center-block" style="max-width:600px;">

        <h4>Settings</h4>
        <table class="table">
            <thead>
                <th class="col-md-4"></th>
                <th class="col-md-4"></th>
                <th class="col-md-4"></th>
            </thead>
            <tbody>
                <tr>
                    <td>Max Teams</td>
                    <td id="maxteams-field"><?=$settings->max_teams?></td>
                    <td class="text-center">
                        <a href="#" id="maxteams-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="maxteams-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Roster Max</td>
                    <td id="rostermax-field"><?=$settings->roster_max?></td>
                    <td class="text-center">
                        <a href="#" id="rostermax-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="rostermax-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Join Password</td>
                    <td id="joinpassword-field"><?=$settings->join_password?></td>
                    <td class="text-center">
                        <a href="#" id="joinpassword-control" class="change-control" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="joinpassword-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Consumer Token</td>
                    <td id="consumertoken-field"><?=$settings->twitter_consumer_token?></td>
                    <td class="text-center">
                        <a href="#" id="consumertoken-control" class="change-control" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="consumertoken-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Consumer Secret</td>
                    <td id="consumersecret-field"><?=$settings->twitter_consumer_secret?></td>
                    <td class="text-center">
                        <a href="#" id="consumersecret-control" class="change-control" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="consumersecret-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Access Token</td>
                    <td id="accesstoken-field"><?=$settings->twitter_access_token?></td>
                    <td class="text-center">
                        <a href="#" id="accesstoken-control" class="change-control" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="accesstoken-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Access Token</td>
                    <td id="accesssecret-field"><?=$settings->twitter_access_secret?></td>
                    <td class="text-center">
                        <a href="#" id="accesssecret-control" class="change-control" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="accesssecret-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Player Moves</td>
                    <?php if($settings->twitter_player_moves == "1"){$text="On";$toggle="Off";}else{$text="Off";$toggle="On";} ?>
                    <td id="playermoves-field"><?=$text?></td>
                    <td class="text-center">
                        <a href="#" id="playermoves-toggle" class="toggle-control" data-toggle="<?=$toggle?>" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>">
                            Change
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Chat Updates</td>
                    <?php if($settings->twitter_chat_updates == "1"){$text="On";$toggle="Off";}else{$text="Off";$toggle="On";} ?>
                    <td id="chatupdates-field"><?=$text?></td>
                    <td class="text-center">
                        <a href="#" id="chatupdates-toggle" class="toggle-control" data-toggle="<?=$toggle?>" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>">
                            Change
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <?php print_r($settings) ?>
</div>
