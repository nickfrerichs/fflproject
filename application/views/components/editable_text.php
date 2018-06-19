<?php 
// Inputs: $id, $value, $blank_value, $url
$set_val = "";
if (!isset($value) || $value == "")
    $set_val = "";
else
    $set_val = $value;
?>

<div class="field has-addons">
    <div class="control is-expanded">
        <input class="input editable-text-input is-fullwidth"
               type="text"
               id="<?=$id?>-input"
               value="<?=$set_val?>"
               data-url="<?=$url?>"
               <?php if(isset($var1)){echo 'data-var1='.$var1;}?>
               disabled>
        </input>
    </div>
    <div class="control">
        <a id="<?=$id?>-edit-button" class="button is-link editable-text-edit-button">edit</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-cancel-button" class="button is-danger editable-text-cancel-button is-hidden">cancel</a>
    </div>
    <div class="control">
        <a id="<?=$id?>-save-button" class="button is-success editable-text-save-button is-hidden">save</a>
    </div>
</div>
