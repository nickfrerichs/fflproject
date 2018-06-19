<?php 
// Inputs: $id, $var1, $var2, $url, $is_checked, $color
if (!isset($color))
    $color = "is-link";
?>
<div class="field">
    <input  class="switch toggle-control <?=$color?>" <?php if(isset($var1)){echo 'data-var1="'.$var1.'"';} ?>
                                                  <?php if(isset($var2)){echo 'data-var2="'.$var2.'"';} ?>
                                                  data-url="<?=$url?>"
        id="<?=$id?>" type="checkbox" <?php if($is_checked){echo "checked";}?>>
    <label for="<?=$id?>">
    </label>

</div>