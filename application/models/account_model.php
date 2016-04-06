<?php

class Account_model extends CI_Model{

    function add_team($user_id, $team_name, $league_id)
    {
        // $user_id = $this->db->select('uacc_id')->from('user_accounts')->
        //         where('uacc_email', $email_address)->get()->row()->uacc_id; 

        $owner_id = $this->db->select('owner.id as owner_id')->from('user_accounts')->join('owner','owner.user_accounts_id = user_accounts.uacc_id')
            ->where('user_accounts.uacc_id',$user_id)->get()->row()->owner_id;

        $data = array('owner_id' => $owner_id, 'league_id' => $league_id, 'team_name' => $team_name, 'long_name' => $team_name);
        $this->db->insert('team', $data);
    }

    function add_owner($user_id, $first_name, $last_name)
    {
        $data = array('user_accounts_id' => $user_id, 'first_name' => $first_name,
                    'last_name' => $last_name);
        $this->db->insert('owner', $data);
    }

    function set_active_league($user_id, $leagueid)
    {
        $data = array('active_league' => $leagueid);
        $this->db->where('user_accounts_id',$user_id);
        $this->db->update('owner',$data);
    }

    function get_email_from_id($id)
    {
        return $this->db->select('uacc_email')->from('user_accounts')->where('uacc_id',$id)->get()->row()->uacc_email;
    }

    function get_league_id($maskid, $code)
    {
        $result = $this->db->select('league.id')->from('league')->join('league_settings','league_settings.league_id = league.id')
            ->where('league.mask_id',$maskid)->where('league_settings.join_password',$code)->get()->row();
        if(count($result) > 0)
            return $result->id;
        return -1;
    }

    function admin_account_exists()
    {
        if ($this->db->from('user_accounts')->where('uacc_group_fk',1)->get()->num_rows() > 0)
            return True;
        return False;
    }

    function user_is_owner($user_id)
    {
        $result = $this->db->select('id')->from('owner')->where('user_accounts_id',$user_id)->get()->result();
        if (count($result) > 0)
            return True;
        return False;
    }
}
