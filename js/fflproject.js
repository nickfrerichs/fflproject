// =====================================
// Misc. used site wide

// For "notifications" at the top
$(document).on('click',"._notification-close",function(){
    debug_out('Message acked');
	var ackurl = $(this).data('ackurl');
    $.post(ackurl);
});

$(document).on('click', '.notification > button.delete', function() {
    $(this).parent().addClass('is-hidden');
    return false;
});

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
    var order = $('#'+targetSel).data('order');
    var by = $('#'+targetSel).data('by');
    var var1 = $('#'+targetSel).data('var1');

    var check_val = null;
    if ($('#'+targetSel+'-checkbox') != undefined){var check_val = $('#'+targetSel+'-checkbox').is(':checked');}

    // $.post(url, {'page':page, 'pos':pos, 'by':by, 'order':order, 'search' : search, 'per_page': per_page,
    // 'year':year, 'starter':starter, 'custom':custom, 'var1':var1}, function(data){
    var url = $('#'+targetSel).data('url');
    $.post(url, {'limit':limit, 'order':order, 'by':by, 'filters':filters, 'var1':var1, 'checkbox' : check_val}, function(data){
        
        if(data.success)
        {
            debug_out("Success");
            debug_out(data);
            $("#"+targetSel).html(data.html);

            $('.lc-remaining[data-for="'+targetSel+'"]').text(data.total-data.count);
            $('.lc-count[data-for="'+targetSel+'"]').text(Math.min(data.count,data.total));
            $('.lc-total[data-for="'+targetSel+'"]').text(data.total);

            $("#"+targetSel).data('total',data.total);
            $("#"+targetSel).data('num-displayed',data.count);
            // Clear the ajax wait
            ajax_waits[aw_key] = false;

        }
        else
        {
            debug_out("loadContent: success = false")
            debug_out(data);
        }
    },'json').fail(function(xhr, status, error){
        report_ajax_error(xhr, status, error);
        ajax_waits[aw_key] = false;
    });

}

function report_ajax_error(xhr, status, error)
{
    notice(status+', see console for details.','error');
    console.log(status);
    console.log(error);
}

// Filter changed events that reload the content
$(document).on('input','.pagination-filter',function(e){
	var playerSearchTimer;
	clearTimeout(playerSearchTimer);
	var delay = 500;
	var for_id = $(this).data('for');
	playerSearchTimer = setTimeout(function(){
		//resetPlayerSearch(tbody,0,0);
		loadContent(for_id);
	},delay);
});

$(document).on('change','.pagination-filter', function(e){
    var for_id = $(this).data('for');
    loadContent(for_id);
});

$(document).on('change','.player-list-checkbox',function(e){
    var for_id = $(this).data('for');
    loadContent(for_id);
});

$(document).on('click','.lc-sort', function(e){
    var for_id = $(this).data('for');
    var order = $('#'+for_id).data('order');
    var by = $(this).data('by');

    if ($('#'+for_id).data('by') == by)
    {
        if(order == 'asc'){order='desc';}
        else{order='asc';}
    }
    $('#'+for_id).data('by',by);
    $('#'+for_id).data('order',order);
    loadContent(for_id);
    e.preventDefault();
});

// 
$(document).on('click','.lc-load-all-button', function(e){
    //e.preventDefault();
    var target = $(this).data('for');
    $('#'+target).data('limit',10000);
    loadContent(target);
});

$(document).on('click','.lc-load-more-button',function(e){
    var target = $(this).data('for');
    var limit = $('#'+target).data('limit');
    var per_page = $('#'+target).data('per-page');
    $('#'+target).data('limit',limit+per_page);
    loadContent(target);
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
        console.log(data);
        var d = $.parseJSON(data);
        debug_out(d);
		if(d.success) {$(input).val(d.value);}
        else {$(input).val($(input).data('initial-value'));}
    }).fail(function(xhr, status, error){report_ajax_error(xhr, status, error);});

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
        var d = $.parseJSON(data);
		if(d.success) {$(select).val(d.value);}
        else {$(select).val($(select).data('initial-value'));}
    }).fail(function(xhr, status, error){report_ajax_error(xhr, status, error);});

    $(select).prop('disabled',true);
    $(id+'-save-button').addClass('is-hidden');
    $(id+'-cancel-button').addClass('is-hidden');
    $(id+'-edit-button').removeClass('is-hidden');
});


