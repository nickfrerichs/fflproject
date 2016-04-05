<?php
// $years - array of db object years
// $year - selected year
// $week - selected week
// $games - array of games > array of teams > array of players
?>
Not live
<?php $this->load->view('user/season/scores/nav',
        array('years' => $years, 'year' =>$year, 'week' => $week));?>

<?php //print_r($games); ?>

<div class="container">
    <div class="row">
        <div class="col-xs-12 page-heading" style="text-align: center;">Week <?=$week?></div>
    </div>
</div>

<div class="container">

<?php foreach ($games as $g): ?>
    <div class="col-md-6">
        <?php $p_count = max(count($g['home']['players']), count($g['away']['players'])); ?>
            <table class="table light-bg table-condensed">

                <th class="hidden-xxs"></th><th class="large-text"><?=$g['home']['team_name']?></th><th class="large-text text-right"><?=$g['home']['points']?></th>
                <th></th>
                <th class="hidden-xxs"></th><th class="large-text"><?=$g['away']['team_name']?></th><th class="large-text text-right"><?=$g['away']['points']?></th>
                <?php for($i=0; $i<$p_count; $i++): ?>
                <tr>
                    <?php if(isset($g['home']['players'][$i])): ?>
                        <?php $h = $g['home']['players'][$i]?>
                        <?php if (isset($h['points'])){$points = $h['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs"><?=$h['pos']?></td>
                        <td class="small-text"><a href="<?=site_url('league/players/id/'.$h['id'].'/'.$year)?>"><?=$h['name']?></a></td>
                        <td class="text-right"><?=$points?>
                    <?php else: ?>
                        <td class="hidden-xxs">-</td><td class="small-text">-</td><td class="r-align">-</td>
                    <?php endif; ?>
                    <td></td>
                    <?php if(isset($g['away']['players'][$i])): ?>
                        <?php $a = $g['away']['players'][$i]?>
                        <?php if (isset($a['points'])){$points = $a['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs"><?=$a['pos']?></td>
                        <td class="small-text"><a href="<?=site_url('league/players/id/'.$a['id'].'/'.$year)?>"><?=$a['name']?></a></td>
                        <td class="text-right"><?=$points?>
                    <?php else: ?>
                        <td class="hidden-xxs">-</td><td class="small-text">-</td><td class="r-align">-</td>
                    <?php endif; ?>
                </tr>
                <?php endfor; ?>
            </table>

    </div>
<?php endforeach; ?>
</div>
