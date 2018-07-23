
<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-lg-container">
            <div class="is-size-5">Game Types</div>

            <?php if(count($types) > 0): ?>
                <em>Game types should never be deleted after they are in use, they are used for League History.</em>
            <?php endif;?>
            <table class="table is-fullwidth is-narrow fflp-table-fixed">
                <th>Game Type</th><th>default</th><th>Title Game</th><th></th>
            <?php foreach($types as $type): ?>
                <tr>
                    <td>
                    <?php $this->load->view('components/editable_text',array('id' => "type".$type->id,
                                                                            'var1' => $type->id, 
                                                                            'value' => $type->text_id,
                                                                            'url' => site_url('admin/schedule_templates/ajax_gametype_name_edit')));?>

                    </td>
                    <td>
                        <?php if ($type->default):?>
                            default
                        <?php else: ?>
                            <a href='<?=site_url('admin/schedule_templates/gametypes/default/'.$type->id)?>'>set</a>
                        <?php endif;?>
                    </td>

                    <td>
                        <?php if($type->title_game){echo "Yes";}else{echo "No";}?>
                    </td>

                    <td><a href='<?=site_url('admin/schedule_templates/gametypes/delete/'.$type->id)?>'>delete</a></td>
                </tr>
            <?php endforeach; ?>
            </table>

                <div class="is-size-6">New game type</div>

                <table class="table is-fullwidth is-narrow fflp-table-fixed">
                    <tr>
                        <td><input data-post-id="text_id" class="input post-add-type" type="text"></td>
                        <td>
                            <label class="checkbox">
                                <input data-post-id="title_game" class="post-add-type" type="checkbox" unchecked>
                                Title Game
                            </label>
                        </td>
                        <td>
                            <button class="button is-small is-link ajax-submit-button"
                                    data-url="<?=site_url('admin/schedule_templates/ajax_add_gametype')?>"
                                    data-varclass="post-add-type"
                            >Add Type</button>
                        </td>
                    </tr>
                </table>

        </div>
    </div>
</div>
