<?php

class Account_model extends CI_Model{

    function add_team($email_address, $team_name, $league_id)
    {
        $user_id = $this->db->select('uacc_id')->from('user_accounts')->
                where('uacc_email', $email_address)->get()->row()->uacc_id;
        $data = array('owner_id' => $user_id, 'league_id' => $league_id, 'team_name' => $team_name);
        $this->db->insert('team', $data);
    }

    function add_owner($email_address, $first_name, $last_name)
    {
        $user_id = $this->db->select('uacc_id')->from('user_accounts')->
            where('uacc_email', $email_address)->get()->row()->uacc_id;
        $data = array('user_accounts_id' => $user_id, 'first_name' => $first_name,
                    'last_name' => $last_name);
        $this->db->insert('owner', $data);
    }

    function get_email_from_id($id)
    {
        return $this->db->select('uacc_email')->from('user_accounts')->where('uacc_id',$id)->get()->row()->uacc_email;
    }

    function get_league_id($code)
    {
        $result = $this->db->select('leauge_id')->from('league_settings')->where('register_code',$code)->get()->result();
        if(count($result) > 0)
            return $result->leauge_id;
        return -1;
    }

    function admin_account_exists()
    {
        if ($this->db->from('user_accounts')->where('uacc_group_fk',1)->get()->num_rows() > 0)
            return True;
        return False;
    }
}
