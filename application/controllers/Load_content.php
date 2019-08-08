<?php
class Load_content extends MY_User_Controller{

// This controller presents the ajax data used by various jquery ajax/post functions.
// The views dislay content to be put between <tbody></tbody> tags.


    function __construct()
    {

        parent::__construct();
        $this->load->model('myteam/waiverwire_model');
        $this->load->model('player_search_model');

        $this->data = array('success' => FALSE);

        // Defaults
        $this->limit = 10;

        if ($this->input->post('limit'))
            $this->limit = $this->input->post('limit');

        // $this->in_page = $this->input->post('page');
        // $this->in_pos = $this->input->post('pos');
        $this->by = $this->input->post('by');
        $this->order = $this->input->post('order');
        
        if ($this->input->post('checkbox'))
        {
            $this->checkbox = $this->input->post('checkbox') == "true";
        }

        $this->filters = array();
        if ($this->input->post('filters'))
        {
            foreach($this->input->post('filters') as $key => $val)
                $this->{'filter_'.$key} = $val;
            
        }
        // $this->in_search = $this->input->post('search');

        // $this->year = $this->input->post('year');
        // $this->starter = $this->input->post('starter');
        // $this->custom = $this->input->post('custom');

        // $this->order_by = array('points','desc');

        // $this->data['page'] = ($this->in_page == '') ? 0 : $this->in_page;
        // $this->data['sel_pos'] = ($this->in_pos == '') ? '0' : $this->in_pos;
        // $this->data['by'] = ($this->in_sort == '') ? 'points' : $this->in_sort;
        // $this->data['order'] = ($this->in_order == '') ? 'asc' : $this->in_order;
        // $this->data['search'] = $this->in_search;

        // $this->order_by = array($this->data['by'],$this->data['order']);

    }

    function test()
    {
        $this->load->model('season/draft_model');
        $this->draft_model->get_available_players_data(10, 0, 0, array("last_name", "asc"), "");

        $this->draft_model->get_watch_list(10, 0, 0);
    }

    function ajax_full_player_list()
    {
        if (!isset($this->filter_pos))
            $this->filter_pos = '';
        $nfl_players = $this->player_search_model->get_nfl_players($this->limit, 0, $this->filter_pos, array($this->by, $this->order), $this->filter_search, true);
 
        $this->load->model('myteam/myteam_roster_model');
        $view_data['total'] = $nfl_players['count'];
        $view_data['players'] = $nfl_players['result'];
        //$this->data['per_page'] = $this->per_page;
        $view_data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
        $view_data['byeweeks'] = $this->common_model->get_byeweeks_array();

        //$this->load->view('player_search/ajax_full_player_list',$this->data);

        $this->data['html'] = $this->load->view('load_content/full_player_list',$view_data,True);
        $this->data['total'] = $view_data['total'];
        $this->data['count'] = $this->limit;

        $this->data['success'] = True;

        echo json_encode($this->data);
    }

    function history_team_record()
    {
        $this->load->model('league/history_model');
        if (!isset($this->filter_year))
            $this->filter_year = '';

        $view_data['teams'] = $this->history_model->get_team_record($this->filter_year,$this->limit);

        $this->data['html'] = $this->load->view('load_content/history_team_record',$view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);
    }

    function history_player_career_list()
    {
        $this->load->model('player_search_model');

        if (!isset($this->filter_pos))
            $this->filter_pos = '';
        if (!isset($this->filter_year))
            $this->filter_year = '';

        // The False boolean is for showing starters only, it didn't seem to work so I hard coded it.
        $view_data['players'] = $this->player_search_model->get_career_data($this->filter_year, "all", $this->filter_pos, array($this->by, $this->order),$this->limit);
        $this->data['html'] = $this->load->view('load_content/history_player_career_list',$view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);
    }

    function history_player_best_week_list()
    {
        $this->load->model('player_search_model');

        if (!isset($this->filter_pos))
            $this->filter_pos = '';
        if (!isset($this->filter_year))
            $this->filter_year = '';

        $view_data['players'] = $this->player_search_model->get_best_week_data($this->filter_year, "all", $this->filter_pos,$this->limit);

        $this->data['html'] = $this->load->view('load_content/history_player_best_week_list',$view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);
    }

