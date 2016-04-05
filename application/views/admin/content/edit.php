<script src="<?=site_url('js/tinymce/tinymce.min.js')?>"></script>
<script> tinymce.init({
    mode: 'textareas',
    selector:'#content',
    plugins: 'table colorpicker',
    table_styles: 'Default=table'
});</script>

<div class="container">
    <div class="row">
        <h3><?=$content->title?></h3>
    </div>
    <div id="content">
    </div>

    <button id="submit" class="btn btn-default">Save</button>
</div>

<script>

$(document).ready(function(){

    loadcontent();
})

$("#submit").on('click',function(){
    savecontent();
});


function loadcontent()
{
    var url = "<?=site_url('admin/content/loadcontent')?>";
    $.post(url,{'text_id' : "<?php if (!isset($content->text_id)){echo '0';}else{echo $content->text_id;} ?>"}, function(data){
        tinymce.activeEditor.setContent(data);
    });
}

function savecontent()
{
    var url = "<?=site_url('admin/content/savecontent')?>";
    var content = tinymce.activeEditor.getContent({format:'raw'});
    $.post(url,{'content_id' : "<?=$content->id?>",'content' : content}, function(data){
        window.location.replace("<?=site_url('admin/content/view/'.$content->text_id)?>");
    });
}

</script>
