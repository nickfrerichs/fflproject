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
        if ($text_id == "playoffs")
            redirect('admin/content/postseason');
        $data = array();
        $data['content'] = $this->content_model->get_page_data($text_id);
        $data['text_id'] = $text_id;
        $this->bc['Content'] = site_url('admin/content');
        $this->bc[$text_id] = "";
        $this->admin_view('admin/content/view',$data);

    }

    function postseason($year = 0)
    {
        $data = array();


        if ($year == 0)
            $year = $this->current_year;
        
        $data['selected_year'] = $year;
        $data['content'] = $this->content_model->get_postseason_data($year);
        $data['years'] = $this->common_model->get_league_years();

        $this->bc['Content'] = site_url('admin/content');
        $this->bc['Post Season'] = site_url('admin/content/postseason');
        $this->bc[$year] = "";
        $this->admin_view('admin/content/view_postseason',$data);

    }

    function edit_postseason($year = 0)
    {
        $data = array();
        $data['selected_year'] = $year;
        if($year == 0)
            $year = $this->current_year;

        $this->bc['Content'] = site_url('admin/content');
        $this->bc['Post Season '.$year] = "";

        $data['content'] = $this->content_model->get_postseason_data($year);
        if (empty($data['content']))
            redirect(site_url('admin/content/postseason'));

        $this->admin_view('admin/content/edit',$data);

    }

    function create_postseason($year = 0)
    {
        if ($year == 0)
            $year = $this->current_year;
        $this->content_model->create_postseason_content($year);
        redirect(site_url('admin/content/edit_postseason/'.$year));
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
        redirect('admin/content/news');
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
