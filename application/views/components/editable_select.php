<?php 
// Inputs: $id, $value, $blank_value, $url, $options, $selected_val

?>
<div class="field has-addons">
    <div class="control is-expanded">
        <div class="select is-fullwidth">
            <select id="<?=$id?>" class="editable-select" disabled data-url="<?=$url?>">
            <?php foreach($options as $text => $val): ?>
                <option value="<?=$val?>" <?php if($val == $selected_val){echo "selected";}?>>
                    <?=$text?>
                </option>
            <?php endforeach;?>
            </select>
        </div>
    </div>
    <div class="control">
        <a id="<?=$id?>-edit-button" class="button is-link editable-select-edit-button">edit</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-cancel-button" class="button is-danger editable-select-cancel-button is-hidden">cancel</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-save-button" class="button is-success editable-select-save-button is-hidden">save</a>
    </div>
</div>

<!-- <div class="field has-addons" style="max-width:500px;">
    <div class="control is-expanded">
        <input class="input editable-text-input is-fullwidth"

        </input>
    </div>
    <div class="control">
        <a id="<?=$id?>-edit-button" class="button is-info editable-text-edit-button">edit</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-cancel-button" class="button is-danger editable-text-cancel-button is-hidden">cancel</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-save-button" class="button is-success editable-text-save-button is-hidden">save</a>
    </div>
</div> -->