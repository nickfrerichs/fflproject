<?php //print_r($teams); ?>


<div class="section">
    <div class="container">
        <div class="title">League Teams</div>
        <div class="columns is-multiline">
            <?php foreach($teams as $t): ?>
                <div class="column is-half-tablet">
                <?=fflp_html_block_begin()?>
                    <div class="is-link has-text-centered has-background-link column">
                        <a href="<?=site_url('league/teams/view/'.$t->team_id)?>" class="has-text-white is-size-4"><?=$t->long_name?></a>
                    </div>

                    <br>
                    <div class="columns">
                        <div class="column is-4">

                            <div class="has-text-center">
                                <img class="team-logo" src="<?=$logos[$t->team_id]?>"/>
                            </div>

                        </div>
                        <div class="column is-8">
                            <table class="table is-fullwidth">
                                <tbody>
                                    <tr>
                                        <td>Owner:</td>
                                        <td><?=$t->first_name." ".$t->last_name?></td>
                                    </tr>
                                    <?php if($t->division_name): ?>
                                    <tr>
                                        <td>Division:</td>
                                        <td><?=$t->division_name?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td>Record:</td>
                                        <td><?=$t->wins?>-<?=$t->losses?>-<?=$t->ties?> - <?=str_replace('0.0','.0',number_format($t->winpct,3))?></td>
                                    </tr>
                                    <tr>
                                        <td>Points:</td>
                                        <td><?=$t->points?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?=fflp_html_block_end()?>
            <?php endforeach;?>
        </div>
    </div>
</div>
