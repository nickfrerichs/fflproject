
<?php $this->load->view('template/modals/stat_popup.php');?>
<?php $all_live_players = true; ?>
<?php $view = $this->input->get('view');?>

<div class="row">
    <div class="columns">
            <div class="row">
                <div class="columns">
                <?php if ($view == "all" || $view==""){$viewurl = site_url('season/scores/live?view=live'); $vtext = "Show All Live Players";}
                      else{$viewurl = site_url('season/scores/live'); $vtext = "Show My Game";}?>
                <div class="text-right"><a id="view" href="<?=$viewurl?>" class="text-right"><?=$vtext?></a></div>
                </div>
            </div>
            <div class="row callout">
                <div class="columns">
                    <?php if(isset($selected_game)):?>
                    <!-- Currently selected game -->
                        <?php $this->load->view('user/season/scores/livescore_game_full',array('g' => $selected_game, 'view' => $view)); ?>
                    <?php endif; ?>
                    <?php if ($view == 'live'): ?> 
                        <?php if (count($matchups) > 0): ?>
                            <?php foreach($matchups as $m): ?>
                                <?php $this->load->view('user/season/scores/livescore_game_full',array('g' => $m, 'view' => $view)); ?>

                            <?php endforeach;?>
                        <?php endif;?>

                    <?php elseif($view == 'all' || $view == ""): ?>
                        <?php if (count($matchups) > 0): ?>
                            <h4 class="text-center">Other games</h4>
                            <div class="row callout">
                                <?php foreach($matchups as $m): ?>
                                    <?php $this->load->view('user/season/scores/livescore_game_min', array('g' => $m)); ?>
                                <?php endforeach;?>
                            </div>
                        <?php endif;?>

                    <?php endif; ?>
                </div>
            </div>

            <div id="key"></div>

    </div>
</div>


<script>

