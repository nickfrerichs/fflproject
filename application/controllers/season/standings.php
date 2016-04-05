<?php

class Standings extends MY_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('season/standings_model');
    }

    function index()
    {
        $data = array();
        $data['current_year'] = $this->current_year;
        $data['years'] = $this->standings_model->get_years();
        $data['year'] = $this->current_year;
        $this->user_view('user/season/standings.php', $data);
        # By default, show top scores?
    }

    function ajax_get_standings()
    {
        $year = $this->input->post('year');
        $divs = $this->standings_model->get_year_standings($year);
        $defs = $this->standings_model->get_notation_defs();
        //------------------------
        //BEGIN VIEW
        ?>
        <?php //print_r($defs); ?>
        <table class="table table-condensed table-striped" id="standings-table">
            <thead>
                      <th>Team</th><th>Win %</th><th>Record</th><th>Points <span style="font-size:.8em">(ppg)</span></th><th>Opp Points <span style="font-size:.8em">(ppg)</span></th>
            </thead>
        <?php foreach ($divs as $key => $d):?>
        		<tbody>
                    <?php if (isset($d['name'])): ?>
                        <tr><td colspan=5 style="background-color:#DDDDDD;"><strong><?=$d['name']?></strong></td></tr>
                    <?php endif;?>
                    <?php foreach ($d['standings'] as $t): ?>
                        <?php $winptc = ($t->wins+($t->ties/2)) / $t->total_games;?>
                        <tr>
                            <td><?=isset($t->notation_symbol) ? $t->notation_symbol.' ' : ''?><?=$t->team_name?></td>
                            <td><?=str_replace('0.','.',number_format($winptc,3))?></td>
                            <td><?=$t->wins?>-<?=$t->losses?>-<?=$t->ties?></td>
                            <td><?=$t->points?> <span style="font-size:.8em">(<?=round($t->points/$t->total_games,1)?>)</span></td>
                            <td><?=$t->opp_points?> <span style="font-size:.8em">(<?=round($t->opp_points/$t->total_games,1)?>)</span></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
        <?php endforeach;?>
    </table>
    <?php foreach($defs as $def): ?>
        <?=$def->symbol.' - '.$def->text?><br>
    <?php endforeach;?>
        <?php
        //END VIEW
        //---------------------------
    }
}
