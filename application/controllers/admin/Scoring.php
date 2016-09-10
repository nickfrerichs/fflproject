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
        $categories = $this->scoring_model->get_scoring_cats_data();
        $scoring_defs_data = $this->scoring_model->get_values_data();

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


        $this->admin_view('admin/scoring/scoring.php', array('categories' => $categories,
                                                            'scoring_defs' => $scoring_defs));
    }

    function add($position_id = 0)
    {
        // SECURITY: league
        $stat_id = $this->input->post('stat_id');

        // This will be "Per unit" or "Unit range"
        $type = $this->input->post('type');

        if ($stat_id)
        {
            if ($type == "Unit range")
            {
                $this->scoring_model->add_stat_value_entry($stat_id, $position_id, true);
            }
            elseif (!$this->scoring_model->stat_value_exists($position_id,$stat_id))
            {
                $this->scoring_model->add_stat_value_entry($stat_id, $position_id, false);
            }
        }

        $this->load->helper('form');
        $categories = $this->scoring_model->get_scoring_cats_data($position_id);

        $nfl_positions = $this->scoring_model->get_nfl_positions_data();

        $this->bc['Scoring'] = site_url('admin/scoring');
        $this->bc['Add Definitions'] = "";

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
                if (!array_key_exists('per',$val))
                    $val['per'] = 0;
                if (!array_key_exists('start',$val))
                    $val['start'] = 0;
                if (!array_key_exists('end',$val))
                    $val['end'] = 0;

                $this->scoring_model->reconcile_scoring_def_year(False,$key,array('points' => $val['points'],
                                                                                        'per' => $val['per'],
                                                                                        'range_start' => $val['start'],
                                                                                        'range_end' => $val['end'],
                                                                                        'round' => $val['round']));
            }
            redirect(site_url('admin/scoring'));
        }

        $this->load->helper('form');
        $values = $this->scoring_model->get_values_data();

        $this->bc['Scoring'] = site_url('admin/scoring');
        $this->bc['Edit Values'] = "";

        $this->admin_view('admin/scoring/edit.php', array('values' => $values));
    }

    function delete($value_id)
    {
        //SECURITY: league
        $this->scoring_model->delete($value_id);
        redirect(site_url('admin/scoring'));
    }
}
?>
