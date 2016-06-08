<?php //print_r($content);?>


<div class="row">
    <div class="columns">
        <?php if($content->text_id == "news"):?>
            <input id="title" type="text" value="<?=$content->title?>">
        <?php else:?>
            <h5><?=$content->title?></h5>
        <?php endif;?>
    </div>
</div>
<div class="row">
    <div class="columns">
        <div id="content">
        </div>
    </div>
</div>
<div class="row">
    <div class="columns">
        <button id="submit" class="button small">Save</button>
    </div>
</div>
<script src="<?=site_url('js/tinymce/tinymce.min.js')?>"></script>
<script>

$(document).ready(function(){

    tinymce.init({
        mode: 'textareas',
        selector:'#content',
        plugins: 'table colorpicker',
        table_styles: 'Default=table',
        init_instance_callback: "loadcontent"});
})

$("#submit").on('click',function(){
    savecontent();
});


function loadcontent()
{
    var url = "<?=site_url('admin/content/loadcontent')?>";
    $.post(url,{'id' : "<?php if (!isset($content->id)){echo '0';}else{echo $content->id;} ?>"}, function(data){
        console.log(data);
        tinymce.activeEditor.setContent(data);
    });
}

function savecontent()
{
    var url = "<?=site_url('admin/content/savecontent')?>";
    var content = tinymce.activeEditor.getContent({format:'raw'});
    var title = $("#title").val();
    $.post(url,{'content_id' : "<?=$content->id?>",'content' : content, 'title' : title}, function(data){
        window.location.replace("<?=site_url('admin/content/view/'.$content->text_id)?>");
    });
}

</script>