// Handles a toggle-control that is clicked, post to the url
$(document).on('click','.toggle-control',function(e){
    var element = $(this);
    var id = '#'+$(this).prop('id');
	var url = $(this).data('url');
	var var1 = $(this).data('var1');
    var var2 = $(this).data('var2');
	$.post(url,{"id":id,"var1":var1,"var2":var2},function(data){
        debug_out(data);
        var d = $.parseJSON(data);
		if(d.success)
		{
            if (d.value == 1){element.prop('checked',true);}
            else {element.prop('checked',false);}
        }
        else
        {
            debug_out('error in toggle');
        }
    }).fail(function(xhr, status, error){report_ajax_error(xhr, status, error);});
    
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



// =====================================
// Ajax posting

$(document).on('click','.ajax-submit-button',function(e){
    e.preventDefault();
    var url = $(this).data('url');
    var post_data = get_post_data('.'+$(this).data('varclass'));
    var do_reload = true;
    var redirect = $(this).data('redirect');
    var redirect_delay = 5000;
    debug_out('Data to post: ',post_data);

    if($(this).data('reload') == false){do_reload = false;} 
    $.post(url,post_data, function(data){
        debug_out('Json response: ',data);
        if (data.success)
        {

            if(do_reload)
            {location.reload(); return;}

            if (data.message)
            {
                notice(data.message,'success');
            }

            if (redirect != undefined)
            {
                setTimeout(function(){
                    window.location.replace(redirect);
                },redirect_delay);
            }

        }
        else
        {
            notice(data.error,'error');
        }

    },'json').fail(function(xhr, status, error){report_ajax_error(xhr, status, error);});

});

function get_post_data(css_class)
{
    var postData = {};
    $(css_class).each(function(){
        var data = $(this).data();
        var id = $(this).data('post-id');
        var arrayid = $(this).data('post-arrayid');
        if (id != undefined)
        {
            if($(this).is(':checkbox')){postData[id] = $(this).is(':checked');}
            else{postData[id] = $(this).val();}   
            // $.each(data,function(key,val){
            //     if(key.match("^post"))
            //     {
            //         key = key.replace('post','').toLowerCase();
            //         postData[key] = val;
            //     }
            // });
        }
        // This part needs to be looked at along with schedule edit controller/view
        if (arrayid != undefined)
        {
            if (postData[arrayid] == undefined)
            {postData[arrayid] = [];}
            var newVal = {};
            newVal['value'] = $(this).val();
            $.each(data,function(key,val){
                if(key.match("^post"))
                {
                    key = key.replace('post','').toLowerCase();
                    newVal[key] = val;
                }
            });
            postData[arrayid].push(newVal);
        }
    });
   return postData;
}

// End Ajax posting
// ======================================


// Pop up notices after ajax success/fail/errors
function notice(text, noticetype, target)
{
	var setcolor = "black";
	var setx = 'left';
	var sety = 'bottom';
	var offx = 0;
	var offy = 0;
	switch(noticetype){
		case "success":
			setcolor = "green";
			break;
		case "warning":
			setcolor = "yellow";
		case "error":
			setcolor = "red";
		default:
    }
    
	new jBox('Notice', {
		content: text,
		attributes: {x: setx, y: sety},
		// position:{x: offx, y: offy},
		color:setcolor,
		closeButton:'title',
		closeOnEsc: true,
        stack: false,
        minWidth: 50,
        minHeight: 75,
        autoClose: 5000,
        animation: {open: 'slide:bottom', close: 'slide:bottom'}
    });

}

function debug_out(o,o2)
{
	if (window.DEBUG_ENABLED){
		if (o != undefined){console.log(o);}
		if (o2 != undefined){console.log(o2);}
	}
}