<style>
.short{max-width:40px;}
</style>

<div class="row">
    <div class="columns">
        <h5><?=$settings->league_name?></h5>
    </div>
</div>

<div class="row align-center">
    <div class="columns small-10">

        <h6>Settings</h6>
        <table class="table">
            <thead>
                <th style="width:30%"></th>
                <th style="width:40%"></th>
                <th style="width:30%"></th>
            </thead>
            <tbody>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Maximum number of teams allowed in the league.">Max Teams</span></td>
                    <td id="maxteams-field" class="short"><?=$settings->max_teams?></td>
                    <td class="text-center">
                        <a href="#" id="maxteams-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="maxteams-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Maximum number of players allowed on a team's roster.">Roster Max</span></td>
                    <td id="rostermax-field" class="short"><?=$settings->roster_max?></td>
                    <td class="text-center">
                        <a href="#" id="rostermax-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="rostermax-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Set a password that new members must know when signing up a new team.">Join Password</span></td>
                    <td id="joinpassword-field"><?=$settings->join_password?></td>
                    <td class="text-center">
                        <a href="#" id="joinpassword-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="joinpassword-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Consumer Token</td>
                    <td id="consumertoken-field"><?=$settings->twitter_consumer_token?></td>
                    <td class="text-center">
                        <a href="#" id="consumertoken-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="consumertoken-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Consumer Secret</td>
                    <td id="consumersecret-field"><?=$settings->twitter_consumer_secret?></td>
                    <td class="text-center">
                        <a href="#" id="consumersecret-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="consumersecret-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Access Token</td>
                    <td id="accesstoken-field"><?=$settings->twitter_access_token?></td>
                    <td class="text-center">
                        <a href="#" id="accesstoken-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="accesstoken-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Twitter Access Token</td>
                    <td id="accesssecret-field"><?=$settings->twitter_access_secret?></td>
                    <td class="text-center">
                        <a href="#" id="accesssecret-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="accesssecret-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="When Twitter auth is configured, Waiver Wire and Trades are automatically tweeted.">Twitter Player Moves</span></td>
                    <td></td>
                    <td class="text-center">
                        <div class="switch tiny">
                        <input  class="switch-input toggle-control" data-item="playermoves" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
                            id="playermoves" type="checkbox" <?php if($settings->twitter_player_moves == "1"){echo "checked";}?>>
                        <label class="switch-paddle" for="playermoves">
                        </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="When Twitter auth is configured, all chat messages are tweeted out.">Twitter Chat Updates</span></td>
                    <td></td>
                    <td class="text-center">
                        <div class="switch tiny">
                        <input  class="switch-input toggle-control" data-item="chatupdates" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
                            id="chatupdates" type="checkbox" <?php if($settings->twitter_chat_updates == "1"){echo "checked";}?>>
                        <label class="switch-paddle" for="chatupdates">
                        </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Offseason disables ability for owners to make changes.  Historical stats are still available.">Offseason enabled</span></td>
                    <td></td>
                    <td class="text-center">
                        <div class="switch tiny">
                        <input  class="switch-input toggle-control" data-item="offseason" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
                            id="offseason" type="checkbox" <?php if($settings->offseason == "1"){echo "checked";}?>>
                        <label class="switch-paddle" for="offseason">
                        </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <?php print_r($settings) ?>
</div>
