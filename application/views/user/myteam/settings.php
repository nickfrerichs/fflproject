<?php //print_r($owner_info); ?>
<link href="<?=site_url('css/cropper.min.css')?>" rel="stylesheet">

<!-- Confirm modal -->
<div class="reveal" id="confirm-modal" data-reveal data-overlay="true">
    <div class="text-center">
            <div class="drop-text">Drop: No One</div>
            <div class="pickup-text">Pick up: No One</div>
        <button class="button" type="button" id="confirm-drop">
            Confirm
        </button>
        <button class="button" type-"button" id="cancel-drop" data-close aria-label="Close modal">
            Cancel
        </button>
    </div>
</div>

<!-- Upload logo modal -->
<div class="reveal" id="logo-modal" data-reveal data-overlay="true">
    <h4> Upload Team Logo </h4>
    <hr>
        <form id="logo-form" action="" method="POST">
            <input id="logo-select" name="files" type="file" class="file text-right" accept="image/*">
            <br>
            <button id="logo-upload-button" name="logo-submit" type="submit" class="button hide">Upload</button>
        </form>
    <div style="max-height:500px;">
        <img id="team-logo-cropper" class="hide" src="<?=$team_uploaded_logo_url?>">
    </div>
</div>


<div class="row">
    <div class="columns">
        <h3>Settings</h3>
    </div>
</div>


<div class="row callout">
    <div class="columns">
        <div class="row">
            <div class="columns">
                <h4>Team Settings</h4>
            </div>
        </div>
        <div class="row">
            <div class="columns">
                <table class="table-condensed">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Team Name</b></td>

                            <td id="teamname-field"><?=$team_info->long_name?></td>
                            <td>
                                <a href="#" id="teamname-control" class="change-control" data-url="<?=site_url('myteam/settings/ajax_change_item')?>">Change</a>
                                <a href="#" id="teamname-cancel" class="cancel-control"></a>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Team Logo</b></td>
                            <td id="team-logo">
                                <?php if($team_info->logo): ?>
                                <img src="<?=$team_thumb_logo_url?>">
                                <?php else: ?>
                                None
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="#" id="logo-change">Upload</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row callout">
    <div class="columns">
        <div class="row">
            <div class="columns">
                <h4>Owner Settings</h4>
            </div>
        </div>
            <br>
        <div class="row">
        <div class="columns">
                <table class="table-condensed">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>First Name</b></td>
                            <td><?=$owner_info->first_name?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Last Name</b></td>
                            <td><?=$owner_info->last_name?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Phone Number</b></td>
                            <td id="phone-field"><?=$owner_info->phone_number?></td>
                            <td>
                                <a href="#" id="phone-control" class="change-control" data-url="<?=site_url('myteam/settings/ajax_change_item')?>">Change</a>
                                <a href="#" id="phone-cancel" class="cancel-control"></a>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Password</b></td>
                            <td id="password-field">********************</td>
                            <td class="text-left"><a href="#" id="password-control" data-command="edit">Change</a>
                                                <a href="#" id="password-cancel"></a>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Show Chat Balloons</b></td>
                            <td></td>
                            <td>
                                <div class="switch tiny">
                                <input  class="switch-input toggle-control" data-item="chat_balloon" data-url="<?=site_url('myteam/settings/ajax_toggle_item')?>"
                                    id="chat_balloon" type="checkbox" <?php if($owner_info->chat_balloon == "1"){echo "checked";}?>>
                                <label class="switch-paddle" for="chat_balloon">
                                </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<script src="<?=site_url('js/cropper.min.js')?>"></script>

<script>

// Logo upload stuff
$("#logo-change").on('click',function(){
    //$("#logo-file").trigger("click");
    $("#logo-modal").foundation('open');
    //$("#logo-upload-button").toggleClass("hidden");
});

$("#logo-select").on('change',function(){
    $("#logo-upload-button").removeClass('hide');
    $("#logo-upload-button").text('Upload');
});

