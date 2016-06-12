<div class="row align-center">
    <div class="small-6">
    <table class=''>
        <thead>
            <th colspan=2 style="font-size:1.5em;">
                <?php if($g['home_team']['team']->id == 0):?>
                    <?=$g['home_team']['team']->team_name?>
                <?php else: ?>
                    <a href="#" class="stat-popup" data-type="team" data-id="<?=$g['home_team']['team']->id?>"><?=$g['home_team']['team']->team_name?></a>
                <?php endif;?>
            </th>
            <th style="font-size:1.7em;" class="text-right"><span class="tscore-<?=$g['home_team']['team']->id?>">-</span></th>
        </thead>
        <tbody>
            <?php foreach($g['home_team']['starters'] as $p): ?>
                <?php $this->load->view('user/season/scores/display_player_live',array('p' => $p, 'view' => $view)); ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="small-6">
        <table class=''>
            <thead>
                <th colspan=2 style="font-size:1.5em;">
                    <?php if($g['away_team']['team']->id == 0):?>
                        <?=$g['away_team']['team']->team_name?>
                    <?php else: ?>
                        <a href="#" class="stat-popup" data-type="team" data-id="<?=$g['away_team']['team']->id?>"><?=$g['away_team']['team']->team_name?></a>
                    <?php endif;?>
                </th>
                <th style="font-size:1.7em;" class="text-right"><span class="tscore-<?=$g['away_team']['team']->id?>">-</span></th>
            </thead>
            <tbody>
                <?php foreach($g['away_team']['starters'] as $p): ?>
                    <?php $this->load->view('user/season/scores/display_player_live',array('p' => $p, 'view' => $view)); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
