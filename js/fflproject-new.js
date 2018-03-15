
// =====================================
// Start Pagination/endless list functions
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
    
    console.log(url);
    console.log(limit);

    $.post(url, {'limit':limit}, function(data){
        console.log(data);
        console.log("Post succeeded.");
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
    },'json').fail(function(){
        console.log("failed");
    });
    console.log(targetSel);
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
// End Pagination/endless list functions
// =====================================



// =====================================
// Start form components to post data back

// Editable text field with save/edit/cancel buttons, posts to url
$(document).on('click','.editable-text-edit-button', function(e){
    var id = '#'+$(this).prop('id').replace('-edit-button','');
    var input = id+'-input';
    $(input).prop('disabled',false);
    $(input).data('initial-value',$(input).val());
    $(id+'-save-button').removeClass('is-hidden');
    $(id+'-cancel-button').removeClass('is-hidden');
    $(id+'-edit-button').addClass('is-hidden');
});

$(document).on('click','.editable-text-cancel-button', function(e){
    var id = '#'+$(this).prop('id').replace('-cancel-button','');
    var input = id+'-input';
    $(input).prop('disabled',true);
    $(input).val($(input).data('initial-value'));
    $(id+'-save-button').addClass('is-hidden');
    $(id+'-cancel-button').addClass('is-hidden');
    $(id+'-edit-button').removeClass('is-hidden');
});

$(document).on('click','.editable-text-save-button', function(e){
    var id = '#'+$(this).prop('id').replace('-save-button','');
    var input = id+'-input';
    var value = $(input).val();
    var var1 = false, var2 = false, var3 = false
    var1 = $(input).data('var1');
    var2 = $(input).data('var2');
    var3 = $(input).data('var3');
    var url = $(input).data('url');

    $.post(url,{'id':id,'value':value,'var1':var1,'var2':var2,'var3':var3},function(data){
        var d = $.parseJSON(data);
        console.log(d);
		if(d.success) {$(input).val(d.value);}
        else {$(input).val($(input).data('initial-value'));}
    });

    $(input).prop('disabled',true);
    $(id+'-save-button').addClass('is-hidden');
    $(id+'-cancel-button').addClass('is-hidden');
    $(id+'-edit-button').removeClass('is-hidden');
});


// Editable select field with save/edit/cancel buttons, posts to url
$(document).on('click','.editable-select-edit-button', function(e){
    var id = '#'+$(this).prop('id').replace('-edit-button','');
    var select = id;
    $(select).prop('disabled',false);
    $(select).data('initial-value',$(select).val());
    $(id+'-save-button').removeClass('is-hidden');
    $(id+'-cancel-button').removeClass('is-hidden');
    $(id+'-edit-button').addClass('is-hidden');
});

$(document).on('click','.editable-select-cancel-button', function(e){
    var id = '#'+$(this).prop('id').replace('-cancel-button','');
    var select = id;
    $(select).prop('disabled',true);
    $(select).val($(select).data('initial-value'));
    $(id+'-save-button').addClass('is-hidden');
    $(id+'-cancel-button').addClass('is-hidden');
    $(id+'-edit-button').removeClass('is-hidden');
});

$(document).on('click','.editable-select-save-button', function(e){
    var id = '#'+$(this).prop('id').replace('-save-button','');
    var select = id;
    var value = $(select).val();
    var var1 = false, var2 = false, var3 = false
    var1 = $(select).data('var1');
    var2 = $(select).data('var2');
    var3 = $(select).data('var3');
    var url = $(select).data('url');

    $.post(url,{'id':id,'value':value,'var1':var1,'var2':var2,'var3':var3},function(data){
        console.log(data);
        var d = $.parseJSON(data);
        console.log(d);
		if(d.success) {$(select).val(d.value);}
        else {$(select).val($(select).data('initial-value'));}
    });

    $(select).prop('disabled',true);
    $(id+'-save-button').addClass('is-hidden');
    $(id+'-cancel-button').addClass('is-hidden');
    $(id+'-edit-button').removeClass('is-hidden');
});


// Handles a toggle-control that is clicked, post to the url
$(document).on('click','.toggle-control',function(e){
	var element = $(this);
	var url = $(this).data('url');
	var var1 = $(this).data('var1');
	var var2 = $(this).data('var2');
	$.post(url,{"var1":var1,"var2":var2},function(data){
		var d = $.parseJSON(data);
		if(d.success)
		{
			if ((d.value == 1 && element.is(':checked')) || (d.value == 0 && !element.is(':checked')))
			{return}
			location.reload();
		}
    });
    
});

// End Start form components to post data back
// =====================================



// =====================================
// Things to make Bulma work

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

// Make Bulma modal close buttons work
$(document).on('click','.modal-close, .modal-close-button, .modal-background', function(){
    $(this).closest($('.modal')).removeClass('is-active');
    if ($(this).closest($('.modal')).data('reloadclose') == "1"){location.reload();}
});

// End things to make Bulma work
// =====================================



function debug_out(o,o2)
{
	if (window.DEBUG_ENABLED){
		if (o != undefined){console.log(o);}
		if (o2 != undefined){console.log(o2);}
	}
}