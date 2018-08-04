<?php //print_r($content);?>


<div class="section">
    <?php if($content->text_id == "news"):?>
        <input class="input" id="title" type="text" value="<?=$content->title?>">
    <?php else:?>
        <div class="is-size-5"><?=$content->title?></div>
    <?php endif;?>

    <div id="content">
    </div>

    <br>

    <button id="submit" class="button is-small is-link">Save</button>
    <button id="cancel" class="button is-small is-link">Cancel</button>
</div>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="<?=site_url('js/tinymce/tinymce.min.js')?>"></script>
<!-- <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script> -->
<script>

$(document).ready(function(){
// Tried Quill, not worth the time to switch
// var quill = new Quill('#content', {
//   modules: { toolbar: true },
//   theme: 'snow'
// });

    tinymce.init({
        mode: 'textareas',
        selector:'#content',
        plugins: 'table colorpicker',
        table_styles: 'Default=table',
        init_instance_callback: "loadcontent",
        content_css: "<?=site_url('css/bulma.min.css')?>",
        height: "280"});

})

$("#submit").on('click',function(){
    savecontent();
});

$("#cancel").on('click',function(){
    window.location.replace("<?=site_url('admin/content')?>");
});


function loadcontent()
{
    var url = "<?=site_url('admin/content/loadcontent')?>";
    $.post(url,{'id' : "<?php if (!isset($content->id)){echo '0';}else{echo $content->id;} ?>"}, function(data){
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
