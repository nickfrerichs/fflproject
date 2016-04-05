<?php //print_r($teams); ?>

<div class="container">
    <h3>Teams</h3>

    <div class="row table-border">
        <?php foreach($teams as $t): ?>
            <div class="col-sm-6">
                <div class="text-center">
                    <h4><a href="<?=site_url('league/teams/view/'.$t->team_id)?>"><?=$t->long_name?></a></h4>
                </div>
                <div class="row">
                    <div class="col-xs-5">

                        <div class="text-center">
                            <img class="team-logo" src="<?=$logos[$t->team_id]?>"/>
                        </div>
                        <br>
                    </div>
                    <div class="col-xs-7">
                        <table class="table">
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
                <hr>
            </div>
        <?php endforeach;?>
    </div>
</div>
