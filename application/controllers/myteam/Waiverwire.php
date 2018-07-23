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

            $data['latest_request_id'] = 0;
            if (count($data['pending']) > 1)
            {
                $recent_date = 0;
                foreach($data['pending'] as $p)
                {
                    if ($p->request_date > $recent_date)
                    {
                        $recent_date = $p->request_date;
                        $data['latest_request_id'] = $p->ww_id;
                        
                    }
                }
            }
            $this->user_view('user/myteam/waiverwire.php', $data);
        }
        else
        {
            $data['ajax_wait'] = false;
            $this->user_view('user/myteam/waiverwire/closed.php',$data);
        }

    }

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
        $data['settings'] = $this->common_waiverwire_model->get_approval_settings($this->leagueid);
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
                    // Add function to check if waiverwire pickup was less than 1 hour ago, if so, don't log it
                    $this->waiverwire_model->log_transaction($pickup, $drop, $approve);
                }

                $data['success'] = True;
            }
            elseif($status_code == 1 && $action == "execute")
            {
                // This puts in a request for a player who's waivers haven't cleared yet.
                // Add code here to request a player during time waiver wire is unavailable
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

    function ajax_make_preferred()
    {
        $ww_id = $this->input->post('id');
        $result['success'] = False;
        $this->waiverwire_model->make_preferred($ww_id);
        $result['success'] = True;
        echo json_encode($result);
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

}
