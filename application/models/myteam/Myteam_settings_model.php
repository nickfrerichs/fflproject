<?php

class Myteam_settings_model extends MY_Model{

    function __construct(){
        parent::__construct();
        $this->teamid = $this->session->userdata('team_id');
        $this->current_year = $this->session->userdata('current_year');
        $this->current_week = $this->session->userdata('current_week');
        $this->ownerid = $this->session->userdata('owner_id');
        $this->userid = $this->session->userdata('user_id');
        $this->logopath = FCPATH.'images/team_logos/';

    }

    function get_team_info($id = 0)
    {
        if ($id == 0)
            $id = $this->teamid;
        return $this->db->select('long_name, team_abbreviation, logo')
            ->from('team')->where('id',$id)->get()->row();
    }

    function get_owner_info($id = 0)
    {
        if ($id == 0)
            $id = $this->ownerid;

        return $this->db->select('owner.first_name, owner.last_name, owner.phone_number, chat_balloon, user_accounts.email as email')->from('owner')
            ->join('owner_setting','owner_setting.owner_id = owner.id')
            ->join('user_accounts','user_accounts.id=owner.user_accounts_id')
            ->where('owner.id',$id)->get()->row();
    }

    function get_owner_identity($id = 0)
    {
        if ($id == 0)
            $id = $this->ownerid;
        $user_id = $this->db->select('user_accounts_id')->from('owner')->where('id',$id)->get()->row()->user_accounts_id;
        return $this->db->select('username')->from('user_accounts')->where('user_accounts.id',$user_id)->get()->row()->username;
    }

    function member_of_league($leagueid)
    {
        $num = $this->db->from('team')->where('league_id',$leagueid)->where('owner_id',$this->ownerid)
            ->count_all_results();

        if ($num > 0)
            return True;
        return False;
    }

    function set_current_league($leagueid)
    {
        $this->db->where('id',$this->ownerid);
        $this->db->update('owner',array('active_league' => $leagueid));
    }

    function get_logo_path($teamid = '', $type = '')
    {
        if ($teamid == '' || $teamid == 0 || $teamid == false)
            $teamid = $this->teamid;
        $path = '/www/html/ff.mylanparty/images/team_logos/';
        //return $path.$teamid.'_uploaded_logo.jpg'

        if ($type == "thumb")
            return $path.$teamid."_thumb_logo.jpg";

        return $path;

    }

    function get_logo_url($teamid = '', $type = '')
    {
        if ($teamid == '' || $teamid == 0 || $teamid == false)
            $teamid = $this->teamid;
        $path = 'images/team_logos/';
        //return $path.$teamid.'_uploaded_logo.jpg'

        if ($type == "thumb")
            return site_url($path.$teamid."_thumb_logo.jpg?nocache=".time());
        if ($type == "med")
            return site_url($path.$teamid."_med_logo.jpg?nocache=".time());
        return site_url($path);

    }

    function get_default_logo_url($type = '')
    {
        $path = 'images/team_logos/';
        if ($type = "thumb")
            return site_url($path."default_thumb_logo.jpg?nocache=".time());
        if ($type = "med")
            return site_url($path."med_thumb_logo.jpg?nocache=".time());
    }

    function save_uploaded_logo($tempname, $filename)
    {
        // Convert to jpg, save in team_logos
        move_uploaded_file($tempname,$this->logopath.$filename);
        $this->convert_and_save_logo($this->logopath.$filename);

        //$team_logo_path = $this->logopath.$this->teamid."_uploaded_logo.jpg";

        // The rest of this should happen after it's cropped.

        // Save med size as jpg
        //$this->make_logo_resizes($this->teamid);

        // Save thumb size as jpg


    }

