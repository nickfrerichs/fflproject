<?php
// id, title, body, size

?>



<div class="modal" id="<?=$id?>" <?php if ($reload_on_close){echo 'data-reloadclose="1"';}?>>
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title"><?=$title?></p>
            <button class="delete modal-close-button" aria-label="close"></button>
        </header>
        <section class="modal-card-body has-text-centered">
            <?=$body?>
        </section>
        <footer class="modal-card-foot">
        <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
        </footer>
    </div>
</div>