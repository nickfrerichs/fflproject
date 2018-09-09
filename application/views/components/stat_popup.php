<?php
// id, title, body, size

?>



<div class="modal fflp-overflow" id="stat-popup-modal">
    <div class="modal-background"></div>
    <div class="modal-card" style="width: 800px;">
        <header class="modal-card-head" style="height: 10px;">
            <p class="modal-card-title">Quick Stats</p>
            <button class="delete modal-close-button" aria-label="close"></button>
        </header>
        <section class="modal-card-body has-text-centered">
            <div id="stat-popup-html">
            </div>
        </section>
        <!-- <footer class="modal-card-foot">
        <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
        </footer> -->
    </div>
</div>



<script>



// $(document).on('click','.stat-popup',function(e){
//
//
//     e.preventDefault();
//     type = $(this).data('type');
//     id = $(this).data('id');
//
//     console.log('stat pop up');
//      var p = $(this).position();
//     // $("#stat-popup-modal").parent().css({position: 'relative'});
//     // console.log('left: '+p.left+" right: "+p.right);
//     //
//     // $("#stat-popup-modal").css({top: p.top, left: p.left+175, position:'absolute'});
//     // $("#stat-popup-modal").removeClass('hidden');
//
//     var url = "<?=site_url('quickstats')?>"+"/"+type;
//     // console.log(url);
//     $.post(url,{'type' : type, 'id' : id},function(data)
//     {
//         var w = "auto";
//         var h = "auto";
//         if ($( window ).width() < 700){w = 500;}
//         if ($( window ).width() < 400){w = 300; h=500;}
//         new jBox('Modal', {
//             content: data,
//             maxWidth: w,
//             maxHeight: h,
//             blockScroll: false,
//             fade: false,
//             overlay: false,
//             draggable: true,
//             animation: false,
//             closeOnEsc: true
//         }).open();
//         //  $("#stat-popup-html").html(data);
//         //  $("#stat-popup-modal").jBox('Tooltip');
//     });
//
// });

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