    function convert_and_save_logo($originalImage)
    {
        // jpg, png, gif or bmp?
        $exploded = explode('.',$originalImage);
        $ext = $exploded[count($exploded) - 1];

        if (preg_match('/jpg|jpeg/i',$ext))
            $imageTmp=imagecreatefromjpeg($originalImage);
        else if (preg_match('/png/i',$ext))
            $imageTmp=imagecreatefrompng($originalImage);
        else if (preg_match('/gif/i',$ext))
            $imageTmp=imagecreatefromgif($originalImage);
        else if (preg_match('/bmp/i',$ext))
            $imageTmp=imagecreatefrombmp($originalImage);
        else
            return 0;

        $outputImage = $this->logopath.$this->teamid.'_uploaded_logo.jpg';
        $quality = 75;

        // quality is a value from 0 (worst) to 100 (best)
        imagejpeg($imageTmp, $outputImage, $quality);
        imagedestroy($imageTmp);
        unlink($originalImage);

        return 1;
    }

    function crop_uploaded_logo($data)
    {
        //x,y,width,height,rotate
        $dst_w = $src_w = $data['width'];
        $dst_h = $src_h = $data['height'];
        $dst_y = $dst_x = 0;
        $src_y = $data['y'];
        $src_x = $data['x'];

        $team_logo_path = $this->logopath.$this->teamid.'_uploaded_logo.jpg';

        echo($dst_x." ".$dst_y);
        $dst_img = imagecreatetruecolor($dst_w, $dst_h);
        $src_img = imagecreatefromjpeg($team_logo_path);

        imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y,
                $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        $this->make_logo_resizes($dst_img);

        $d = array('logo' => true);
        $this->db->where('id',$this->teamid);
        $this->db->update('team',$d);
    }

    function make_logo_resizes($source)
    {
        $team_logo_path = $this->logopath.$this->teamid."_uploaded_logo.jpg";

        //print_r($source);
        $width = imagesx($source);
        $height = imagesy($source);
        //list($width, $height) = getimagesize($team_logo_path);

        $sizes = array();
        $sizes[] = array('h' => 400,
                         'w' => 400,
                         'out' => $this->logopath.$this->teamid."_med_logo.jpg");

        $sizes[] = array('h' => 100,
                         'w' => 100,
                         'out' => $this->logopath.$this->teamid."_thumb_logo.jpg");


        foreach($sizes as $s)
        {
            $temp = imagecreatetruecolor($s['w'],$s['h']);
            //$source = imagecreatefromjpeg($team_logo_path);

            imagecopyresized($temp, $source, 0, 0, 0, 0, $s['w'], $s['h'], $width, $height);
            imagejpeg($temp, $s['out']);
            imagedestroy($temp);
        }
    }

    function change_owner_phone($value)
    {
        $data = array('phone_number' => $value);
        $this->db->where('id',$this->ownerid);
        $this->db->update('owner',$data);

        $data = array('phone' => $value);
        $this->db->where('id',$this->userid);
        $this->db->update('user_accounts',$data);

        return $value;
    }

    function change_team_name($value)
    {
        $short_name = substr($value,0,30);
        $data = array('long_name' => $value,
                      'team_name' => $short_name);
        $this->db->where('id', $this->teamid);
        $this->db->update('team',$data);

        return $short_name;
    }

    function change_team_abbreviation($value)
    {
        $data = array('team_abbreviation' => $value);
        $this->db->where('id', $this->teamid);
        $this->db->update('team',$data);

        return $value;
    }

    function change_owner_email($value)
    {
        $data = array('email' => $value);
        $this->db->where('user_accounts.id',$this->session->userdata('user_id'));
        $this->db->update('user_accounts',$data);
        return $value;
    }

    function change_owner_lastname($value)
    {
        $data = array('last_name' => $value);
        $this->db->where('id',$this->ownerid);
        $this->db->update('owner',$data);

        $this->db->where('id',$this->userid);
        $this->db->update('user_accounts',$data);
    }

    function change_owner_firstname($value)
    {
        $data = array('first_name' => $value);
        $this->db->where('id',$this->ownerid);
        $this->db->update('owner',$data);

        $this->db->where('id',$this->userid);
        $this->db->update('user_accounts',$data);
    }

    function toggle_chat_balloon()
    {
        $val = !$this->db->select('chat_balloon')->from('owner_setting')
            ->where('owner_id',$this->ownerid)->get()->row()->chat_balloon;
        $this->db->where('owner_id',$this->ownerid);
        $this->db->update('owner_setting',array('chat_balloon' => $val));
        return $val;
    }

}
?>
