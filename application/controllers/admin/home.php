<?php

class Home extends MY_Admin_Controller
{
    function index()
    {
    	redirect('admin/teams');
        $this->admin_view('admin/home');
    }
}