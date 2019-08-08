<div class="columns is-mobile is-multiline roster-content<?=$team['team']->id?> is-hidden-mobile">
    <?php foreach($team['starters'] as $p): ?>

        <?php if ($p['player']): ?>
            
                <div class="column is-2">
                    <?=$p['pos_text']?>
                </div>
                <div class="column is-7">
                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$p['player']->player_id?>"><?=$p['player']->short_name?></a>
                </div>
                <div class="column is-3">
                    <?=$p['player']->points?>
                </div>

        <?php else: ?>

                <div class="column is-2">
                    <?=$p['pos_text']?>
                </div>
                <div class="column is-7">
                <i>Vacant</i>
                </div>
                <div class="column is-3">
                    -
                </div>

        <?php endif;?>
    <?php endforeach;?>
    <?php if (isset($team['bench'])): ?>
        <div class="column is-12 bench-link" data-class=".bench-content<?=$matchup_id?>_<?=$team['team']->id?>"
            style="cursor:pointer;"> <!-- Bench header -->
            <div class="columns is-mobile">
                <div class="column is-9 has-text-left has-text-weight-bold">
                    Bench <span class="bench-expand-icon">+</span><span class="bench-expand-icon is-hidden">-</span>
                </div>
                <div class="column is-3 has-text-weight-bold">
                    <?=$team['bench_points']?>
                </div>
            </div>
        </div>
        <?php foreach($team['bench'] as $p):?>
            <div class="column is-12 bench-content<?=$matchup_id?>_<?=$team['team']->id?> is-hidden">
                <div class="columns is-mobile">
                    <div class="column is-2">
                        <?=$p->nfl_pos?>
                    </div>
                    <div class="column is-7 has-text-left">
                        <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->player_id?>"><?=$p->short_name?></a>
                    </div>
                    <div class="column is-3">
                        <?=$p->points?>
                    </div>
                </div>
            </div>
        <?php endforeach;?>   
    <?php endif;?>             
</div>