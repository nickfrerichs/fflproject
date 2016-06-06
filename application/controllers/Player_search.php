<?php
class Player_search extends MY_Controller{

// This controller presents the ajax data used by various jquery ajax/post functions.
// The views dislay content to be put between <tbody></tbody> tags.


    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/waiverwire_model');
        $this->load->model('player_search_model');

        $this->data = array();

        $this->in_page = $this->input->post('page');
        $this->in_pos = $this->input->post('pos');
        $this->in_sort = $this->input->post('by');
        $this->in_order = $this->input->post('order');
        $this->in_search = $this->input->post('search');
        $this->per_page = $this->input->post('per_page');
        $this->per_page = 10;

        $this->year = $this->input->post('year');
        $this->starter = $this->input->post('starter');
        $this->custom = $this->input->post('custom');

        $this->order_by = array('points','desc');

        $this->data['page'] = ($this->in_page == '') ? 0 : $this->in_page;
        $this->data['sel_pos'] = ($this->in_pos == '') ? '0' : $this->in_pos;
        $this->data['by'] = ($this->in_sort == '') ? 'points' : $this->in_sort;
        $this->data['order'] = ($this->in_order == '') ? 'asc' : $this->in_order;
        $this->data['search'] = $this->in_search;

        $this->order_by = array($this->data['by'],$this->data['order']);

    }

    function ajax_admin_get_player_list()
    {
        if ($this->session->userdata('is_league_admin'))
        {
            $nfl_players = $this->player_search_model->get_nfl_players($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos'], $this->order_by, $this->data['search'], true);
            $this->load->model('myteam/myteam_roster_model');
            $this->data['total'] = $nfl_players['count'];
            $this->data['players'] = $nfl_players['result'];
            $this->data['per_page'] = $this->per_page;
            $this->data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();

            //$this->load->view('player_search/ajax_full_player_list',$this->data);

            // **************************************
            // BEGIN VIEW
            ?>

            <?php foreach($this->data['players'] as $p):?>
                <tr>
                    <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a></td>
                    <td><?=$p->position?></td>
                    <td><?=$p->club_id?></td>
                    <?php if($p->team_name): ?>
                    <td><?=$p->team_name?></td>
                    <?php else: ?>
                        <td><button class="button tiny add-button" data-id="<?=$p->id?>" data-name = "<?=$p->first_name.' '.$p->last_name?>">add</button></td>
                    <?php endif;?>
                </tr>
            <?php endforeach; ?>
            <tr id="main-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$this->data['total']?>">
            </tr>

            <?php
            // END VIEW
            // **************************************
        }
    }

    // for season/draft/live
    function ajax_draft_list()
    {
        $this->load->model('season/draft_model');
        //if ($this->order_by[0] == 'points')
        //    $this->order_by[0] = 'last_name';

        $nfl_players = $this->draft_model->get_available_players_data($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos'], $this->order_by, $this->data['search']);

        $data['total_players'] = $nfl_players['count'];
        $data['players'] = $nfl_players['result'];
        $data['per_page'] = $this->per_page;
        $data['draft_team_id'] = $this->draft_model->get_draft_team_id();
        $data['team_id'] = $this->teamid;
        $data['paused'] = $this->draft_model->draft_paused();

        $this->load->view('user/season/draft/ajax_get_draft_table', $data);

    }

    function ajax_get_draft_watch_list()
    {
        $this->load->model('season/draft_model');


        $this->data['draft_team_id'] = $this->draft_model->get_draft_team_id();
        $this->data['team_id'] = $this->teamid;

        $watch_players = $this->draft_model->get_watch_list($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos']);
        $this->data['players'] = $watch_players['result'];
        $this->data['total_players'] = $watch_players['count'];
        $this->data['per_page'] = $this->per_page;
        $this->data['page'] = $this->in_page;
        $this->data['paused'] = $this->draft_model->draft_paused();
        $this->load->view('user/season/draft/ajax_get_watch_list', $this->data);
    }

    // for myteam/waiverwire
    function ajax_ww_player_list()
    {
        $this->load->model('myteam/waiverwire_model');
        $this->load->model('myteam/myteam_roster_model');

        $nfl_players = $this->waiverwire_model->get_nfl_players($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos'], $this->order_by, $this->data['search'], true);

        $this->data['total'] = $nfl_players['count'];
        $this->data['players'] = $nfl_players['result'];
        $this->data['per_page'] = $this->per_page;
        $this->data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
        //$this->load->view('user/myteam/waiverwire/ajax_pickup_table',$data);

        // BEGIN VIEW
        ?>

        <?php foreach($this->data['players'] as $p):?>
            <tr class="pickup-player" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">
                <td><?=$p->position?></td>
                <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a></td>
                <td><?=$p->club_id?></td>
                <td class="hide-for-small-only"><?=$this->data['matchups'][$p->club_id]['opp']?></td>
                <td><?=$p->points?></td>
                <td> <!-- Might want to make the waiviers not cleared notice better -->
    				<?php if ($p->clear_time)
    				{
    					$remaining = $p->clear_time - time();
    					$hr = (int)($remaining / (60*60));
    					$min = (int)(($remaining - $hr*(60*60)) / 60);
    					$sec = (int)(($remaining - $hr*(60*60) - $min*60));
    				}
    				?>
    				<?php if($p->clear_time): ?>
    					Waivers clear in <?=$hr?>h:<?=$min?>m:<?=$sec?>s
    				<?php else: ?>
    					<button class="player-pickup button tiny" data-pickup-id="<?=$p->id?>" data-pickup-name="<?=$p->first_name.' '.$p->last_name?>">Pickup</button>
    				<?php endif;?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr id="ww-list-data" class="hide" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$this->data['total']?>">
        </tr>

        <?php
        // END VIEW
    }

    function ajax_full_player_list()
    {
        $nfl_players = $this->player_search_model->get_nfl_players($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos'], $this->order_by, $this->data['search'], true);
        $this->load->model('myteam/myteam_roster_model');
        $this->data['total'] = $nfl_players['count'];
        $this->data['players'] = $nfl_players['result'];
        $this->data['per_page'] = $this->per_page;
        $this->data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();

        //$this->load->view('player_search/ajax_full_player_list',$this->data);

        // **************************************
        // BEGIN VIEW
        ?>

        <?php foreach($this->data['players'] as $p):?>
            <tr>
                <td><a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a></td>
                <td><?=$p->position?></td>
                <td><?=$p->club_id?></td>
                <td><?=$this->data['matchups'][$p->club_id]['opp']?></td>
                <td><?=$p->points?></td>
                <td><?=$p->team_name?></td>
            </tr>
        <?php endforeach; ?>
        <tr id="main-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$this->data['total']?>">
        </tr>

        <?php
        // END VIEW
        // **************************************
    }

    function ajax_best_week()
    {
        $players = $this->player_search_model->get_best_week_data($this->year, $this->starter, $this->data['sel_pos']);

        // ******************************
        //BEGIN VIEW
        // print_r($players);
        ?>
        <?php foreach($players as $num => $p): ?>
            <tr>
                <td><?=$num+1?></td>
                <td><?=$p->first_name." ".$p->last_name?></td>
                <td><?=$p->position?></td>
                <td><?=$p->points?></td>
                <td><?=$p->week?></td>
                <td><?=$p->year?></td>
                <td><?=$p->owner_first_name.' '.$p->owner_last_name?></td>
            </tr>
        <?php endforeach;?>
        <?php
        //END VIEW
        // ******************************
    }

    function ajax_avg_week()
    {
        $players = $this->player_search_model->get_avg_week_data($this->year, $this->starter, $this->data['sel_pos']);

        // ******************************
        //BEGIN VIEW
        // print_r($players);
        ?>
        <?php foreach($players as $num => $p): ?>
            <tr>
                <td><?=$num+1?></td>
                <td><?=$p->first_name." ".$p->last_name?></td>
                <td><?=$p->position?></td>
                <td><?=number_format($p->points,1)?></td>
                <td><?=$p->games?></td>
                <?php if($this->year == 0):?>
                    <td>-</td>
                <?php else: ?>
                    <td><?=$p->year?></td>
                <?php endif;?>
            </tr>
        <?php endforeach;?>
        <?php
        //END VIEW
        // ******************************
    }
}
