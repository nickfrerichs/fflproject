
<table id="matchup-<?=$id?>" data-id="<?=$id?>" class="table is-fullwidth is-bordered is-striped ls-matchup-table<?php if($id != 0){echo ' is-hidden"';}?>">
    <thead>
        <th class="ls-c-playerlight"></th>
        <?php if(!$compact):?>
            <th class="ls-c-playerphoto-box"><img src="<?=$matchup['home_team']['thumb']?>"></th>
        <?php endif;?>
        <th class="text-center">
            <a href="#" class="stat-popup" data-type="team" data-id="<?=$matchup['home_team']['team']->id?>">
                <div class="is-hidden-tablet"><?=$matchup['home_team']['team']->team_abbreviation?></div>
                <div class="is-hidden-mobile"><?=$matchup['home_team']['team']->team_name?></div>
            </a>
        </th>
        <th class="ls-c-teamscore text-left teamscore-<?=$matchup['home_team']['team']->id?>"><?=$matchup['home_team']['points']?></th>
        <th class="text-center ls-c-position">vs</th>
        <th class="ls-c-teamscore text-right teamscore-<?=$matchup['away_team']['team']->id?>"><?=$matchup['away_team']['points']?></th>
        <th class="text-center">
            <a href="#" class="stat-popup" data-type="team" data-id="<?=$matchup['away_team']['team']->id?>">
                <div class="is-hidden-tablet"><?=$matchup['away_team']['team']->team_abbreviation?></div>
                <div class="is-hidden-mobile"><?=$matchup['away_team']['team']->team_name?></div>
            </a>
        </th>
        <?php if(!$compact):?>
            <th class="ls-c-playerphoto-box"><img src="<?=$matchup['away_team']['thumb']?>"></th>
        <?php endif;?>
        <th class="ls-c-playerlight"></th>
    </thead>
    <tbody>
        <?php foreach($matchup['home_team']['starters'] as $key => $s):?>
                <?php if (isset($matchup['home_team']['starters'][$key]['player'])){$hp = $matchup['home_team']['starters'][$key];}else{$hp=False;}?>
                <?php if (isset($matchup['away_team']['starters'][$key]['player'])){$ap = $matchup['away_team']['starters'][$key];}else{$ap=False;}?>
                <?php if ($hp){$hpclass=" ".$hp['teamclass']." p_".$hp['player']->player_id;}else{$hpclass="";}?>
                <?php if ($ap){$apclass=" ".$ap['teamclass']." p_".$ap['player']->player_id;}else{$apclass="";}?>
                <tr>
                    <?php if($hp): ?>
                        <td class="ls-c-playerlight<?=$hpclass?>"></td>
                        <?php if(!$compact): ?>
                            <td class="ls-c-playerphoto-box">
                                <?php if($hp['player']->photo != "" && 1==1): ?>
                                    <img class="ls-c-playerphoto-img" src="<?=site_url('images/'.$hp['player']->photo)?>" height=85px width=85px>
                                <?php endif;?>
                            </td>
                        <?php endif;?>
                        <td class="ls-c-playerbox ls-c-td-left<?=$hpclass?>" data-id="<?=$hp['player']->player_id?>" data-team="<?=$hp['teamclass']?>">
                            <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $hp, 'compact' => $compact)); ?>
                        </td>
                        <td class="ls-c-playerscore text-center<?=$hpclass?> playerscore-<?php if($hp){echo $hp['player']->player_id;}?>">
                            <?php if($hp){echo $hp['player']->points;}else{echo "-";}?>
                        </td>
                    <?php else: ?>
                        <td></td><td class="ls-c-playerbox"></td><td></td>
                        <?php if(!$compact):?>
                            <td></td>
                        <?php endif;?>
                    <?php endif;?>
                    <td class="text-center ls-c-position"><?=$s['pos_text']?></td>

                    <?php if($ap): ?>
                        <td class="ls-c-playerscore text-center<?=$apclass?> playerscore-<?php if($ap){echo $ap['player']->player_id;}?>">
                            <?php if($ap){echo $ap['player']->points;}else{echo "-";}?>
                        </td>
                        <td class="text-right ls-c-playerbox ls-c-td-right<?=$apclass?>" data-id="<?=$ap['player']->player_id?>" data-team="<?=$ap['teamclass']?>">
                            <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $ap)); ?>
                        </td>
                        <?php if(!$compact): ?>
                            <td class="ls-c-playerphoto-box">
                                <?php if($ap['player']->photo != "" && 1==1): ?>
                                    <img class="ls-c-playerphoto-img" src="<?=site_url('images/'.$ap['player']->photo)?>" height=85px width=85px>
                                <?php endif;?>
                            </td>
                        <?php endif;?>
                        <td class="ls-c-playerlight<?=$apclass?>"></td>
                    <?php else: ?>
                        <td></td><td class="ls-c-playerbox"></td><td></td>
                        <?php if(!$compact):?>
                            <td></td>
                        <?php endif;?>
                    <?php endif;?>

                </tr>
        <?php endforeach;?>
    </tbody>
</table>

