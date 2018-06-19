<?php

class Roster extends MY_User_Controller{

    function __construct()
    {
        parent::__construct();
        $this->load->model('myteam/myteam_roster_model');
        $this->load->model('myteam/myteam_settings_model');
        $this->load->model('myteam/schedule_model');
        $this->bc['My Team'] = "";
        $this->bc['Roster'] = "";
    }

    function index()
    {
        $data['ajax_wait'] = true;
        //$roster = $this->myteam_roster_model->get_roster_data();
        //$nfl_pos = $this->myteam_roster_model->get_nfl_positions_array();
        //$lea_pos = $this->myteam_roster_model->get_league_positions_data();
        $data['schedule'] = $this->schedule_model->get_team_schedule();
        $data['info'] = $this->myteam_settings_model->get_team_info();
        $data['teamid'] = $this->teamid;
        $data['teamname'] = $this->team_name;
        $data['weeks'] = $this->myteam_roster_model->get_weeks_left();
        $data['logo_thumb_url'] = $this->myteam_settings_model->get_logo_url(0,"thumb");
        $data['record'] = $this->myteam_roster_model->get_team_record_data();
        $data['keepers_num'] = $this->myteam_roster_model->get_keepers_num();

        $this->user_view('user/myteam/roster', $data);

    }

    function start()
    {
        if (!$this->offseason)
        {
            $player_id = $this->input->post('player_id');
            $lea_pos = $this->input->post('pos_id');
            $week = $this->input->post('week');

            if( $this->myteam_roster_model->ok_to_start($lea_pos,$player_id,$week))
            {
                $this->myteam_roster_model->start_player($player_id, $lea_pos,$week);
            }
        }

    }

    function ajax_starter_table()
    {
        //$data['roster'] = $this->myteam_roster_model->get_roster_data();
        //$starters = $this->myteam_roster_model->get_starters_data();
        //$data['nfl_pos'] = $this->myteam_roster_model->get_nfl_positions_array();
        $week = $this->input->post('week');
        $lea_pos = $this->myteam_roster_model->get_league_positions_data();
        $data['lea_pos'] = $this->myteam_roster_model->get_league_positions_data();
        $starters_data = $this->myteam_roster_model->get_starters_data($this->teamid, $week);
        $starters = array();

        // Blank starters array, if no max for a pos use the number of starters at that position as the limit for the for loop
        foreach ($lea_pos as $l)
        {
            $starters[$l->id]['pos'] = $l->text_id;
            if ($l->max_start == -1)
            {

                foreach($starters_data as $key => $s)
                {
                    if ($s->starting_position_id == $l->id)
                    {
                        $starters[$l->id]['players'][] = $s;
                        unset($starters_data[$key]);
                        continue;
                    }
                }
            }
            else
            {
                for($i = 0; $i<$l->max_start; $i++)
                {
                    foreach($starters_data as $key => $s)
                    {
                        if ($s->starting_position_id == $l->id)
                        {
                            $starters[$l->id]['players'][] = $s;
                            unset($starters_data[$key]);
                            continue 2;
                        }
                    }
                    $starters[$l->id]['players'][] = null;
                }
            }
        }
        $data['starters'] = $starters;
        $data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array($week);
        $data['byeweeks'] = $this->common_model->get_byeweeks_array();

        $this->load->view('user/myteam/roster/ajax_starter_table',$data);
    }

    function ajax_bench_table()
    {
        $week = $this->input->post('week');
        if ($week == "")
            $week = 0;
        $lea_pos = $this->myteam_roster_model->get_league_positions_data();
        $bench_data = $this->myteam_roster_model->get_bench_data($week);
        $bench = array();
        foreach ($bench_data as $b)
        {
            $bench[$b->player_id]['data'] = $b;
            foreach ($lea_pos as $pos)
            {
                // OK to start this position if these are true.
                if($week>0 && in_array($b->nfl_position_id,explode(',',$pos->nfl_position_id_list)) &&
                    ($this->myteam_roster_model->num_starters($pos->id,$this->teamid,$week) < $pos->max_start || $pos->max_start == -1))
                {
                    $bench[$b->player_id]['can_start'][$pos->id] = $pos->text_id;
                }
            }
           // if ($this->myteam_roster_model->num_starters($b->))
        }
        $data['bench'] = $bench;
        $data['matchups'] = $this->myteam_roster_model->get_nfl_opponent_array($week);
        $data['byeweeks'] = $this->common_model->get_byeweeks_array();
        $this->load->view('user/myteam/roster/ajax_bench_table',$data);
    }

    function ajax_keeper_table()
    {
        $data = array();
        $data['roster'] = $this->myteam_roster_model->get_roster_data();
        $this->load->view('user/myteam/roster/ajax_keeper_table',$data);
    }

    function toggle_keeper()
    {
        $response = array('success' => False);
        $id = $this->input->post('var1');

        if ($this->myteam_roster_model->is_player_owner($id))
        {
            $response['value'] = $this->myteam_roster_model->toggle_keeper($id); 
            $response['success'] = True;
        }

        echo json_encode($response);

    }

}
