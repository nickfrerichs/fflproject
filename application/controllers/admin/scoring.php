<?php

class Scoring extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/security_model');
        $this->load->model('admin/scoring_model');
    }

    function index()
    {
        $categories = $this->scoring_model->get_scoring_cats_data();
        $scoring_defs_data = $this->scoring_model->get_values_data();

        $scoring_defs = array();
        foreach ($scoring_defs_data as $s)
        {
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['per'] = $s->per;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['points'] = $s->points;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['round'] = $s->round;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['long_text'] = $s->long_text;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['pos_text'] = $s->pos_text;
        }


        $this->admin_view('admin/scoring/scoring.php', array('categories' => $categories,
                                                            'scoring_defs' => $scoring_defs));
    }

    function add($position_id = 0)
    {
        // SECURITY: league
        $stat_id = $this->input->post('stat_id');
        if ($stat_id)
        {
            if (!$this->scoring_model->stat_value_exists($position_id,$stat_id))
                $this->scoring_model->add_stat_value_entry($stat_id, $position_id);
        }

        $this->load->helper('form');
        $categories = $this->scoring_model->get_scoring_cats_data($position_id);

        $nfl_positions = $this->scoring_model->get_nfl_positions_data();

        $this->admin_view('admin/scoring/add.php', array('cats'=>$categories,
                                                        'nfl_positions' => $nfl_positions,
                                                        'selected_pos' => $position_id));

    }

    function edit()
    {
        if ($this->input->post('save'))
        {
            $values = array();
            foreach ($this->input->post() as $key => $val)
            {
                if (stripos($key,'_') === false)
                    continue;
                $v = explode('_',$key);
                $values[$v[1]][$v[0]] = $val;
            }

            foreach ($values as $key => $val)
            {
                $this->scoring_model->reconcile_scoring_def_year(False,$key,array('points' => $val['points'],
                                                                                        'per' => $val['per'],
                                                                                        'round' => $val['round']));
            }
            redirect(site_url('admin/scoring'));
        }

        $this->load->helper('form');
        $values = $this->scoring_model->get_values_data();
        $this->admin_view('admin/scoring/edit.php', array('values' => $values));
    }

    function delete($value_id)
    {
        //SECURITY: league
        $this->scoring_model->delete($value_id);
        redirect(site_url('admin/scoring'));
    }
}
