<?php

class Scoring extends MY_Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/scoring_model');
        $this->bc["League Admin"] = "";
        $this->bc["Scoring"] = "";
    }

    function index()
    {
        $this->year();
    }

    function year($selected_year = 0)
    {
        if($selected_year == 0)
            $selected_year = $this->current_year;

        $data = array();
        $categories = $this->scoring_model->get_scoring_cats_data($selected_year);
        $scoring_defs_data = $this->scoring_model->get_values_data($selected_year);

        $scoring_defs = array();
        foreach ($scoring_defs_data as $s)
        {
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['per'] = $s->per;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['points'] = $s->points;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['round'] = $s->round;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['range_end'] = $s->range_end;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['range_start'] = $s->range_start;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['is_range'] = $s->is_range;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['long_text'] = $s->long_text;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['pos_text'] = $s->pos_text;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['cat_id'] = $s->nfl_scoring_cat_id;
            $scoring_defs[$s->type_text][$s->pos_text][$s->id]['pos_id'] = $s->pos_id;
        }
        $data['def_range'] = $this->common_model->scoring_def_range($selected_year);
        $data['selected_year'] = $selected_year;
        $data['categories'] = $categories;
        $data['scoring_defs'] = $scoring_defs;
        $data['years'] = $this->common_model->get_league_years();

        $this->bc['Scoring'] = site_url('admin/scoring');
        $this->bc[$selected_year] = '';


        $this->admin_view('admin/scoring/scoring.php', $data);
    }

    function add($year, $position_id = 0)
    {
        $data = array();
        $data['selected_year'] = $year;
        // SECURITY: league
        $stat_id = $this->input->post('stat_id');

        // This will be "Per unit" or "Unit range"
        $type = $this->input->post('type');

        // if ($stat_id)
        // {
        //     if ($type == "Unit range")
        //     {
        //         $this->scoring_model->add_stat_value_entry($stat_id, $position_id, true, $year);
        //     }
        //     elseif (!$this->scoring_model->stat_value_exists($position_id,$stat_id))
        //     {
        //         $this->scoring_model->add_stat_value_entry($stat_id, $position_id, false, $year);
        //     }
        // }

        $this->load->helper('form');
        $categories = $this->scoring_model->get_scoring_cats_data($position_id);

        $nfl_positions = $this->scoring_model->get_nfl_positions_data();

        $this->bc['Scoring'] = site_url('admin/scoring');
        $this->bc[$year] = site_url('admin/scoring/year/'.$year);
        $this->bc['Add Definitions'] = "";

        $data['cats'] = $categories;
        $data['nfl_positions'] = $nfl_positions;
        $data['selected_pos'] = $position_id;
        $this->admin_view('admin/scoring/add.php', $data);

    }

    function ajax_add_scoring_def()
    {
        $response = array('success' => false);

        $cat_id = $this->input->post('cat_id');
        $is_range = $this->input->post('is_range');
        $year = $this->input->post('year');
        $position_id = $this->input->post('pos_id');

        if ($is_range == "1")
            $this->scoring_model->add_stat_value_entry($cat_id, $position_id, true, $year);
        elseif(!$this->scoring_model->stat_value_exists($position_id,$cat_id))
            $this->scoring_model->add_stat_value_entry($cat_id, $position_id, false, $year);

        $response['success'] = True;

        echo json_encode($response);
    }

    function edit($year=0)
    {
        $data = array();
        if ($year == 0)
            $year = $this->current_year;
        $data['selected_year'] = $year;
        // if ($this->input->post('save'))
        // {
        //     $values = array();
        //     foreach ($this->input->post() as $key => $val)
        //     {
        //         if (stripos($key,'_') === false)
        //             continue;
        //         $v = explode('_',$key);
        //         $values[$v[1]][$v[0]] = $val;
        //     }

        //     foreach ($values as $key => $val)
        //     {
        //         if (!array_key_exists('per',$val))
        //             $val['per'] = 0;
        //         if (!array_key_exists('start',$val))
        //             $val['start'] = 0;
        //         if (!array_key_exists('end',$val))
        //             $val['end'] = 0;

        //         $this->scoring_model->reconcile_scoring_def_year(False,$key,array('points' => $val['points'],
        //                                                                                 'per' => $val['per'],
        //                                                                                 'range_start' => $val['start'],
        //                                                                                 'range_end' => $val['end'],
        //                                                                                 'round' => $val['round']),
        //                                                                                 $year);
        //     }
        //     redirect(site_url('admin/scoring'));
        // }

        $this->load->helper('form');
        $values = $this->scoring_model->get_values_data($year);

        $this->bc['Scoring'] = site_url('admin/scoring');
        $this->bc[$year] = site_url('admin/scoring/year/'.$year);
        $this->bc['Edit Values'] = "";

        $data['values'] = $values;

        $this->admin_view('admin/scoring/edit.php', $data);
    }

    function ajax_save_scoring_defs()
    {
        $response = array('success' => false);

        $values = $this->input->post('values');
        $year = $this->input->post('year');
        foreach($values as $id => $val)
        {
            if (!array_key_exists('per',$val))
                $val['per'] = 0;
            if (!array_key_exists('start',$val))
                $val['start'] = 0;
            if (!array_key_exists('end',$val))
                $val['end'] = 0;
            $this->scoring_model->reconcile_scoring_def_year(False,$id,array('points' => $val['points'],
                    'per' => $val['per'],
                    'range_start' => $val['start'],
                    'range_end' => $val['end'],
                    'round' => $val['round']),
                    $year);
        }

        $response['success'] = True;

        echo json_encode($response);
    }

    function delete($year=0, $value_id)
    {
        if ($year == 0)
            $year = $this->current_year;
        //SECURITY: league
        $this->scoring_model->reconcile_scoring_def_year($value_id,False,False,$year);
        redirect(site_url('admin/scoring/year/'.$year));
    }
}
?>
