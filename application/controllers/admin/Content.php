<?php

class Content extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/content_model');
        $this->load->model('admin/admin_security_model');
        $this->bc["League Admin"] = "";
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
        if ($text_id == "news")
            redirect('admin/content/news');
        $data = array();
        $data['content'] = $this->content_model->get_page_data($text_id);
        $data['text_id'] = $text_id;
        $this->bc['Content'] = site_url('admin/content');
        $this->bc[$text_id] = "";
        $this->admin_view('admin/content/view',$data);

    }

    function news()
    {
        $data = array();
        $data['content'] = $this->content_model->get_news_data();
        $this->bc['Content'] = site_url('admin/content');
        $this->bc['News'] = "";
        $this->admin_view('admin/content/viewnews',$data);

    }

    function edit_news($id)
    {
        $data = array();
        $data['content'] = $this->content_model->get_page_data(null,$id);
        $this->bc['Content'] = site_url('admin/content');
        $this->bc['News'] = site_url('admin/content/news');
        $this->bc["Edit"] = "";
        $this->admin_view('admin/content/edit',$data);
    }

    function delete_news($id)
    {
        $data = array();
        $this->content_model->delete_content_item($id);
        redirect('admin/content');
    }

    function create($text_id)
    {
        if ($this->content_model->ok_to_save($text_id))
        {
            $id = $this->content_model->save_content(0,$text_id);
            if($text_id == "news")
                redirect('admin/content/edit_news/'.$id);
            else
                redirect('admin/content/view/'.$text_id);
        }
    }

    function loadcontent()
    {
        $id = $this->input->post('id');
        echo $this->content_model->get_page_data(null,$id)->data;
    }

    function savecontent()
    {
        // save_content($content_id, $text_id='',$content='',$title='', $date_posted = 0)
        $content_id = $this->input->post('content_id');
        $content = $this->input->post('content');
        $title = $this->input->post('title');
        $this->content_model->save_content($content_id,null,$content,$title);
        echo 'done';
    }
}

?>
