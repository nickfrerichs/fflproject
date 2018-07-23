<?php
class Player_search extends MY_User_Controller{

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

    function ajax_admin_start_get_player_list()
    {
        if ($this->session->userdata('is_league_admin'))
        {
            $nfl_players = $this->player_search_model->get_nfl_players($this->per_page, $this->data['page']*$this->per_page, $this->data['sel_pos'], $this->order_by, $this->data['search'], true);
            $this->load->model('myteam/myteam_roster_model');
            $this->data['total'] = $nfl_players['count'];
            $this->data['players'] = $nfl_players['result'];
            $this->data['per_page'] = $this->per_page;
            $this->data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();

            $year = $this->input->post('var1');

            $pos_lookup = $this->common_model->get_leapos_lookup_array($year);
        }

        // BEGIN VIEW
        ?>
        <?php foreach ($this->data['players'] as $p): ?>
            <tr>
                <td><?=$p->first_name?> <?=$p->last_name?></td>
                <td><?=$p->position?></td>
                <td><?=$p->club_id?></td>
                <td>
                    <?php foreach($pos_lookup as $posid => $pl): ?>
                        <?php if(in_array($p->nfl_position_id, explode(",",$pl['list']))): ?>
                            <button class="button small admin-start-button" data-id="<?=$p->player_id?>" data-posid="<?=$posid?>"><?=$pl['pos_text']?></button>
                        <?php endif;?>
                    <?php endforeach;?>
                </td>
            </tr>
        <?php endforeach;?>
        <tr id="main-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$this->data['total']?>">
        </tr>


        <?php
        // END VIEW
        // **************************************
        
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
        $data['admin_pick'] = false;
        if ($this->input->post('var1') == "true")
            $data['admin_pick'] = true;

        $data['byeweeks'] = $this->common_model->get_byeweeks_array();

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

        $this->data['byeweeks'] = $this->common_model->get_byeweeks_array();

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
        $this->data['byeweeks'] = $this->common_model->get_byeweeks_array();
        //$this->load->view('user/myteam/waiverwire/ajax_pickup_table',$data);

        // BEGIN VIEW
        $this->load->view('player_search/ajax_ww_player_list',$this->data);
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
        $this->data['byeweeks'] = $this->common_model->get_byeweeks_array();

        //$this->load->view('player_search/ajax_full_player_list',$this->data);

        // **************************************
        // BEGIN VIEW
        ?>

        <?php foreach($this->data['players'] as $p):?>
            <tr>
                <td>
                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a>
                </td>
                <td><?=$p->position?></td>
                <td><?=$p->club_id?></td>
                <td><?=$this->data['matchups'][$p->club_id]['opp']?></td>
                <td><span class="hide-for-small-only">Week </span><?=$this->data['byeweeks'][$p->club_id]?></td>
                <td><?=$p->points?></td>
                <td><?=$p->team_name?></td>
                <?php if($this->session->userdata('use_draft_ranks')): ?>
                <td><?=$p->draft_rank?></td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
        <tr id="main-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$this->data['total']?>">
        </tr>

        <?php
        // END VIEW
        // **************************************
    }

    // MOVED TO LOAD CONTENT
    function ajax_team_history_record()
    {
        $this->load->model('league/history_model');
        $teams = $this->history_model->get_team_record($this->year);
        // print_r($teams);

        ?>
        <?php foreach($teams as $num => $t): ?>
            <tr>
                <td><?=$num+1?></td>
                <td><div><?=$t->first_name." ".$t->last_name?></div><div style="font-size:.8em"><?=$t->team_name?></div></td>
                <td><?=str_replace('0.','.',number_format($t->win_pct,3))?></td>
                <td><?=number_format($t->avg_points,2)?> / <?=number_format($t->avg_opp_points,2)?> <span style="font-size:.7em"> <?=number_format($t->avg_diff,2)?></span></td>
                <td><?=$t->wins?>-<?=$t->losses?>-<?=$t->ties?></td>
                <td><?=$t->points?></td>
                <td><?=$t->opp_points?></td>

            </tr>
        <?php endforeach;?>
        <?php
    }