    function ww_player_list()
    {
        $this->load->model('myteam/waiverwire_model');
        $this->load->model('myteam/myteam_roster_model');

        if (!isset($this->filter_search))
            $this->filter_search = '';

        if (!isset($this->filter_pos))
            $this->filter_pos = '';

        $nfl_players = $this->waiverwire_model->get_nfl_players($this->limit, 0, $this->filter_pos, array($this->by, $this->order),$this->filter_search);
       // get_nfl_players($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',$show_owned = false)
  
        $view_data['total'] = $nfl_players['count'];
        $view_data['players'] = $nfl_players['result'];
        #$view_data['per_page'] = 10;
        #$view_data['in_page'] = 1;
        $view_data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
        $view_data['byeweeks'] = $this->common_model->get_byeweeks_array();
        //$this->load->view('user/myteam/waiverwire/ajax_pickup_table',$data);

        // BEGIN VIEW
        $this->data['total'] = $view_data['total'];
        $this->data['count'] = $this->limit;
        $this->data['html'] = $this->load->view('load_content/ww_player_list',$view_data,True);
        // END VIEW


        $this->data['success'] = True;


        echo json_encode($this->data);
    }

    function draft_player_list()
    {
        $this->load->model('season/draft_model');
        //if ($this->order_by[0] == 'points')
        //    $this->order_by[0] = 'last_name';

        if (!isset($this->filter_search))
            $this->filter_search = '';

        if (!isset($this->filter_pos))
            $this->filter_pos = '';

        if (!isset($this->checkbox))
            $this->checkbox=false;

        $nfl_players = $this->draft_model->get_available_players_data($this->limit, 0, $this->filter_pos, array($this->by, $this->order), $this->filter_search, $this->checkbox);

        $view_data['total'] = $nfl_players['count'];
        $view_data['players'] = $nfl_players['result'];
        //$data['per_page'] = $this->per_page;
        $view_data['draft_team_id'] = $this->draft_model->get_draft_team_id();
        $view_data['team_id'] = $this->teamid;
        $view_data['paused'] = $this->draft_model->draft_paused();
        $view_data['admin_pick'] = false;
        if ($this->input->post('var1') == "true")
            $view_data['admin_pick'] = true;

        $view_data['byeweeks'] = $this->common_model->get_byeweeks_array();

        $this->data['total'] = $view_data['total'];
        $this->data['count'] = $this->limit;
        $this->data['html'] = $this->load->view('user/season/draft/ajax_get_draft_table', $view_data, True);

        $this->data['success'] = True;
        echo json_encode($this->data);
    }

    function draft_watch_list()
    {
        $this->load->model('season/draft_model');

        if (!isset($this->filter_pos))
        $this->filter_pos = '';

        $view_data['draft_team_id'] = $this->draft_model->get_draft_team_id();
        $view_data['team_id'] = $this->teamid;

        $watch_players = $this->draft_model->get_watch_list($this->limit, 0, $this->filter_pos);
        $view_data['players'] = $watch_players['result'];
        $view_data['total_players'] = $watch_players['count'];
       // $view_data['per_page'] = $this->per_page;
       // $view_data['page'] = $this->in_page;
        $view_data['paused'] = $this->draft_model->draft_paused();

        $view_data['byeweeks'] = $this->common_model->get_byeweeks_array();

        $this->data['total'] = $view_data['total_players'];
        $this->data['count'] = $this->limit;
        $this->data['html'] = $this->load->view('user/season/draft/ajax_get_watch_list', $view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);
    }

