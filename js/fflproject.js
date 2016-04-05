//Custom functions used site-wide

function showMessage(text, type)
{
	var html = '<div class="messages alert '+type+'" role="alert">'+text+'</div>';
	$('#messages').html(html);
	$('#messages').slideDown();
	setTimeout(function(){
//		$('#messages').addClass('hide');
	$('#messages').slideUp();
	},4000);
	console.log("here");
}

// .change-control and .cancel-control classes for editing a single setting/database value at a time.
$(".change-control").on('click', function(e){
    // An early attempt to make some resusable javascript
    // Classes change-control and cancel-control with a *-field *-control *-cancel
    // change-control element needs data-url, value, optional data-var1, data-var2, data-var3
    var name = $(this).attr('id').replace('-control','');
    var control = $("#"+name+"-control");
    var cancel = $("#"+name+"-cancel");
    var field = $("#"+name+"-field");
    var newvalue = $("#"+name+"-new");
    var url = $(this).data('url');
    var var1 = $(this).data('var1');
    var var2 = $(this).data('var2');
    var var3 = $(this).data('var3');

    if($(this).text() == "Change")
    {
        var current = field.text();
        control.data('current',current);
        field.html(
            '<input id="'+name+'-new" type="text" placeholder="'+current+'">'
        )
        control.text('Save');
        cancel.text('Cancel');
        return;
    }
    if($(this).text() == 'Save')
    {
        $.post(url,{type:name,value:newvalue.val(),var1:var1, var2:var2, var3:var3}, function(data){
            if(data)
            {
                var d = $.parseJSON(data);
                if(d.success){field.html(newvalue.val());}
            }
        });
        //field.text(control.data('current'));
        control.text('Change');
        cancel.text('');
    }

});

$(".cancel-control").on('click', function(){
    var name = $(this).attr('id').replace('-cancel','');
    var control = $("#"+name+"-control");
    var field = $("#"+name+"-field");
    control.text('Change');
    field.text(control.data('current'));
    $(this).text('');
})
