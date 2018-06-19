<?php foreach($roster as $r): ?>
    <tr>
        <td><?=$r->nfl_pos_text_id?></td>
        <td><?=$r->club_id?></td>
        <td><?=$r->first_name.' '.$r->last_name?></td>
        <td>
                <?php $this->load->view('components/toggle_switch',
                                                array('id' => "keeper-<?=$r->player_id?>",
													  'url' => site_url('myteam/roster/toggle_keeper'),
													  'var1' => $r->player_id,
                                                      'is_checked' => $r->keeper));
                        		?>
        </td>

    </tr>
<?php endforeach;?>
