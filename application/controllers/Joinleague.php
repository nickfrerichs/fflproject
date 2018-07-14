<?php

class Joinleague extends MY_Basic_Controller{

    function __construct()
    {
        parent::__construct();

        $this->load->library('ion_auth');
    }

    function invite($mask_id="")
    {
        if (!$this->ion_auth->logged_in())
        {
            // If they aren't logged in, ask them to login, or register new account
            $league_name = $this->common_noauth_model->league_name_from_mask_id($mask_id);
            $site_name = $this->common_noauth_model->get_site_name();
            $code_required = $this->common_noauth_model->join_code_required($mask_id);
            $has_room = $this->common_noauth_model->league_has_room($mask_id);
            $data = array('mask_id' => $mask_id,
                          'league_name' => $league_name,
                          'site_name' => $site_name,
                          'code_required' => $code_required,
                          'has_room' => $has_room);
            $this->basic_view('guest_invite',$data);

        }
        else
        {
            redirect('user/joinleague/'.$mask_id);
        }
    }
}

?>
