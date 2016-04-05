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

    // function get_player_table()
    // {
    //     $in_page = $this->input->post('page');
    //     $in_pos = $this->input->post('sel_pos');
    //     $in_sort = $this->input->post('sel_sort');
    //     $in_search = $this->input->post('search');
    //     $data = array();
    //
    //     $data['page'] = ($in_page == '') ? 0 : $in_page;
    //     $data['sel_pos'] = ($in_pos == '') ? '0' : $in_pos;
    //     $data['sel_sort'] = ($in_sort == '') ? 'points' : $in_sort;
    //     $data['search'] = $in_search;
    // 	$this->load->library('pagination');
    // 	$config['base_url'] = site_url('myteam/waiverwire/get_roster_table');
    // 	$config['total_rows'] = $this->player_search_model->nfl_players_count();
	// 	$config['per_page'] = $this->per_page;
	// 	$config['uri_segment'] = 4;
	// 	$config['full_tag_open'] = '<p id="pagination">';
	// 	$config['full_tag_close'] = '</p>';
	// 	$this->pagination->initialize($config);
    //
    //     if ($data['sel_sort'] == 'points')
    //         $order_by = array('points','desc');
    //     if ($data['sel_sort'] == 'a')
    //         $order_by = array('last_name', 'asc');
    //     if ($data['sel_sort'] == 'z')
    //         $order_by = array('last_name', 'desc');
    //     if ($data['sel_sort'] == 'nfl_team')
    //         $order_by = array('club_id','asc');
    //
	// 	$data['players'] = $this->player_search_model->get_nfl_players($this->per_page, $data['page']*$this->per_page, $data['sel_pos'], $order_by, $data['search'], false);
	// 	$this->load->view('player_search/player_search_table_view', $data);
    // }

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
