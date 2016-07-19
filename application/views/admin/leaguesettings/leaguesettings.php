<style>
.short{max-width:40px;}
</style>


<div class="row align-center" style="max-width:650px;">

    <div class="columns">
        <h4><?=$settings->league_name?></h4>
    </div>

    <div class="columns small-12 callout">
        <h6><b>League Settings</b></h6>
        <table class="table">
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Maximum number of active teams allowed in the league.  Use Season > Teams to toggle active teams.">Max Active Teams</span></td>
                    <td id="maxteams-field" class="short"><?=$settings->max_teams?></td>
                    <td class="text-center">
                        <a href="#" id="maxteams-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="maxteams-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Maximum number of players allowed on a team's roster.">Team Roster Max</span></td>
                    <td id="rostermax-field" class="short"><?=$settings->roster_max?></td>
                    <td class="text-center">
                        <a href="#" id="rostermax-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="rostermax-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Number of players an owner can set the keeper flag on to retain for the next season. Leave at 0 to disable keepers.">Number of Keepers</span>
                        </td>
                    <td id="keepersnum-field" class="short"><?=$settings->keepers_num?></td>
                    <td class="text-center">
                        <a href="#" id="keepersnum-control" class="change-control" data-type="number" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="keepersnum-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Make it so no team has keepers selected.  Use this if making a change to the allowed number of keepers.">Clear Existing Keepers</span></td>
                    <td></td>
                    <td class="text-center"><a href="#" id="clear-keepers">Clear</a></td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Allow teams to trade future draft picks along with players.">Allow draft pick trading</span></td>
                    <td></td>
                    <td class="text-center">
                        <div class="switch tiny">
                        <input  class="switch-input toggle-control" data-item="tradepicks" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
                            id="tradepicks" type="checkbox" <?php if($settings->trade_draft_picks == "1"){echo "checked";}?>>
                        <label class="switch-paddle" for="tradepicks">
                        </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Teams cannot start/sit players after the first game of the week has started.">Lock lineups after first game</span></td>
                    <td></td>
                    <td class="text-center">
                        <div class="switch tiny">
                        <input  class="switch-input toggle-control" data-item="locklineups" data-url="<?=site_url('admin/leaguesettings/ajax_toggle_item')?>"
                            id="locklineups" type="checkbox" <?php if($settings->lock_lineups_first_game == "1"){echo "checked";}?>>
                        <label class="switch-paddle" for="locklineups">
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
    <div class="columns small-12 callout">
        <h6><b>Cosmetic</b></h6>
        <table>
            <tbody>
                <tr>
                    <td>Display who's online</td>
                    <td>
                        <fieldset>
                            <div class="row">
                                <div class="columns small-12">
                                    <input type="radio" class="wo-setting" name="wo-setting" value="1" id="fullauto" required <?php if($settings->show_whos_online==1){echo "checked";}?>>
                                    <label for="fullauto">On</label>
                                </div>
                                <div class="columns small-12">
                                    <input type="radio" class="wo-setting" name="wo-setting" value="2" id="partauto" required <?php if($settings->show_whos_online==2){echo "checked";}?>>
                                    <label for="partauto">On (league admins only)</label>
                                </div>
                                <div class="columns small-12">
                                    <input type="radio" class="wo-setting" name="wo-setting" value="0" id="manual" required <?php if($settings->show_whos_online==0){echo "checked";}?>>
                                    <label for="manual">Off</label>
                                </div>
                            </div>
                        </fieldset>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="columns small-12 callout">
        <h6><b>Join settings</b></h6>
        <table>
            <tbody>
                <tr>
                    <td><span data-tooltip class="has-tip top" title="Set a password that new owners must know when joining the league.">Join Password</span></td>
                    <td id="joinpassword-field"><?=$settings->join_password?></td>
                    <td class="text-center">
                        <a href="#" id="joinpassword-control" class="change-control" data-type="text" data-url="<?=site_url('admin/leaguesettings/ajax_change_item')?>">Change</a>
                        <a href="#" id="joinpassword-cancel" class="cancel-control"></a>
                    </td>
                </tr>
                <tr>
                    <td>Invite URL </td>
                    <td colspan=2 class="text-right"><a href="<?=$invite_url?>"><?=$invite_url?></a></small></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="columns small-12 callout">
        <h6><b>Twitter API</b></h6>

        <table>
            <thead>
            </thead>
            <tbody>
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
                    <td><span data-tooltip class="has-tip top" title="When Twitter auth is configured, all chat messages are tweeted.">Twitter Chat Updates</span></td>
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
            </tbody>
        </table>
        <a href="https://apps.twitter.com/">https://apps.twitter.com/</a>
    </div>
    </div>
    <?=debug($settings,$this->session->userdata('debug'))?>
</div>

<script>
$(".wo-setting").on('click', function(){
    var url="<?=site_url('admin/leaguesettings/set_wo_setting')?>";
    $.post(url,{'value':$(this).val()},function(data){
        if (data.success)
        {notice('Saved.','success');}
        else {notice('An error ocurred while saving.','Error');}
    },'json');
});
</script>
