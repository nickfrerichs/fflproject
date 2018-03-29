<?php

class Positions extends MY_Admin_Controller{

    function __construct()
    {
        parent::__construct();

        $this->load->model('admin/admin_security_model');
        $this->load->model('admin/positions_model');
        $this->bc["League Admin"] = "";
        $this->bc["Positions"] = "";
    }

    function index()
    {
        $this->year($this->session->userdata('current_year'));
        // $data = array();
        // $league_positions = $this->positions_model->get_league_positions_data();
        // $nfl_positions = $this->positions_model->get_nfl_positions_array();
        // $data['years'] = $this->common_model->get_league_years();
        // $data['league_positions'] = $league_positions;
        // $data['nfl_positions'] = $nfl_positions;
        // $this->admin_view('admin/positions/positions', $data);
    }

    function year($selected_year)
    {
        $data = array();
        $league_positions = $this->positions_model->get_league_positions_data($selected_year);
        $nfl_positions = $this->positions_model->get_nfl_positions_array();
        $data['years'] = $this->common_model->get_league_years();
        $data['league_positions'] = $league_positions;
        $data['nfl_positions'] = $nfl_positions;
        $data['selected_year'] = $selected_year;
        $data['def_range'] = $this->common_model->league_position_range($selected_year);
        $this->admin_view('admin/positions/positions', $data);
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
        <tr><td>Short Name</td><td><input id="short-text" class="input" type="text" value="<?php if($editid){echo $position->text_id;}?>"></td></tr>
        <tr><td>Long Name</td><td><input id="long-text" class="input" type="text" value="<?php if($editid){echo $position->long_text;}?>"></td></tr>
        <tr><td>Roster Max</td><td>
            <?php if($roster_max == -1){$roster_max=20;}?>
            <div class="select">
                <select id="roster-max">
                    <?php if($position->max_roster == -1): ?>
                        <option selected value="-1">No max</option>
                    <?php else: ?>
                        <option value="-1">No max</option>
                    <?php endif; ?>
                    <?php for($i=0; $i<=$roster_max; $i++): ?>
                        <?php if ($editid && $position->max_roster == $i): ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                        <?php else:?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endif;?>
                    <?php endfor; ?>
                </select>
            </div>
        </td></tr>
        <tr><td>Roster Min</td><td>
            <div class="select">
                <select id="roster-min">
                    <?php if($position->min_roster == -1): ?>
                        <option selected value="-1">No min</option>
                    <?php else: ?>
                        <option value="-1">No min</option>
                    <?php endif; ?>
                    <?php for($i=0; $i<=$roster_max; $i++): ?>
                        <?php if ($editid && $position->min_roster == $i): ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                        <?php else: ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endif;?>
                    <?php endfor; ?>
                </select>
            </div>
        </td></tr>
        <tr><td>Start Max</td><td>
            <div class="select">
                <select id="start-max">
                    <?php if($position->max_start == -1): ?>
                        <option selected value="-1">No max</option>
                    <?php else: ?>
                        <option value="-1">No max</option>
                    <?php endif; ?>
                    <?php for($i=0; $i<=$roster_max; $i++): ?>
                        <?php if ($editid && $position->max_start == $i): ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                        <?php else: ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endif;?>
                    <?php endfor; ?>
                </select>
            </div>
        </td></tr>
        <tr><td>Start Min</td><td>
            <div class="select">
                <select id="start-min">
                    <?php if($position->min_start == -1): ?>
                        <option selected value="-1">No min</option>
                    <?php else: ?>
                        <option value="-1">No min</option>
                    <?php endif; ?>
                    <?php for($i=0; $i<=$roster_max; $i++): ?>
                        <?php if ($editid && $position->min_start == $i): ?>
                            <option selected value="<?=$i?>"><?=$i?></option>
                        <?php else: ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endif;?>
                    <?php endfor; ?>
                </select>
            </div>
        </td></tr>
        <tr><td>
                <div class="select is-multiple">
                    <select id="nfl-positions" name="nfl-positions" multiple size="5" style="min-width:75px;">
                    <?php foreach($nfl_positions as $id => $p): ?>
                        <?php if(!$editid || !in_array($id,explode(',',$position->nfl_position_id_list))):?>
                            <option value="<?=$id?>"><?=$p?></option>
                        <?php endif;?>
                    <?php endforeach;?>
                    </select>
                </div>
                <br>
                <button class="button is-link" onclick="MoveItem('nfl-positions', 'league-positions');">>></button>

            </td>
            <td>
                <div class="select is-multiple">
                    <select id="league-positions" name="nfl-positions" multiple size="5" style="min-width:75px;">
                        <?php if($editid):?>
                            <?php foreach(explode(',',$position->nfl_position_id_list) as $p): ?>
                                <option value="<?=$p?>"><?=$nfl_positions[$p]?></option>
                            <?php endforeach;?>
                        <?php endif;?>
                    </select>
                </div>
                <br>
                <button class="button is-link" onclick="MoveItem('league-positions', 'nfl-positions');"><<</button>
                    

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
        if ($this->admin_security_model->is_position_in_league($var))
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

        //$pos_year = $this->positions_model->reconcile_current_positions_year();

        $posid = $this->input->post('posid');
        $edit = false;
        if($posid != "" && $this->positions_model->position_exists($posid))
            $edit = true;

        $values = array('text_id' => $this->input->post('text_id'),
            'long_text' => $this->input->post('long_text'),
            'league_positions' => implode($this->input->post('league_positions'),','),
            'max_roster' => $this->input->post('max_roster'),
            'min_roster' => $this->input->post('min_roster'),
            'max_start' => $this->input->post('max_start'),
            'min_start' => $this->input->post('min_start'),
            'year' => $this->input->post('year'));
        if($edit)
            $values['id'] = $posid;
        $this->positions_model->reconcile_current_positions_year(False,$values,$values['year']);
        //$this->positions_model->save_position($values);

    }

    function delete($year,$var)
    {
        if ($this->admin_security_model->is_position_in_league($var))
        {
            $this->positions_model->reconcile_current_positions_year($var,False,$year);
            redirect('admin/positions/year/'.$year);
        }
    }

    function test()
    {
        echo $this->positions_model->reconcile_current_positions_year();
    }
}
?>
