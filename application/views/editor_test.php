<script src="<?=site_url('js/tinymce/tinymce.min.js')?>"></script>
<script> tinymce.init({
    selector:'#content',
    plugins: 'table colorpicker'
});</script>

<style>
#editor{

}
</style>

<div class="container">
    <br>
    <div id="content">
    </div>

    <button id="submit" class="btn btn-default">Save</button>

</div>
<script>

$(document).ready(function(){

})

$("#submit").on('click',function(){

});

</script>
