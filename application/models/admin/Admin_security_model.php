<?php

class Admin_security_model extends MY_Model
{
    function is_league_admin()
    {
        $data = $this->db->select('league_admin_id')
            ->from('league_admin')
            ->where('league_id', $this->leagueid)
            ->where('league_admin_id', $this->userid)
            ->get();
        if ($data->num_rows() > 0)
            return true;
        return false;
    }

    function is_position_in_league($id)
    {
        $data = $this->db->select('id')
            ->from('position')
            ->where('league_id', $this->leagueid)
            ->where('position.id', $id)
            ->get();
        if ($data->num_rows() > 0)
            return true;
        return false;
    }

    function is_team_in_league($id)
    {
        $data = $this->db->select('league_id')
        ->from('team')
        ->where('id', $id)
        ->where('league_id', $this->leagueid)
        ->get();
    if ($data->num_rows() > 0)
        return true;
    return false;
    }

    function positions_defined()
    {
        $pos_year = $this->common_model->league_position_year();
        $count = $this->db->from('position')->where('league_id',$this->leagueid)->where('year',$pos_year)->count_all_results();
        if ($count > 0)
            return True;
        return False;
    }

    function scoring_defs_defined()
    {
        $def_year = $this->common_model->scoring_def_year();
        $count = $this->db->from('scoring_def')->where('league_id',$this->leagueid)->where('year',$def_year)->count_all_results();
        if ($count > 0)
            return True;
        return False;
    }

    function max_teams_set()
    {
        $row = $this->db->select('max_teams')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row();
        if (count($row) > 0 && $row->max_teams > 0)
            return True;
        return False;
    }

    function max_roster_set()
    {
        $row = $this->db->select('roster_max')->from('league_settings')->where('league_id',$this->leagueid)
            ->get()->row();
        if (count($row) > 0 && $row->roster_max != 0)
            return True;
        return False;
    }

    function league_admins_missing()
    {
        $this->load->model('admin/site_model');
        $leagues = $this->site_model->get_leagues();
        $noadmins = False;
        foreach($leagues as $l)
        {
            if (count($l['admins']) == 0)
                $noadmins = True;
        }
        return $noadmins;
    }
    function get_admin_notifications()
    {
        # class: is the foundation css class for a callout
        # message: is what will be displayed to the user
        # id: can be whatever you want, but it should be unique site-wide vs all css classes

        $messages = array();

        // Messages for site admin
        if ($this->session->userdata('is_site_admin'))
        {
            if ($this->league_admins_missing())
            {
                $messages[] = array('class'=>'warning',
                                    'message'=>'Some leagues have no admins assigned.'.
                                    '<br><a href="'.site_url('admin/site/manage_leagues').'">Manage League Settings</a>',
                                    'id'=>'msg_site_admins_missing');   
            }
        }

        // Messages for league admin (is_owner)
        if ($this->session->userdata('is_league_admin'))
        {
            if (!$this->max_teams_set() && $this->leagueid != 0)
            {
                $messages[] = array('class'=>'warning',
                                    'message'=>'Max active teams is set to 0. '.
                                                '<br><a href="'.site_url('admin/leaguesettings').'">Manage League Settings</a>',
                                    'id'=>'msg_admin_no_max_teams');
            }

            if (!$this->max_roster_set() && $this->leagueid != 0)
            {
                $messages[] = array('class'=>'warning',
                                    'message'=>'Roster max is set to 0. '.
                                                '<br><a href="'.site_url('admin/leaguesettings').'">Manage League Settings</a>',
                                    'id'=>'msg_admin_no_max_roster');
            }

            if (!$this->positions_defined() && $this->leagueid != 0)
            {
                $messages[] = array('class'=>'warning',
                                    'message'=>'You have no positions defined for your league. '.
                                                '<br><a href="'.site_url('admin/positions').'">Manage League Positions</a>',
                                    'id'=>'msg_admin_no_pos');
            }

            if (!$this->scoring_defs_defined() && $this->leagueid != 0)
            {
                $id = "msg_admin_no_scoring_defs";
                $messages[] = array('class'=>'warning',
                                    'message'=>'You have no scoring definitions defined for your league.'.
                                                '<br><a href="'.site_url('admin/scoring').'" data-ackurl="'.
                                                site_url('common/message_ack/'.$id).'" class="_notification-close">Manage Scoring Definitions</a>',
                                    'id' => $id);
            }

            }
        return $messages;

    }
}
