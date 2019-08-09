<style>
.short{max-width:40px;}
</style>


<div class="section">
    <div class="container">

            <div class="title"><?=$settings->league_name?></div>
            <div class="is-divider"></div>
            <div class="title is-size-5">League Settings</div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Maximum number of active teams allowed in the league.  Use Season > Teams to toggle active teams.">Max Active Teams</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/editable_text',array('id' => 'maxteams', 
                                                                                    'value' => $settings->max_teams,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Maximum number of players allowed on a team's roster.">Team Roster Max</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/editable_text',array('id' => 'rostermax', 
                                                                                    'value' => $settings->roster_max,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Number of players an owner can set the keeper flag on to retain for the next season. Leave at 0 to disable keepers.">Number of Keepers</span>
                </div>
                <div class="column">
                        <?php $this->load->view('components/editable_text',array('id' => 'keepersnum', 
                                                                                'value' => $settings->keepers_num,
                                                                                'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Make it so no team has keepers selected.  Use this if making a change to the allowed number of keepers.">Clear Existing Keepers</span>
                </div>
                <div class="column">
                    <a href="#" id="clear-keepers">Clear</a>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Display draft rank data collected from NFL fantasy api.  Enable this to display rank data in player tables.">Display draft ranks</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'draftranks',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->use_draft_ranks));
                            ?>
                </div>
            </div>

            <div class="columns">
                <div class="column is-one-third">

                    <span data-tooltip class="has-tip top" title="Allow teams to trade future draft picks along with players.">Allow draft pick trading</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'tradepicks',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->trade_draft_picks));
                            ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Teams cannot start/sit players after the first game of the week has started.">Lock lineups after first game</span>
                </div>
                <div class="column">
                        <?php $this->load->view('components/toggle_switch',
                                        array('id' => 'locklineups',
                                                'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                'is_checked' => $settings->lock_lineups_first_game));
                        ?>
                </div>
 
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Offseason disables ability for owners to make changes.  Historical stats are still available.">Offseason enabled</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'offseason',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->offseason));
                            ?>
                 </div>
            </div>

            <hr>
            <div class="title is-size-5">Cosmetic</div>
            <div class="columns">
                <div class="column is-one-third">
                    League Name
                </div>
                <div class="column">
                    <?php $this->load->view('components/editable_text',array('id' => 'leaguename', 
                                                                            'value' => $settings->league_name,
                                                                            'url' => site_url('admin/leaguesettings/ajax_change_league_name')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Display who's online
                </div>
                <div class="column">
                    <?php 
                            // Inputs: $id, $value, $blank_value, $url, $options, $selected_val
                            $options = array('On' => '1', 'On (league admins only)' => '2', 'Off' => '3');
                            $this->load->view('components/editable_select',
                                        array('id' => 'ww-approvals',
                                                'options' => $options,
                                                'url' => site_url('admin/leaguesettings/set_wo_setting'),
                                                'selected_val' => $settings->show_whos_online));
                        ?>
                </div>
            </div>

            <hr>
            <div class="title is-size-5">Join Settings</div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="Set a password that new owners must know when joining the league.">Join Password</span>
                </div>
                <div class="column">
                            <?php $this->load->view('components/editable_text',array('id' => 'joinpassword', 
                                                                                    'value' => $settings->join_password,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Invite URL
                </div>
                <div class="column">
                        <a href="<?=$invite_url?>"><?=$invite_url?></a></small>
                </div>
            </div>                
            <hr>

            <div class="title is-size-5">Twitter API</div>
            <div class="columns">
                <div class="column is-one-third">
                    Twitter Consumer Token
                </div>
                <div class="column">
                    
                        <?php $this->load->view('components/editable_text',array('id' => 'consumertoken', 
                                                                                'value' => $settings->twitter_consumer_token,
                                                                                'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Twitter Consumer Secret
                    </div>
                <div class="column">

                            <?php $this->load->view('components/editable_text',array('id' => 'consumersecret', 
                                                                                    'value' => $settings->twitter_consumer_secret,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Twitter Access Token
                </div>
                <div class="column">    
                            <?php $this->load->view('components/editable_text',array('id' => 'accesstoken', 
                                                                                    'value' => $settings->twitter_access_token,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                Twitter Access Secret
                </div>
                <div class="column">
                            <?php $this->load->view('components/editable_text',array('id' => 'accesssecret', 
                                                                                    'value' => $settings->twitter_access_secret,
                                                                                    'url' => site_url('admin/leaguesettings/ajax_change_item')));?>

                        
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="When Twitter auth is configured, Waiver Wire and Trades are automatically tweeted.">Twitter Player Moves</span>
                </div>
                <div class="column">
                
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'playermoves',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->twitter_player_moves));
                            ?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    <span data-tooltip class="has-tip top" title="When Twitter auth is configured, all chat messages are tweeted.">Twitter Chat Updates</span>          
                </div>
                <div class="column">
                            <?php $this->load->view('components/toggle_switch',
                                            array('id' => 'chatupdates',
                                                    'url' => site_url('admin/leaguesettings/ajax_toggle_item'),
                                                    'is_checked' => $settings->twitter_chat_updates));
                            ?>
                </div>
            </div>
            <a href="https://apps.twitter.com/">https://apps.twitter.com/</a>


            <?=debug($settings,$this->session->userdata('debug'))?>

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
