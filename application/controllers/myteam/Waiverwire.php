<?php

class Waiverwire extends MY_User_Controller{

    private $per_page = 6;
    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/waiverwire_model');
        $this->load->model('player_search_model');
        $this->load->model('myteam/myteam_roster_model');
        $this->bc['My Team'] = "";
        $this->bc['Waiver Wire'] = "";
    }


    function index()
    {
        $data = array('ajax_wait'=>true);
        if ($this->waiverwire_model->waiverwire_open())
        {
            $data['pos'] = $this->player_search_model->get_nfl_positions_data();
            $data['pending'] = $this->waiverwire_model->get_pending_requests();
            $this->user_view('user/myteam/waiverwire.php', $data);
        }
        else
        {
            $this->user_view('user/myteam/waiverwire/closed.php',$data);
        }

    }

    // function index_old()
    // {
    //     $data = array();
    //     if ($this->waiverwire_model->waiverwire_open())
    //     {
    //         $this->load->helper('form');
    //     	$data['roster'] = $this->waiverwire_model->get_roster_data();
    //         $data['sort'] = array('points'=>'Points','a'=>'A->Z','z'=>'Z->A', 'nfl_team'=>'NFL Team');
    //         $data['pos'] = $this->player_search_model->get_nfl_positions_data();
    //         $data['per_page'] = $this->per_page;
    //     	$this->user_view('user/myteam/waiverwire.php', $data);
    //     }
    //     else
    //     {
    //         $this->user_view('user/myteam/waiverwire/closed.php',$data);
    //     }
    // }

    function log()
    {
        $data = array();
        $d = $this->waiverwire_model->get_log_data();
        $data['log'] = $d['result'];
        $data['clear_time'] = $this->waiverwire_model->get_clear_time();
        $this->bc['Waiver Wire'] = site_url('myteam/waiverwire');
        $this->bc['Log'] = "";
        $this->user_view('user/myteam/waiverwire/showlog.php',$data);

    }

    function priority()
    {
        $data = array();
        $data['data'] = $this->waiverwire_model->get_priority_data_array();
        $this->bc['Waiver Wire'] = site_url('myteam/waiverwire');
        $this->bc['Priority'] = "";
        $this->user_view('user/myteam/waiverwire/showpriority.php',$data);
    }

    function transaction($action = "")
    {
        if (!$this->offseason)
        {
            $pickup = $this->input->post('pickup_id');
            $drop = $this->input->post('drop_id');
            $settings = $this->common_waiverwire_model->get_approval_settings($this->leagueid);

            $data = array();
            $data['drop_id'] = $drop;
            $data['pickup_id'] = $pickup;
            if ($this->waiverwire_model->ok_to_process_transaction($pickup, $drop, $error, $status_code))
            {
                if ($action == "execute") // Yes go ahead and update the database, log it.
                {
                    if ($settings->type == "manual")
                    {
                        $approve = false;
                        $this->common_waiverwire_model->send_admin_approval_notice($this->leagueid);
                        $data['manual'] = True;
                    }
                    else
                    {
                        if ($drop != "0")
                            $this->waiverwire_model->drop_player($drop);
                        if ($pickup != "0")
                            $this->waiverwire_model->pickup_player($pickup);
                        $approve = true;
                    }
                    $this->waiverwire_model->log_transaction($pickup, $drop, $approve);
                }
                $data['success'] = True;
            }
            elseif($status_code == 1 && $action == "execute")
            {
                // This puts in a request for a player who's waivers haven't cleared yet.
                $this->waiverwire_model->request_player($pickup, $drop);
                $data['success'] = True;
                $data['status_code'] = $status_code;
            }
            else {
                $data['success'] = False;
                $data['status_code'] = $status_code;
                $data['error'] = $error;
            }
            echo json_encode($data);
        }

    }

    function ajax_drop_table()
    {
        $data['roster'] = $this->waiverwire_model->get_roster_data();
        $data['per_page'] = $this->per_page;
        $data['roster_max'] = $this->waiverwire_model->get_roster_max();
        $this->load->view('user/myteam/waiverwire/ajax_drop_table', $data);
    }

    function ajax_cancel_request()
    {
        $result = array();
        $id = $this->input->post('id');
        $this->waiverwire_model->cancel_request($id);

        echo json_encode($result);
    }

    // function ajax_pickup_table()
    // {
    //     $in_page = $this->input->post('page');
    //     $in_pos = $this->input->post('sel_pos');
    //     $in_sort = $this->input->post('sel_sort');
    //     $in_search = $this->input->post('search');
    //     $this->per_page = $this->input->post('per_page');
    //     $data = array();
    //
    //     $data['page'] = ($in_page == '') ? 0 : $in_page;
    //     $data['sel_pos'] = ($in_pos == '') ? '0' : $in_pos;
    //     $data['sel_sort'] = ($in_sort == '') ? 'points' : $in_sort;
    //     $data['search'] = $in_search;
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
    //
    //     $nfl_players = $this->waiverwire_model->get_nfl_players($this->per_page, $data['page']*$this->per_page, $data['sel_pos'], $order_by, $data['search'], true);
    //
    //     $data['total_players'] = $nfl_players['count'];
    //     $data['players'] = $nfl_players['result'];
    //     $data['per_page'] = $this->per_page;
    //     $data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array();
    //     $this->load->view('user/myteam/waiverwire/ajax_pickup_table',$data);
    // }
}
