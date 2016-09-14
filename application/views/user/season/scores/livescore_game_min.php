<div class="small-12">
    <table class=''>
        <thead>
            <th class="text-right tscore-<?=$g['home_team']['team']->id?>" style="font-size:1.2em;">-</th>
            <th width="40%" height="55px" class="text-right">
                <?php if($g['home_team']['team']->id == 0):?>
                    <?=$g['home_team']['team']->team_name?>
                <?php else: ?>
                    <a href="#" class="stat-popup" data-type="team" data-id="<?=$g['home_team']['team']->id?>"><?=$g['home_team']['team']->team_name?></a>
                <?php endif;?>
            </th>

            <th class="text-center">
                <a href="<?=site_url('season/scores/live/'.$g['home_team']['team']->id)?>">
                    <span class="glyphicon glyphicon-circle-arrow-up" style="font-size:1.4em;"></span>
                </a></th>
            <th width="40%" height="55px">
                <?php if($g['away_team']['team']->id == 0):?>
                    <?=$g['away_team']['team']->team_name?>
                <?php else:?>
                    <a href="#" class="stat-popup" data-type="team" data-id="<?=$g['away_team']['team']->id?>"><?=$g['away_team']['team']->team_name?></a>
                <?php endif;?>
            </th>
            <th class="text-right tscore-<?=$g['away_team']['team']->id?>" style="font-size:1.2em;">-</th>
        </thead>
        <tbody>
            <?php foreach($g['home_team']['starters'] as $key => $p): ?>
                <tr>
                    <?php $hp = $g['home_team']['starters'][$key];?>
                    <?php $ap = isset($g['away_team']['starters'][$key]) ? $g['away_team']['starters'][$key] : false;?>

                    <!-- Home team player -->
                    <?php if($hp['player']): ?>
                    <td colspan=2 id="player-<?=$hp['player']->player_id?>" data-team="<?=$hp['teamclass']?>">
                        <div style="display:inline-block;float:left;width:25px;" class="text-right player-score">
                        </div>
                        <div style="display:inline-block;float:right;">
                            <a href="#" class="stat-popup" data-type="player" data-id="<?=$hp['player']->player_id?>">
                                <?=$hp['player']->short_name?>
                            </a>
                        </div>
                    </td>
                    <?php else:?>
                        <td colspan=2></td>
                    <?php endif;?>

                    <td class="text-center" style="background-color:#EEE"><strong><?=$hp['pos_text']?></strong></td>

                    <!-- Away team player -->
                    <?php if($ap['player']): ?>
                    <td colspan=2 id="player-<?=$ap['player']->player_id?>" data-team="<?=$ap['teamclass']?>">
                        <div style="display:inline-block;float:left;">
                            <a href="#" class="stat-popup" data-type="player" data-id="<?=$ap['player']->player_id?>">
                            <?=$ap['player']->short_name?>
                            </a>
                        </div>
                        <div style="display:inline-block;float:right;width:25px;" class="text-right player-score">
                        </div>
                    </td>
                <?php else:?>
                    <td colspan=2></td>
                <?php endif;?>
                    <!--<td id="player-<?=$ap['player']->player_id?>-score" class="text-right">-</td>-->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
