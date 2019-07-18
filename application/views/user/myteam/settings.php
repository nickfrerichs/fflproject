<?php //print_r($owner_info); ?>
<link href="<?=site_url('css/cropper.min.css')?>" rel="stylesheet">


<!-- Upload logo modal -->
<!-- <div class="reveal" id="logo-modal" data-reveal data-overlay="true">
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
</div> -->

<?php
$body = '   <form id="logo-form" action="" method="POST">
                <div class="file is-centered">
                    <label class="file-label">
                        <input id="logo-select" class="file-input" type="file" name="files" accept="image/*">
                        <span class="file-cta">
                            <span class="file-icon">
                            <i class="fas fa-upload"></i>
                            </span>
                            <span class="file-label">
                            Choose a file…
                            </span>
                        </span>
                    </label>
                </div>
                <br>
                <button id="logo-upload-button" name="logo-submit" type="submit" class="button is-hidden is-success">Upload</button>
                <br>
                <br>
            </form>
            <div style="max-height:500px;">
                <img id="team-logo-cropper" class="is-hidden" src="'.$team_uploaded_logo_url.'">
            </div>';

$this->load->view('components/modal', array('id' => 'logo-modal',
                                                    'title' => 'Upload Team Logo',
                                                    'body' => $body,
                                                    'reload_on_close' => False));



?>

<!-- Change Password Modal -->
<?php
$body = '   
            <div id="passerror" style="color:red">
            </div>
            <div id="passsuccess" style="color:green">
            </div>
            <div class="field has-text-left">
                <label class="label">Current Password</label>
                <div class="control">
                    <input id="current-password" class="input is-fullwidth" type="password"></input>
                </div>
            </div>

            <div class="field has-text-left">
                <label class="label">New Password</label>
                <div class="control">
                    <input id="password1" class="input is-fullwidth" type="password"></input>
                </div>
            </div>

            <div class="field has-text-left">
                <label class="label">Confirm Password</label>
                <div class="control">
                    <input id="password2" class="input is-fullwidth" type="password"></input>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button id="password-confirm" class="button is-success is-fullwidth is-medium">Confirm</button>
                </div>
            </div>

        ';

$this->load->view('components/modal', array('id' => 'change-password-modal',
                                                    'title' => 'Change Password',
                                                    'body' => $body,
                                                    'reload_on_close' => False));



?>

<div class="section">
    <div class="container">
            <div class="title">
                Team Settings
            </div>


            <div class="columns">
                <div class="column is-one-third">
                    Team Name
                </div>
                <div class="column">

                                <?php $this->load->view('components/editable_text',array('id' => 'teamname', 
                                                                                        'value' => $team_info->long_name,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Team Abbreviation
                </div>
                <div class="column">

                                <?php $this->load->view('components/editable_text',array('id' => 'abbreviation', 
                                                                                        'value' => $team_info->team_abbreviation,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Team Logo
                </div>
                <div id="team-logo" class="column">

                                <?php if($team_info->logo): ?>
                                <img src="<?=$team_thumb_logo_url?>">
                                <?php else: ?>
                                None
                                <?php endif; ?>
                                
                                <br><a href="#" id="logo-change">Upload Team Logo</a>
                </div>
            </div>

            <hr>
            <div class="title">
                Owner Settings
            </div>


            <div class="columns">
                <div class="column is-one-third">
                    First Name
                </div>
                <div class="column">

                                <?php $this->load->view('components/editable_text',array('id' => 'first', 
                                                                                        'value' => $owner_info->first_name,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>
                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Last Name
                </div>
                <div class="column">
        
                                <?php $this->load->view('components/editable_text',array('id' => 'last', 
                                                                                        'value' => $owner_info->last_name,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Email Address
                </div>
                <div class="column">

                                <?php $this->load->view('components/editable_text',array('id' => 'email', 
                                                                                        'value' => $owner_info->email,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Phone Number
                </div>
                <div class="column">
                                <?php $this->load->view('components/editable_text',array('id' => 'phone', 
                                                                                        'value' => $owner_info->phone_number,
                                                                                        'url' => site_url('myteam/settings/ajax_change_item')));?>

                </div>
            </div>
            <div class="columns">
                <div class="column is-one-third">
                    Password
                </div>
                <div class="column">
                    <a id="launch-change-password" href="#">Change Password</a>
                </div>
            </div>
                


            <div class="columns">
                <div class="column is-one-third">
                    Show Chat Balloons
                </div>
                <div class="column">
                                <?php $this->load->view('components/toggle_switch',
                                                array('id' => 'chat_balloon',
                                                        'url' => site_url('myteam/settings/ajax_toggle_item'),
                                                        'is_checked' => $owner_info->chat_balloon));
                                ?>
                </div>
            </div>

    </div>
</div>


<script src="<?=site_url('js/cropper.min.js')?>"></script>

<script>

// Logo upload stuff
$("#logo-change").on('click',function(){
    //$("#logo-file").trigger("click");
    $('#logo-modal').addClass('is-active')
    //$("#logo-upload-button").toggleClass("hidden");
});

$("#logo-select").on('change',function(){
    $("#logo-upload-button").removeClass('is-hidden');
    $("#logo-upload-button").text('Upload');
});

$("#launch-change-password").on('click',function(){
    $('#change-password-modal').addClass('is-active');
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
                $("#team-logo-cropper").removeClass('is-hidden');
                $("#logo-upload-button").text('Save as Team Logo');
                //$image.cropper('reset', true);
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log('Errors: '+textStatus);
            }

        });

        console.log("<?=$team_uploaded_logo_url?>");
    }
    else if($(this).text() == "Save as Team Logo")
    {

        var cropData = $("#team-logo-cropper").cropper('getData',true);

        // ajax/post call to crop and save the image.
        var url = "<?=site_url('myteam/settings/ajax_crop_team_logo')?>";
        $.post(url,{'cropData':cropData}, function(data){

            console.log(data);
            // Close modal and reset upload text
            $("#logo-modal").removeClass('is-active');
            setTimeout(function(){
                         window.location = "<?=current_url();?>";
                    }, 250);
            //$("#logo-upload-button").text('Upload');
            //$("#logo-select").val('');
        });
    }
});

// Password reset stuff
$("#password-confirm").on('click',function(){
    var pass1 = $("#password1").val();
    var pass2 = $("#password2").val();
    var curpass = $("#current-password").val();
    $("#password1").css('background-color','');
    $("#password2").css('background-color','');
    $("#current-password").css('background-color','');
    $("#password-control").data('success','true');

    if (pass1 == pass2)
    {
        // Post to controller, check return value to make sure requirements met
        url = "<?=site_url('myteam/settings/ajax_change_password')?>";
        $.post(url,{'curpass':curpass, 'newpass':pass1}, function(data){
            if (data != "1")
            {
                //$("#password-control").data('success','false');
                $("#passerror").text('Current password not correct!');
                $("#current-password").css('background-color','#FFCCCC');

            }
            else {
                //showMessage("Password updated!","alert");
                reset_password_control();
                //$('#change-password-modal').removeClass('is-active');
                $('#passsuccess').text('Password changed: Logging out');
                setTimeout(function(){window.location.replace('<?=site_url("auth/logout")?>');},3000);
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
});

$("#password-cancel").on('click',function(){
    reset_password_control();
});

function reset_password_control()
{
    $("#password1").css('background-color','');
    $("#password2").css('background-color','');
    $("#current-password").css('background-color','');
    var field = "********************";
}
</script>
