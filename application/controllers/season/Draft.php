<?php
class Draft extends MY_User_Controller{

    private $per_page = 6;

    function __construct()
    {
        parent::__construct();
        $this->load->model('season/draft_model');
        $this->load->model('player_search_model');
        $this->bc[$this->current_year." Season"] = "";
        $this->bc['Draft'] = "";
    }

    function index()
    {
        $data = array();
        $settings = $this->draft_model->get_settings();
        $data['start_time'] = $settings->scheduled_draft_start_time;
        $data['per_page'] = $this->per_page;
        $data['sort'] = array('a'=>'A->Z','z'=>'Z->A', 'nfl_team'=>'NFL Team');
        $data['pos'] = $this->player_search_model->get_nfl_positions_data();
        $data['draft_end'] = $settings->draft_end;
        $data['years'] = $this->draft_model->get_draft_years_array();

        //if ($data['start_time'] < time())
            $this->user_view('user/season/draft',$data);
        //else
        //    $this->user_view('user/season/draft/live',$data);
    }

    function ajax_get_draft_results()
    {
        $year = $this->input->post('year');
        $draft_rows = $this->draft_model->get_draft_results($year);


        // DISPLAY THE VIEW
        ?>
        <?php foreach($draft_rows as $row): ?>
            <tr>
                <td><b><?=$row->overall_pick?>.</b> <?=$row->round.'-'.$row->pick?></td>
                <td><?=$row->first_name[0].'. '.$row->last_name?></td>
                <td><?=$row->pos?></td>
                <td><?=$row->club_id?></td>
                <td><?=$row->owner_first.' '.$row->owner_last?></td>
            </tr>
        <?php endforeach;?>


        <?php
        // VIEW FINISHED
    }

    function live()
    {
        $data = array();
        if ($this->session->userdata('offseason'))
        {
            $this->bc['Draft'] = site_url('season/draft');
            $this->bc[$this->current_year.' Live'] = "";
            $this->user_view('user/offseason',$data);
        }
        else
        {
            $this->load->model('myteam/myteam_settings_model');
            
            $current_time = time();
            $settings = $this->draft_model->get_settings();

            // Recent/current pick data
            $data['current_pick'] = $this->draft_model->get_current_pick_data();
            $data['picks'] = $this->draft_model->get_recent_picks_data();

            // if($settings->draft_start_time <= $current_time) // Draft has started
            // {
            //     $current_pick = $this->draft_model->get_current_pick_data();
            // }

            $data['start_time'] = $settings->scheduled_draft_start_time;
            $data['per_page'] = $this->per_page;
            $data['sort'] = array('a'=>'A->Z','z'=>'Z->A', 'nfl_team'=>'NFL Team');
            $data['pos'] = $this->player_search_model->get_nfl_positions_data();

            $data['scheduled_start_time'] = $settings->scheduled_draft_start_time;
            $data['start_time'] = $settings->draft_start_time;
            $data['current_time'] = $current_time;
            $data['paused'] = false;
            $data['draft_end'] = $settings->draft_end;
            if ($settings->draft_paused > 0)
                $data['paused'] = true;

            if(($settings->scheduled_draft_start_time > $current_time && ($settings->draft_start_time == 0 || $settings->draft_start_time > $current_time)))
                $data['block_title'] = 'Draft Begins';
            elseif($settings->draft_end == $this->current_year)
                $data['block_title'] = 'End of Draft';
            else
            {
                $data['seconds_left'] = $settings->draft_update_key - $current_time;
                if($data['paused'])
                {
                    $data['seconds_left'] = $this->draft_model->draft_paused();
                }
                $data['block_title'] = 'Now Picking';
                if ($data['current_pick']->logo)
                    $data['current_pick']->{'logo_url'} = $this->myteam_settings_model->get_logo_url($data['current_pick']->team_id,'thumb');
                else
                    $data['current_pick']->{'logo_url'} = $this->myteam_settings_model->get_default_logo_url();
            }



            $this->bc['Draft'] = site_url('season/draft');
            $this->bc[$this->current_year.' Live'] = "";
            $this->user_view('user/season/draft/live',$data);
        }
    }

    function pause()
    {
        if($this->is_league_admin)
        {
            $this->draft_model->pause();
        }

    }

    function unpause()
    {
        if($this->is_league_admin)
            $this->draft_model->unpause();
    }

    function start()
    {
        if($this->is_league_admin)
            $this->draft_model->start();
    }

    function undo_last_pick()
    {
        if($this->is_league_admin)
            $this->draft_model->undo_last_pick();
    }

    function ajax_get_update_key()
    {
        $this->draft_model->get_update_key();
    }

    // function test()
    // {
    //     print_r($this->draft_model->undo_last_pick());
    // }

