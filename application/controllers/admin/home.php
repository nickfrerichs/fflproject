<?php

class Home extends MY_Admin_Controller
{
    function index()
    {
        $this->admin_view('admin/home');
    }
}