$("#logo-upload-button").on('click submit',function(e){
    console.log($(this).text());
    e.stopPropagation();
    e.preventDefault();
    if($(this).text() == "Upload")
    {
        $("#logo-upload-button").text('Uploading...');
        var file = $("#logo-select")[0].files[0];
        var formData = new FormData();
        formData.append('files[]', file, file.name);
        var url = "<?=site_url('myteam/settings/ajax_upload_logo')?>"
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(data, textStatus, jqXHR)
            {

                $("#logo-upload-button").text('Done');

                console.log(data);
                console.log("Launching cropper");
                var $image = $('#team-logo-cropper');
                //$("#team-logo-cropper").attr("src", $("#team-logo-cropper").attr("src")+"?"+Math.random()

                $image.cropper({
                    aspectRatio: 9 / 9,
                    autoCropArea: 0.90,
                    strict: false,
                    guides: false,
                    highlight: false,
                    dragCrop: false,
                    cropBoxMovable: false,
                    cropBoxResizable: false
                });
                $image.cropper('replace',$("#team-logo-cropper").attr("src")+"?"+Math.random() );
                $("#team-logo-cropper").removeClass('hide');
                $("#logo-upload-button").text('Save');
                //$image.cropper('reset', true);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log('Errors: '+textStatus);
            }

        });

        console.log("<?=$team_uploaded_logo_url?>");
    }
    else if($(this).text() == "Save")
    {

        var cropData = $("#team-logo-cropper").cropper('getData',true);

        // ajax/post call to crop and save the image.
        var url = "<?=site_url('myteam/settings/ajax_crop_team_logo')?>";
        $.post(url,{'cropData':cropData}, function(data){

            console.log(data);
            // Close modal and reset upload text
            $("#logo-modal").foundation('close');
            setTimeout(function(){
                         window.location = "<?=current_url();?>";
                    }, 250);
            //$("#logo-upload-button").text('Upload');
            //$("#logo-select").val('');
        });
    }
});

// Password reset stuff
$("#password-control").on('click', function(){
    //alert($(this).data('command'));
    if ($(this).data('command') == "edit")
    {
        var field = "<div class='form-group'>"+
            "<input type='password' class='form-control' id='currentpassword' placeholder='current password'>"+
            "<input type='password' class='form-control' id='password1' placeholder='new password'>"+
            "<input type='password' class='form-control' id='password2' placeholder='repeat new password'>"+
            "<div id='passerror'></div>"+
            "</div>";
        $("#password-field").html(field);
        $("#password-control").text("Save");
        $('#password-control').data('command','save');
        $('#password-cancel').text('Cancel');

        return
    }
    if ($(this).data('command') == 'save')
    {
        var pass1 = $("#password1").val();
        var pass2 = $("#password2").val();
        var curpass = $("#currentpassword").val();
        $("#password1").css('background-color','');
        $("#password2").css('background-color','');
        $("#currentpassword").css('background-color','');
        $("#password-control").data('success','true');

        if (pass1 == pass2)
        {
            // Post to controller, check return value to make sure requirements met
            url = "<?=site_url('myteam/settings/ajax_change_password')?>";
            $.post(url,{'curpass':curpass, 'newpass':pass1}, function(data){
                console.log(data);
                if (data != "1")
                {
                    console.log("data not eq 1");
                    $("#password-control").data('success','false');
                    $("#passerror").text('Current password not correct!');
                    $("#currentpassword").css('background-color','#FFCCCC');

                }
                else {
                    showMessage("Password updated!","alert");
                    reset_password_control();
                }
            });
        }
        else
        {
            $("#passerror").text('New password doesn\'t match, fix it.');
            $("#password1").css('background-color','#FFCCCC');
            $("#password2").css('background-color','#FFCCCC');
            //showMessage("New password doesn't match!","alert");
        }
    }
});

$("#password-cancel").on('click',function(){
    reset_password_control();
});

function reset_password_control()
{
    $("#password1").css('background-color','');
    $("#password2").css('background-color','');
    $("#currentpassword").css('background-color','');
    var field = "********************";
    $("#password-field").html(field);
    $("#password-control").text("Change");
    $('#password-control').data('command','edit');
    $("#password-cancel").text("");
}
</script>