    function ajax_news_ww_activity()
    {
        if ($this->leagueid == "")
        {
            echo '<div class="text-center" style="font-style:italic">Nothing recent to report</div>';
        }
        else
        {
            $this->per_page = 3;
            $data = $this->waiverwire_model->get_log_data($this->current_year,$this->per_page, $this->data['page']*$this->per_page, 3);
            $waiverwire_log = $data['result'];
            ?>

            <?php if (count($waiverwire_log) > 0): ?>
                <br>
                <?php foreach($waiverwire_log as $i=>$w): ?>
                  <div>
                      <span><?=$w->team_name?></span> <span class="date"><?=date("M j g:i a",$w->transaction_date)?></span><br>
                      <span><strong>Add: </strong><?=$w->pickup_short_name?> <?=$w->pickup_pos?> <?=$w->pickup_club_id?></span><br>
                      <span><strong>Drop: </strong><?=$w->drop_short_name?> <?=$w->drop_pos?> <?=$w->drop_club_id?></span>
                  </div>
                  <?php if($i+1 < count($waiverwire_log)): ?>
                      <hr>
                <?php endif;?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center" style="font-style:italic">Nothing to report</div>
            <?php endif;?>
            <div id="news-ww-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$data['total']?>"></div>

            <?php
        }
    }

    function ajax_news_trade_activity()
    {
        if ($this->leagueid > 0)
        {
        $this->load->model('myteam/trade_model');
        $this->per_page = 3;
        $data = $this->trade_model->get_trade_log_array($this->current_year,$this->per_page, $this->data['page']*$this->per_page,3);
        $log = $data['log'];
        ?>
        <?php if (count($log) > 0): ?>
            <br>
            <?php $i = 0; ?>
            <?php foreach((array)$log as $l): ?>

              <div class="row">
                <div class="columns">
                   <span class="date"><?=date("n/j g:i a",$l['completed_date'])?></span>
                </div>
            </div>
              <div class="row">
                <?php foreach($l['teams'] as $team): ?>
                <div class="columns">
                  <?=$team['team_name']?> gets
                    <?php foreach($team['players'] as $p): ?>
                        <div class="columns small-12" style="font-style:italic"><?=$p->first_name.' '.$p->last_name?></div>
                    <?php endforeach;?>
                    <?php foreach($team['picks'] as $p): ?>
                        <div class="columns small-12" style="font-style:italic">Yr: <?=$p->year.', Rnd: '.$p->round?></div>
                    <?php endforeach;?>
                </div>
                <?php endforeach; ?>
              </div>
              <?php if($i+1 < count($log)): ?>
                  <hr>
            <?php endif;?>
            <?php $i++;?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center" style="font-style:italic">Nothing recent to report</div>
        <?php endif;?>
        <div id="news-trade-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$data['total']?>"></div>

        <?php

        }
        else
        {
            echo '<div class="text-center" style="font-style:italic">Nothing to report</div>';
        }
    }

    function ajax_news_news_list()
    {
        $this->per_page = 3;
        $this->load->model('league/news_model');
        $result = $this->news_model->get_news_data($this->per_page, $this->data['page']*$this->per_page);
        $news = $result['news'];




        // The view
        ?>
        <?php if(count($news) == 0): ?>
            <div class="section callout" style="min-height:313px;">
               <h5 class="title"> League News </h5>

            <div class="news-body">
                Nothing to report.
            </div>
            </div>
        <?php else: ?>
            <?php foreach($news as $n): ?>
            <div class="section callout">

            <h5 class="title">
                <?=$n->title?>
            </h5>
            <div class="date"><?=date("M j g:i a",$n->date_posted)?></div>

            <div class="news-body">
                <?=$n->data?>
            </div>
            </div>
            <?php endforeach; ?>
        <?php endif;?>
        <div id="news-news-list-data" data-page="<?=$this->in_page?>" data-perpage="<?=$this->per_page?>" data-total="<?=$result['total']?>"></div>

        <?php
        // End view

    }

}
