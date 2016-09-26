<?php //print_r($nfl_opp);?>
<?php print_r($matchups);?>
<div class="row align-center">
    <div class="columns small-12 medium-12 large-6" style="padding:0">
        <?php foreach($matchups as $id => $matchup): ?>
            <table id="matchup-<?=$id?>" class="ls-c-matchup-table<?php if($id != 0){echo ' hide"';}?>" >
                <thead>
                    <th class="ls-c-playerlight"></th>
                    <th class="text-center"><?=$matchup['home_team']['team']->team_name?></th>
                    <th class="ls-c-teamscore"></th>
                    <th class="text-center ls-c-position">vs</th>
                    <th class="ls-c-teamscore"></th>
                    <th class="text-center"><?=$matchup['away_team']['team']->team_name?></th>
                    <th class="ls-c-playerlight"></th>
                </thead>
                <tbody>
                    <?php foreach($matchup['home_team']['starters'] as $key => $s):?>
                            <?php if (isset($matchup['home_team']['starters'][$key]['player'])){$hp = $matchup['home_team']['starters'][$key]['player'];}else{$hp=False;}?>
                            <?php if (isset($matchup['away_team']['starters'][$key]['player'])){$ap = $matchup['away_team']['starters'][$key]['player'];}else{$ap=False;}?>
                            <tr>

                                <td class="ls-c-playerlight"></td>
                                <td class="ls-c-playerbox ls-c-td-left">
                                    <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $hp)); ?>
                                </td>
                                <td class="ls-c-playerscore text-center">
                                    <?php if($hp){echo $hp->points;}else{echo "-";}?>
                                </td>

                                <td class="text-center ls-c-position"><?=$s['pos_text']?></td>

                                <td class="ls-c-playerscore text-center">
                                    <?php if($hp){echo $hp->points;}else{echo "-";}?>
                                </td>

                                <td class="ls-c-playerbox ls-c-td-right">
                                    <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $ap)); ?>
                                </td>

                                <td class="ls-c-playerlight"></td>

                            </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        <?php endforeach;?>
    </div>
</div>
