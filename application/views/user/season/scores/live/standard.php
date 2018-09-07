<!--
<div class="row">
    <div class="columns small-4 text-center"><h6>NFL Scores</h6></div>
    <div class="columns small-5"></div>
    <div class="columns small-3 text-center"><h6>League Scores</h6></div>
</div>
-->
<?php fflp_stat_popup(); ?>
<?php //array_unshift($matchups,$matchups[0]); ?>
<?php //array_unshift($matchups,$matchups[3]); ?>
<?php //array_unshift($matchups,$matchups[4]); ?>
<?php //array_unshift($matchups,$matchups[5]); ?>

<div class="section">
    <?php fflp_html_block_begin();?>
        <?php $max_name_len = 19;?>
        <?php $cnt=0;?>
        <?php foreach($matchups as $id => $matchup): ?>
        <?php if($cnt % 6 == 0): ?>
        <div class="columns is-multiline">
        <?php endif;?>
            <div class="column is-2" style="font-size:.8em">
                <div class="columns ls-s-matchup-score <?php if($id == 0){echo 'ls-s-matchup-selected"';}?>" style="" data-id="<?=$id?>">
                    <div class="column is-9">
                        <div><?=substr($matchup['home_team']['team']->team_name,0,$max_name_len)?></div>
                        <div><?=substr($matchup['away_team']['team']->team_name,0,$max_name_len)?></div>
                    </div>
                    <div class="column is-3">
                        <div class="teamscore-<?=$matchup['home_team']['team']->id?>"><?=$matchup['home_team']['points']?></div>
                        <div class="teamscore-<?=$matchup['away_team']['team']->id?>"><?=$matchup['away_team']['points']?></div>
                    </div>
                </div>
            </div>
            
            <?php $cnt++; ?>
            <?php endforeach;?>
    <?php fflp_html_block_end();?>



    <div class="columns ls-s-row">
       
        <div class="column is-4-desktop callout">
            <?php fflp_html_block_begin();?>
            <!-- <div style="overflow-y:auto;max-height:500px;overflow-x:auto"> -->
            <div>
                <table class="table is-fullwidth is-striped">
                    <thead>
                    </thead>
                    <tbody id="ls-s-nflgames">
                    <?php foreach($nfl_matchups as $m):?>
                        <tr class="ls-s-nflgame">
                            <td class="<?=$m->h?>_o-gamerow <?=$m->v?>_o-gamerow">
                                <div class="columns">
                                    <div class="column is-5-tablet">
                                        <div class="columns">
                                            <div class="column is-3-tablet">
                                                <div class="<?=$m->v?>_o-clubid"><?=$m->v?></div>
                                                <div class="<?=$m->h?>_o-clubid"><?=$m->h?></div>
                                            </div>
                                            <div class="column is-3-tablet">
                                                <div class="<?=$m->v?>_o-score">-</div>
                                                <div class="<?=$m->h?>_o-score">-</div>
                                            </div>
                                            <div class="column is-6-tablet">
                                                <div>
                                                    <span class="ls-s-nflgame-down"></span>
                                                </div>
                                                <div>
                                                    <span class="ls-s-nflgame-clock"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="columns">
                                            <div class="column is-12-tablet">
                                                <div class="progress success ls-s-drivebar hide" role="progressbar">
                                                    <span class="progress-meter" style="width: 25%;">
                                                        <p class="progress-meter-text ls-s-drivebar-text"></p>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="column is-7-tablet">
                                        <div class="ls-s-nflgame-lastplay"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php //print_r($nfl_matchups); ?>
                    </tbody>
                </table>
            </div>
            <?php fflp_html_block_end();?>
        </div>
        
        <div class="column is-8-desktop" >

            <?php foreach($matchups as $id => $matchup): ?>
                <?php $this->load->view('user/season/scores/live/compact_table',array('id' => $id, 'matchup' => $matchup, 'compact' => False)); ?>
            <?php endforeach; ?>

        </div>
    </div>
    
</div>
<div id="lsdata" class="hide"></div>

<script>
    $(".ls-c-playerbox").on('click',function(){
        showStatsPopup($(this).data('id'),'player');
    });

    $(".ls-s-matchup-score").on('click',function(){
        var id = $(this).data('id');
        $(".ls-s-matchup-score").removeClass('ls-s-matchup-selected');
        $(this).addClass('ls-s-matchup-selected');
        $(".ls-matchup-table").addClass('hide');
        $("#matchup-"+id).removeClass('hide');

    });
</script>
