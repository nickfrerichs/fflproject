<?php

class Positions extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();

        $this->load->model('admin/security_model');
        $this->load->model('admin/positions_model');
        $this->bc[$this->league_name] = "";
        $this->bc["Positions"] = "";
    }

    function index()
    {
        $league_positions = $this->positions_model->get_league_positions_data();
        $nfl_positions = $this->positions_model->get_nfl_positions_array();
        $this->admin_view('admin/positions/positions', array('league_positions' => $league_positions,
                                                    'nfl_positions' => $nfl_positions));
    }


    function ajax_load_position_form($editid=null)
    {
        if ($editid)
        {
            $position = $this->positions_model->get_league_position_data($editid);
        }
        $roster_max = $this->positions_model->get_league_roster_max();

        $nfl_positions = $this->positions_model->get_nfl_positions_array();
        ?>
        <tr><td>Short Name</td><td><input id="short-text" type="text" value="<?php if($editid){echo $position->text_id;}?>"></td></tr>
        <tr><td>Long Name</td><td><input id="long-text" type="text" value="<?php if($editid){echo $position->long_text;}?>"></td></tr>
        <tr><td>Roster Max</td><td>
            <select id="roster-max">
                <?php for($i=0; $i<=$roster_max; $i++): ?>
                    <?php if ($editid && $position->max_roster == $i): ?>
                        <option selected><?=$i?></option>
                    <?php else:?>
                        <option><?=$i?></option>
                    <?php endif;?>
                <?php endfor; ?>
            </select>
        </td></tr>
        <tr><td>Roster Min</td><td>
            <select id="roster-min">
                <?php for($i=0; $i<=$roster_max; $i++): ?>
                    <?php if ($editid && $position->min_roster == $i): ?>
                        <option selected><?=$i?></option>
                    <?php else: ?>
                        <option><?=$i?></option>
                    <?php endif;?>
                <?php endfor; ?>
            </select>
        </td></tr>
        <tr><td>Start Max</td><td>
            <select id="start-max">
                <?php for($i=0; $i<=$roster_max; $i++): ?>
                    <?php if ($editid && $position->max_start == $i): ?>
                        <option selected><?=$i?></option>
                    <?php else: ?>
                        <option><?=$i?></option>
                    <?php endif;?>
                <?php endfor; ?>
            </select>
        </td></tr>
        <tr><td>Start Min</td><td>
            <select id="start-min">
                <?php for($i=0; $i<=$roster_max; $i++): ?>
                    <?php if ($editid && $position->min_start == $i): ?>
                        <option selected><?=$i?></option>
                    <?php else: ?>
                        <option><?=$i?></option>
                    <?php endif;?>
                <?php endfor; ?>
            </select>
        </td></tr>
        <tr><td>
                <select id="nfl-positions" name="nfl-positions" multiple="multiple">
                <?php foreach($nfl_positions as $id => $p): ?>
                    <?php if(!$editid || !in_array($id,explode(',',$position->nfl_position_id_list))):?>
                        <option value="<?=$id?>"><?=$p?></option>
                    <?php endif;?>
                <?php endforeach;?>
                </select>
                <div class="text-center">
                    <button class="button" onclick="MoveItem('league-positions', 'nfl-positions');"><<</button>
                </div>
            </td>
            <td>
                <select id="league-positions" name="nfl-positions" multiple="multiple">
                    <?php if($editid):?>
                        <?php foreach(explode(',',$position->nfl_position_id_list) as $p): ?>
                            <option value="<?=$p?>"><?=$nfl_positions[$p]?></option>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
                <div class="text-center">
                    <button class="button" onclick="MoveItem('nfl-positions', 'league-positions');">>></button>
                </div>
            </td>

        <?php

    }

    function add()
    {
        $this->load->helper('form');
        $nfl_positions = $this->positions_model->get_nfl_positions_data(true);
        $this->admin_view('admin/positions/positions_add', array('nfl_positions' => $nfl_positions));
    }

    function edit($var)
    {
        if ($this->security_model->is_position_in_league($var))
        {
            $this->load->helper('form');
            $nfl_positions = $this->positions_model->get_nfl_positions_data(true);
            $position = $this->positions_model->get_league_position_data($var);
            $this->admin_view('admin/positions/positions_add', array('nfl_positions'=> $nfl_positions,
                                                            'pos' => $position,
                                                            'edit' => true));
        }
    }

    function save($posid = null)
    {

        $pos_year = $this->positions_model->reconcile_current_positions_year();

        $posid = $this->input->post('posid');
        $edit = false;
        if($posid != "" && $this->positions_model->position_exists($this->input->post('text_id')))
            $edit = true;

        $values = array('text_id' => $this->input->post('text_id'),
            'long_text' => $this->input->post('long_text'),
            'league_positions' => implode($this->input->post('league_positions'),','),
            'max_roster' => $this->input->post('max_roster'),
            'min_roster' => $this->input->post('min_roster'),
            'max_start' => $this->input->post('max_start'),
            'min_start' => $this->input->post('min_start'),
            'year' => $pos_year);
        if($edit)
            $values['id'] = $posid;
        $this->positions_model->save_position($values);

    }

    function delete($var)
    {
        if ($this->security_model->is_position_in_league($var))
        {
            $this->positions_model->reconcile_current_positions_year($var);
            redirect('admin/positions');
        }
    }
}
