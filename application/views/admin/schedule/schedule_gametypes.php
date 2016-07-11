
<div class="row">
    <div class="columns">
        <h5>Game Types</h5>
    </div>
</div>

<div class="row">
    <div class="columns">
        <?php if(count($types) > 0): ?>
            <em>Game types should never be deleted after they are in use, they are used for League History.</em>
        <?php endif;?>
        <table>
            <th class="text-left" style="width:150px;">Game Type</th><th><th class="text-left">default</th><th class="text-left">
                Title Game</th>
                    <th></th>
        <?php foreach($types as $type): ?>
            <tr>
                <td id="type<?=$type->id?>-field"> <?=$type->text_id?></td>
                <td class="text-left">
                    <a href="#" id="type<?=$type->id?>-control" class="change-control" data-type="text" data-url="<?=site_url('admin/schedule_templates/ajax_gametype_name_edit')?>">Change</a>
                    <a href="#" id="type<?=$type->id?>-cancel" class="cancel-control"></a>
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
    </div>
</div>

<div class="row">
    <div class="columns">
            <h6>New game type</h6>
            <?=form_open(current_url())?>
            <table>
                <tr>
                    <td><?=form_input('text_id')?></td>
                    <td><input id="title_game" name="title_game" type="checkbox" unchecked value="1"><label for="title_game">
                        <span data-tooltip class="has-tip top" title="Used to determine season title (Ex: championship or sacko).  These will be tracked as titles in League History.  Should be only one per season.">
                            Title Game
                        </span></label></td>
                    <td><input class="button small" type="submit" name="add" value="Add type"  /></td>
                </tr>
            </table>
        <?=form_close()?>
    </div>
</div>
