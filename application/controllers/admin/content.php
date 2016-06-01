<?php

class Content extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/content_model');
        $this->load->model('admin/security_model');
        $this->bc[$this->league_name] = "";
        $this->bc["Content"] = "";
    }

    function index()
    {
        $data = array();
        $this->admin_view('admin/content/content',$data);
    }

    function edit($text_id)
    {
        $data = array();
        $data['content'] = $this->content_model->get_page_data($text_id);
        $this->bc['Content'] = site_url('admin/content');
        $this->bc[$text_id] = site_url('admin/content/view/'.$text_id);
        $this->bc["Edit"] = "";
        $this->admin_view('admin/content/edit',$data);
    }

    function view($text_id)
    {
        $data = array();
        $data['content'] = $this->content_model->get_page_data($text_id);
        $data['text_id'] = $text_id;
        $this->bc['Content'] = site_url('admin/content');
        $this->bc[$text_id] = "";
        $this->admin_view('admin/content/view',$data);
    }

    function create($text_id)
    {
        if ($this->content_model->ok_to_save($text_id))
        {
            echo "ok to save";
            $this->content_model->save_content(0,$text_id);
            redirect('admin/content/view/'.$text_id);
        }
    }

    function loadcontent()
    {
        $text_id = $this->input->post('text_id');
        echo $this->content_model->get_page_data($text_id)->data;
    }

    function savecontent()
    {
        $content_id = $this->input->post('content_id');
        $content = $this->input->post('content');
        $this->content_model->save_content($content_id,null,$content);
        echo 'done';
    }
}

?>
