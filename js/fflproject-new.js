

// HTML Pagination

function loadContent(targetSel)
{

	// Set an ajax wait key to prevent flashing of objects on initial page load.
	var aw_key = 'loadcontent_'+targetSel;
    ajax_waits[aw_key] = true;

    var per_page = $('#'+targetSel).data('per-page');
    //url
    //filters (table colums)
    //order
    //searchtext
    //per_page
    //page / count
    if ($('#'+targetSel).data('limit') == undefined){$('#'+targetSel).data('limit',per_page);}
    var limit = $('#'+targetSel).data('limit');
    var filters = {};
    $('.pagination-filter[data-for="'+targetSel+'"]').each(function(){
        filters[$(this).data('filter')] = $(this).val();
    });

    // $.post(url, {'page':page, 'pos':pos, 'by':by, 'order':order, 'search' : search, 'per_page': per_page,
    // 'year':year, 'starter':starter, 'custom':custom, 'var1':var1}, function(data){
    var url = $('#'+targetSel).data('url');

    $.post(url, {'limit':limit}, function(data){
        console.log(data);
        if(data.success)
        {
            debug_out(data);
            $("#"+targetSel).html(data.html);

            $('.lc-remaining[data-for="'+targetSel+'"]').text(data.total-data.count);
            $('.lc-count[data-for="'+targetSel+'"]').text(data.count);
            $('.lc-total[data-for="'+targetSel+'"]').text(data.total);

            $("#"+targetSel).data('total',data.total);
            $("#"+targetSel).data('num-displayed',data.count);

        }
    },'json');

    // Clear the ajax wait
    ajax_waits[aw_key] = false;
}

// 
$(document).on('click','.lc-load-all-button', function(e){
    //e.preventDefault();
    var target = $(this).data('for');
    $('#'+target).data('limit',10000);
    loadContent(target);

    console.log('.lc-load-all-button[data-for="'+target+'"]');
});

$(document).on('click','.lc-load-more-button',function(e){
    var target = $(this).data('for');
    var limit = $('#'+target).data('limit');
    var per_page = $('#'+target).data('per-page');
    $('#'+target).data('limit',limit+per_page);
    loadContent(target);

    console.log('.lc-load-all-button[data-for="'+target+'"]');
});

$(document).on('click','.lc-reset-button',function(e){
    var target = $(this).data('for');
    var per_page = $('#'+target).data('per-page');
    $('#'+target).data('limit',per_page);

    loadContent(target);
});


// Make Bulma tabs work with jquery
$(document).on('click','.fflp-tabs-active > ul > li',function(){
    $(this).siblings().each(function(){
        $(this).removeClass('is-active');
        var target = $(this).data('for');
        $('#'+target).addClass('is-hidden');
    });
    $(this).addClass('is-active');
    var target = $(this).data('for');
    $('#'+target).removeClass('is-hidden');
    var load_content = $(this).data('load-content');
    if(load_content != undefined && $('#'+load_content).text().trim()=='')
    {loadContent($(this).data('load-content'));}
});


function debug_out(o,o2)
{
	if (window.DEBUG_ENABLED){
		if (o != undefined){console.log(o);}
		if (o2 != undefined){console.log(o2);}
	}
}
