<?php

class History extends MY_Controller{


    function __construct()
    {
        parent::__construct();
        $this->bc['League'] = "";
        $this->bc['History'] = "";
    }


    public function index()
    {
        $this->user_view('user/league/history');
    }
}
?>