    // function stream_get_update_key()
    // {
    //     session_write_close();
    //     $count = 10;
    //     header("Content-Type: text/event-stream\n\n");
    //     header("Cache-Control: no-cache\n\n");
    //     while(1)
    //     {
    //         $data = $this->draft_model->get_update_key();
    //         echo "data: ".$data['p']."-".$data['k']."\n\n";
    //         ob_flush(); // Needed to add this after moving to centos, no idea why.
    //         flush();
    //         usleep(500000); //half a second
    //         $count--;
    //     }
    // }

    // function ajax_get_watch_list()
    // {
    //     $pos = $this->input->post('pos');
    //     $data = array();
    //     $data['draft_team_id'] = $this->draft_model->get_draft_team_id();
    //     $data['team_id'] = $this->teamid;
    //     $data['players'] = $this->draft_model->get_watch_list($pos);
    //     $data['paused'] = $this->draft_model->draft_paused();
    //     $this->load->view('user/season/draft/ajax_get_watch_list', $data);
    // }

/*
    function ajax_reset_player_ranks()
    {
        $response = array('success' => false);
        $this->draft_model->reset_player_ranks();
        $response['success'] = true;

        echo json_encode($response);
    }
*/
    function ajax_clear_watch_list()
    {
        $response = array('success' => false);
        $this->draft_model->clear_watch_list();
        $response['success'] = true;

        echo json_encode($response);
    }

    function ajax_get_myteam()
    {
        $data = array('success' => False);
        $view_data = array();
        $view_data['players'] = $this->draft_model->get_myteam();
        $view_data['byeweeks'] = $this->common_model->get_byeweeks_array();
        $data['success'] = True;
        $data['html'] = $this->load->view('user/season/draft/ajax_get_myteam',$view_data,True);

        echo json_encode($data);
    }

    function ajax_get_recent_picks()
    {
        $data = array();
        $data['picks'] = $this->draft_model->get_recent_picks_data();
        $data['current_pick'] = $this->draft_model->get_current_pick_data();
        $this->load->view('user/season/draft/ajax_get_recent_picks',$data);
    }

    function ajax_get_draft_table()
    {
        $in_page = $this->input->post('page');
        $in_pos = $this->input->post('sel_pos');
        $in_sort = $this->input->post('sel_sort');
        $in_search = $this->input->post('search');

        $data = array();

        $data['page'] = ($in_page == '') ? 0 : $in_page;
        $data['sel_pos'] = ($in_pos == '') ? '0' : $in_pos;
        $data['sel_sort'] = ($in_sort == '') ? 'a' : $in_sort;
        $data['search'] = $in_search;

        if ($data['sel_sort'] == 'points')
            $order_by = array('points','desc');
        if ($data['sel_sort'] == 'a')
            $order_by = array('last_name', 'asc');
        if ($data['sel_sort'] == 'z')
            $order_by = array('last_name', 'desc');
        if ($data['sel_sort'] == 'nfl_team')
            $order_by = array('club_id','asc');

        $nfl_players = $this->draft_model->get_available_players_data($this->per_page, $data['page']*$this->per_page, $data['sel_pos'], $order_by, $data['search']);

        $data['total_players'] = $nfl_players['count'];
        $data['players'] = $nfl_players['result'];
        $data['per_page'] = $this->per_page;
        $data['draft_team_id'] = $this->draft_model->get_draft_team_id();
        $data['team_id'] = $this->teamid;
        $data['paused'] = $this->draft_model->draft_paused();

        $this->load->view('user/season/draft/ajax_get_draft_table', $data);
    }

    function toggle_watch_player()
    {
        $player_id = $this->input->post('player_id');
        $this->draft_model->toggle_watch_player($player_id);
        $this->draft_model->order_watch_list();
    }

    function watch_player_up()
    {
        $player_id = $this->input->post('player_id');
        $this->draft_model->watch_player_order_change($player_id, 'up');
    }

    function watch_player_down()
    {
        $player_id = $this->input->post('player_id');
        $this->draft_model->watch_player_order_change($player_id, 'down');
    }

    function draft_player()
    {

        $player_id = $this->input->post('player_id');
        $admin_pick = $this->input->post('admin_pick');

        if ($this->draft_model->player_available($player_id) && $this->draft_model->is_teams_pick() && !$this->draft_model->draft_paused())
        {
            $this->draft_model->draft_player($player_id);
            $this->draft_model->order_watch_list();

        }
        elseif ($this->is_league_admin && $admin_pick == true && $this->draft_model->player_available($player_id))
        {
            $this->draft_model->draft_player($player_id,true);
            $this->draft_model->order_watch_list();
        }
    }
}