$(document).ready(function(){

    var evtSource = new EventSource("<?=site_url('season/scores/stream_live_scores_key')?>");
    evtSource.onmessage = function(e)
    {

        if($("#key").data('update') != e.data)
        {
            var currentupdate = e.data;
            $("#key").data('update',e.data);

            var url = "<?=site_url('season/scores/live_json')?>";
            $.post(url,{},function(data){
                console.log($("#key").data('update'));

                // Set FFL team scores
                $.each(data.scores.teams,function(id, score){
                    $(".tscore-"+id).text(score);
                });

                // For each player score (all players started), update each player's table row
                $.each(data.scores.players, function(id, score){
                    // Get variables for this player.
                    var teamid = $("#player-"+id).data('team');
                    var status_ele = $("#player-"+id+" > .player-status");
                    var score_ele = $("#player-"+id+" > .player-score");
                    //var score_ele = $("#player-"+id+"-score");
                    var player_ele = $("#player-"+id);
                    var debug_id = "5289";
                    var teamStatusDelay = 0;

                    // Check if live update for this player
                    if(data.players_live.hasOwnProperty(id))
                    {

                        // Get playerplayid, and update it
                        var last_playerplayid = player_ele.data('playerplayid');
                        var current_playerplayid = data.players_live[id].play_id;
                        $("#player-"+id).data('playerplayid',current_playerplayid);

                        if (last_playerplayid < current_playerplayid && last_playerplayid != 0)
                        {

                            doPlayerStatusUpdate(0);
                            teamStatusDelay = 10000;
                        }
                    }

                    // Check if the player's team has a teamid in the DOM, if so, update the team status and/or details, then do player score update
                    if (teamid)
                    {
                        // For debugging
                               // doTeamDetailUpdate(0);
                                //teamStatusDelay = 10000;
                        //  Get vars for this player's team status update
                        var last_teamplayid = player_ele.data('teamplayid');
                        var current_teamplayid = data.nfl_games[teamid].p;
                        player_ele.data('teamplayid',current_teamplayid);

                        if (last_teamplayid < current_teamplayid && last_teamplayid !=0)
                        {
                            // If there are details, and something hasn't been displayed yet (teamStatusDelay == 0)
                            if (data.nfl_games[teamid].d && teamStatusDelay == 0)
                            {
                                doTeamDetailUpdate(0);
                                teamStatusDelay = 10000;
                            }
                        }
                        doTeamStatusUpdate(teamStatusDelay);

                    }
                    doPlayerScoreUpdate();

                    // Return true if current teamid has a playid in the json data
                    function livegame()
                    {
                        if (data.nfl_games.hasOwnProperty(teamid))
                        {
                            if(data.nfl_games[teamid].hasOwnProperty("p"))
                            {
                                return true;
                            }
                        }
                        return false;
                    }

                    function doPlayerStatusUpdate(delay)
                    {
                        var text = data.players_live[id].text;
                        console.log(text);
                        setTimeout(function(){
                            status_ele.text(text)
                            player_ele.addClass('livescore-player-update');
                            if (id == debug_id)
                            {console.log('doPlayerStatusUpdate done.');
                             console.log(player_ele);}

                        },delay);
                    }
                    function doTeamStatusUpdate(delay)
                    {

                        //var teamid = $("#player-"+id).data('team');
                        var text = data.nfl_games[teamid].s;
                        setTimeout(function(){
                            if(text.indexOf("on defense.")>=0 || text.indexOf("on offense.")>=0 || text.indexOf("Halftime") >= 0)
                            {setStatusStyleClass("livescore-sleep");}
                            else if(text.indexOf('Final:')>=0)
                            {setStatusStyleClass("livescore-final");}
                            else if(livegame()){
                                setStatusStyleClass("livescore-status");
                            }
                            else if(text.indexOf('@') >= 0)
                            {
                                setStatusStyleClass("livescore-scheduled");
                            }
                            status_ele.text(text)
                            player_ele.removeClass('livescore-player-update');
                            if (id == debug_id)
                            {//console.log('doTeamStatusUpdate done.');
                            }
                        },delay);
                    }
                    function doTeamDetailUpdate(delay)
                    {
                        //var teamid = $("#player-"+id).data('team');
                        var text = data.nfl_games[teamid].d;
                        setTimeout(function(){
                            status_ele.text(text)
                            setStatusStyleClass("livescore-detail");
                            player_ele.removeClass('livescore-player-update');
                            if (id == debug_id)
                            {console.log('doTeamDetailUpdate done.');}

                        },delay);
                    }
                    function doTeamDetailUpdateNew(delay)
                    {
                        //var teamid = $("#player-"+id).data('team');
                        var text = data.nfl_games[teamid].d;
                        console.log(status_ele);
                        
                        var stat_jbox = new jBox('Tooltip', {
                            content: text,
                            target: $("#player-3674"),
                            appendTo: status_ele,
                            width: 400,
                            stack: false
                        });
                        stat_jbox.open();
                        setTimeout(function(){stat_jbox.close();},4000);



                    }
                    function doPlayerScoreUpdate()
                    {
                        var score = data.scores.players[id];
                        var oldscore = score_ele.text();

                        score_ele.text(score);
                        if(livegame())
                        {
                            score_ele.addClass('player-score-live');
                        }
                        else {
                            score_ele.removeClass('player-score-live');
                            <?php if($view == "live"){echo "player_ele.addClass('livescore-hide-player');";} ?>
                        }
                    }

                    function setStatusStyleClass(setClass)
                    {
                        var classes = ['livescore-sleep','livescore-detail','livescore-status','livescore-final', 'livescore-scheduled'];
                        $.each(classes,function(key, val){
                            status_ele.addClass(setClass);
                            if (setClass != val)
                            {
                                status_ele.removeClass(val);
                            }
                        });
                    }
                });

            },'json');
        }

        $("#debug").text(e.data);
    }

});
</script>