    function admin_rosters_player_search()
    {

        if (!isset($this->filter_pos))
            $this->filter_pos = '';

        $this->common_model->force_league_admin();
//        function get_nfl_players($limit = 100000, $start = 0, $nfl_pos = 0, $order_by = array('last_name','asc'),$search='',
//        $show_owned = true, $show_inactive = false, $hide_non_lea = true)

        $nfl_players = $this->player_search_model->get_nfl_players($this->limit,0,$this->filter_pos, array($this->by, $this->order), $this->filter_search);
        $this->load->model('myteam/myteam_roster_model');
        $view_data['total'] = $nfl_players['count'];
        $view_data['players'] = $nfl_players['result'];
        // $view_data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();

        //$this->load->view('player_search/ajax_full_player_list',$this->data);
        $this->data['total'] = $view_data['total'];
        $this->data['count'] = $this->limit;
        $this->data['html'] = $this->load->view('load_content/admin_rosters_player_search',$view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);

    }


    function admin_lineup_player_search()
    {
        $this->common_model->force_league_admin();

        $year = $this->input->post('var1');

        if (!isset($this->filter_pos))
            $this->filter_pos = '';

        $nfl_players = $this->player_search_model->get_nfl_players($this->limit,0,$this->filter_pos, array($this->by, $this->order),$this->filter_search);
        $this->load->model('myteam/myteam_roster_model');

        $view_data['total'] = $nfl_players['count'];
        $view_data['players'] = $nfl_players['result'];
        $view_data['pos_lookup'] = $this->common_model->get_leapos_lookup_array($year);

//        $this->data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();

        $this->data['total'] = $view_data['total'];
        $this->data['count'] = $this->limit;
        $this->data['html'] = $this->load->view('load_content/admin_lineup_player_search',$view_data,True);

        $this->data['success'] = True;

        echo json_encode($this->data);        
    }

    function news_items()
    {
        $this->load->model('league/news_model');
        $news_data = $this->news_model->get_news_data($this->limit, 0);
        $this->data['html'] = $this->load->view('load_content/news_items',$news_data,True);
        $this->data['total'] = $news_data['total'];
        $this->data['count'] = count($news_data['news']);
        $this->data['success'] = true;

        echo json_encode($this->data);

    }


    function news_ww_tbody()
    {
        $this->load->model('myteam/waiverwire_model');

        if($this->leagueid == "")
        {
            $wwdata = array();
        }
        else
        {
            $wwdata = $this->waiverwire_model->get_log_data($this->current_year,$this->limit, 0);
            $this->data['count'] = count($wwdata['result']);
            $this->data['total'] = $wwdata['total'];
        }

        // $this->per_page = 3;
        // $data = $this->waiverwire_model->get_log_data($this->current_year,$this->limit, 0, 3);
        // $waiverwire_log = $data['result'];
        
        $this->data['html'] = $this->load->view('load_content/news_ww_tbody',$wwdata,True);
        $this->data['success'] = true;

        echo json_encode($this->data);
    }

    function news_moves_items()
    {
        $this->load->model('myteam/waiverwire_model');
        
        if($this->leagueid == "")
        {
            $wwdata = array();
        }
        else
        {
            $wwdata = $this->waiverwire_model->get_log_data($this->current_year,$this->limit, 0);
            $this->data['count'] = count($wwdata['result']);
            $this->data['total'] = $wwdata['total'];
        }

        // $this->per_page = 3;
        // $data = $this->waiverwire_model->get_log_data($this->current_year,$this->limit, 0, 3);
        // $waiverwire_log = $data['result'];
        
        $this->data['html'] = $this->load->view('load_content/news_moves_items',$wwdata,True);
        $this->data['success'] = true;

        echo json_encode($this->data);
            
        
    }

    function news_standings()
    {
        $this->load->model('season/standings_model');
        $view_data['divs'] = $this->standings_model->get_year_standings($this->session->userdata('year'));
        $view_data['defs'] = $this->standings_model->get_notation_defs();
        $this->data['html'] = $this->load->view('load_content/news_standings_html',$view_data,True);

        $this->data['success'] = true;

        echo json_encode($this->data);


    }

    function moneylist()
    {
        $this->load->model('season/moneylist_model');
        $view_data = array();
        $view_data['list'] = $this->moneylist_model->get_moneylist();
        $view_data['totals'] = $this->moneylist_model->get_totals();
        $this->data['html'] = $this->load->view('load_content/moneylist_html',$view_data,True);
        
        $this->data['success'] = true;

        echo json_encode($this->data);
    }
}