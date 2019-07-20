<?php
// id, title, body, size

?>



<div class="modal f-scrollbar" id="stat-popup-modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="height: 10px;">
            <p class="modal-card-title">Player Stats</p>
            <button class="delete modal-close-button" aria-label="close"></button>
        </header>
        <section class="modal-card-body has-text-centered">
            <div id="stat-popup-html">
            </div>
        </section>
        <footer class="modal-card-foot">
        <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
        </footer>
    </div>
</div>



<script>

$(document).on('click','.stat-popup',function(e){
    e.preventDefault();
    type = $(this).data('type');
    id = $(this).data('id');
    week = $(this).data('week');
    var p = $(this).position();
    var url = "<?=site_url('quickstats')?>"+"/"+type;
    // console.log(url);
    $.post(url,{'type' : type, 'id' : id, 'week' : week},function(data)
    {
        $("#stat-popup-html").html(data);
        $("#stat-popup-modal").addClass('is-active');
    });

});

function showStatsPopup(id, type)
{
    //var p = $(this).position();
    var url = "<?=site_url('quickstats')?>"+"/"+type;
    // console.log(url);
    $.post(url,{'type' : type, 'id' : id},function(data)
    {
        $("#stat-popup-html").html(data);
        $("#stat-popup-modal").addClass('is-active');
    });
}
</script>
