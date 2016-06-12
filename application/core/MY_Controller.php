<?php

class MY_Controller extends CI_Controller{
    protected $userid;
    protected $leagueid;
    protected $current_week;
    protected $current_year;
    protected $teamid;
    function __construct()
    {
        parent::__construct();
        // Initialize flexi auth (lite)
        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');
        $this->load->model('common/common_model');

        // Turn debugging on, if enabled.
        if ($this->session->userdata('debug') && !$this->input->is_ajax_request())
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE,
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE,
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => TRUE
                );
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }

        // If not logged in redirect to login page
        if (!$this->flexi_auth->is_logged_in() && !$this->input->is_ajax_request())
        {
             redirect('');
        }
        //Load session variables
        $this->userid = $this->session->userdata('user_id');
        $this->is_site_admin = $this->session->userdata('is_site_admin');
        $this->is_league_admin = $this->session->userdata('is_league_admin');
        $this->debug = $this->session->userdata('debug');

        // Owner specific session variables
        if ($this->session->userdata('is_owner'))
        {
            $this->leagueid = $this->session->userdata('league_id');
            $this->teamid = $this->session->userdata('team_id');
            $this->ownerid = $this->session->userdata('owner_id');
            $this->team_name = $this->session->userdata('team_name');
            $this->current_year = $this->session->userdata('current_year');
            $this->current_week = $this->session->userdata('current_week');
            $this->week_type = $this->session->userdata('week_type');
            $this->league_name = $this->session->userdata('league_name');
            $this->offseason = $this->session->userdata('offseason');
        }
        elseif ($this->is_site_admin)
        {
            redirect('admin');
        }

        // Breadcrumbs
        $this->bc = array();

        // This is to make sure the user session gets these vars if so much time has passed
        // since they were first set.
        if ($this->session->userdata('expire_dynamic_vars') < time())
        {
            $this->load->model('security_model');
            $this->security_model->set_dynamic_session_variables();
        }

    }

    function user_view($viewname, $d=null)
    {
        $this->load->model('menu_model');

        $d['menu_items'] = $this->menu_model->get_menu_items_data();

        $d['v'] = $viewname;
        $d['bc'] = $this->bc;

        $d['_messages'] = $this->common_model->get_user_messages();
        $this->load->view('template/user_init', $d);
    }

}


class MY_Admin_Controller extends CI_Controller{

    function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('is_owner'))
        {
            $this->current_year = $this->session->userdata('current_year');
            $this->current_week = $this->session->userdata('current_week');
            $this->league_name = $this->session->userdata('league_name');
        }
        $this->is_league_admin = $this->session->userdata('is_league_admin');
        // Initialize flexi auth (lite)
        $this->auth = new stdClass;
        $this->load->library('flexi_auth_lite', FALSE, 'flexi_auth');
        $this->is_admin = $this->flexi_auth->is_admin();

        $this->bc = array();

        // Turn debugging on, if enabled.
        if ($this->session->userdata('debug') && !$this->input->is_ajax_request())
        {
                $sections = array(
                        'benchmarks' => TRUE, 'memory_usage' => TRUE,
                        'config' => FALSE, 'controller_info' => FALSE, 'get' => FALSE, 'post' => TRUE, 'queries' => TRUE,
                        'uri_string' => FALSE, 'http_headers' => FALSE, 'session_data' => TRUE
                );
                $this->output->set_profiler_sections($sections);
                $this->output->enable_profiler(TRUE);
        }

        // If not logged in redirect to login page
        if (!$this->flexi_auth->is_logged_in() || !($this->is_admin || $this->is_league_admin))
        {
             redirect('');
        }
    }

    function admin_view($viewname, $d=null)
    {
        $this->load->model('menu_model');
        $this->load->model('admin/security_model');
        $d['_messages'] = $this->security_model->get_admin_messages();
        $d['menu_items'] = $this->menu_model->get_menu_items_data(true);
        $d['v'] = $viewname;
        $d['bc'] = $this->bc;
        $this->load->view('template/admin_init', $d);
    }
}
