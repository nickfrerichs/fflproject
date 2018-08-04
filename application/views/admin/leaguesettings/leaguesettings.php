<style>
.short{max-width:40px;}
</style>


<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-lg-container">
            <div class="is-size-4"><?=$settings->league_name?></div>
            <hr>
            <div class="is-size-6"><b>League Settings</b></div>
            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Maximum number of active teams allowed in the league.  Use Season > Teams to toggle active teams.">Max Active Teams</span></td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'maxteams', 
                                                                                    'value' => $settings->max_teams,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Maximum number of players allowed on a team's roster.">Team Roster Max</span></td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'rostermax', 
                                                                                    'value' => $settings->roster_max,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Number of players an owner can set the keeper flag on to retain for the next season. Leave at 0 to disable keepers.">Number of Keepers</span></td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'keepersnum', 
                                                                                    'value' => $settings->keepers_num,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Make it so no team has keepers selected.  Use this if making a change to the allowed number of keepers.">Clear Existing Keepers</span></td>
                        <td class="text-center"><a href="#" id="clear-keepers">Clear</a></td>
                    </tr>

                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Display draft rank data collected from NFL fantasy api.  Enable this to display rank data in player tables.">Display draft ranks</span></td>
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'draftranks',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->use_draft_ranks));
                            ?>
                        </td>
                    </tr>


                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Allow teams to trade future draft picks along with players.">Allow draft pick trading</span></td>
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'tradepicks',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->trade_draft_picks));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Teams cannot start/sit players after the first game of the week has started.">Lock lineups after first game</span></td>
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'locklineups',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->lock_lineups_first_game));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Offseason disables ability for owners to make changes.  Historical stats are still available.">Offseason enabled</span></td>
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'offseason',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->offseason));
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <div class="is-size-6"><b>Cosmetic</b></div>
            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <tbody>
                    <tr>
                        <td>League Name</td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'leaguename', 
                                                                                    'value' => $settings->league_name,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_league_name')));?>

                        </td>
                    </tr>
                    <tr>
                        <td>Display who's online</td>
                        <td>
                            <?php 
                                    // Inputs: $id, $value, $blank_value, $url, $options, $selected_val
                                    $options = array('On' => '1', 'On (league admins only)' => '2', 'Off' => '3');
                                    $this->load->view('components/editable_select',
                                                array('id' => 'ww-approvals',
                                                        'options' => $options,
                                                        'url' => site_url('admin/leaguesettings/set_wo_setting'),
                                                        'selected_val' => $settings->show_whos_online));
                                ?>
                        </td>
                    </tr>
                </tbody>
            </table>


            <div class="is-size-6"><b>Join settings</b></div>
            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <tbody>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="Set a password that new owners must know when joining the league.">Join Password</span></td>  
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'joinpassword', 
                                                                                    'value' => $settings->join_password,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td>Invite URL </td>
                        <td><a href="<?=$invite_url?>"><?=$invite_url?></a></small></td>
                    </tr>
                </tbody>
            </table>

            <div class="is-size-6"><b>Twitter API</b></div>

            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <thead>
                </thead>
                <tbody>
                    <tr>
                        <td>Twitter Consumer Token</td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'consumertoken', 
                                                                                    'value' => $settings->twitter_consumer_token,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td>Twitter Consumer Secret</td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'consumersecret', 
                                                                                    'value' => $settings->twitter_consumer_secret,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td>Twitter Access Token</td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'accesstoken', 
                                                                                    'value' => $settings->twitter_access_token,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td>Twitter Access Secret</td>
                        <td>
                            <?php $this->load->view('components/editable_text',array('id' => 'accesssecret', 
                                                                                    'value' => $settings->twitter_access_secret,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="When Twitter auth is configured, Waiver Wire and Trades are automatically tweeted.">Twitter Player Moves</span></td>
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'playermoves',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->twitter_player_moves));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><span data-tooltip class="has-tip top" title="When Twitter auth is configured, all chat messages are tweeted.">Twitter Chat Updates</span></td>            
                        <td>
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'chatupdates',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->twitter_chat_updates));
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <a href="https://apps.twitter.com/">https://apps.twitter.com/</a>


            <?=debug($settings,$this->session->userdata('debug'))?>
        </div>
    </div>
</div>

<script>
$("#clear-keepers").on('click',function(){
    var url="<?=site_url('admin/leaguesettings/clear_keepers')?>";
    $.post(url,{},function(data){
        if (data.success)
        {location.reload();}
    },'json');
});
</script>
