<div id="player-news-popup-modal" class="reveal large" data-reveal data-overlay="true" data-multiple-opened="true">
    <div id="player-news-popup-html">
    </div>
    <button class="close-button" data-close aria-label="Close reveal" type="button">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<script>

$(document).on('click','.player-news-popup',function(e){
    e.preventDefault();
    type = $(this).data('type');
    id = $(this).data('id');
    week = $(this).data('week');
    var p = $(this).position();
    var url = "<?=site_url('quickstats')?>/player_news";
    // console.log(url);
    $.post(url,{'id' : id},function(data)
    {
        $("#player-news-popup-html").html(data);
        $("#player-news-popup-modal").foundation('open');
    });

});

// function showStatsPopup(id, type)
// {
//     //var p = $(this).position();
//     var url = "<?=site_url('quickstats')?>"+"/"+type;
//     // console.log(url);
//     $.post(url,{'type' : type, 'id' : id},function(data)
//     {
//         $("#player-news-popup-html").html(data);
//         $("#player-news-popup-modal").foundation('open');
//     });
// }
</script>
