
<div class="section">
    <div class="columns">
        <div class="column">

            <div class="container fflp-sm-container">

                <div class="field">
                <label class="label">Notation Symbol</label>
                    <div class="control">
                        <input id="notation-symbol" class="input" type="text" placeholder="example: #">
                    </div>
                    <p class="help">Symbol to denote on standings page</p>
                </div>
                <div class="field">
                    <label class="label">Notation Text</label>
                    <div class="control">
                        <input id="notation-text" class="input" type="text" placeholder="example: Playoff Berth">
                    </div>
                    <p class="help">What the symbol means</p>
                </div>

                <button id="add-button" class="button">Add</button>
            </div>
        </div>
    </div>
</div>

<script>

$('#add-button').on('click',function(){
    var url = "<?=site_url('admin/standings/ajax_add_notation')?>";
    var symbol = $('#notation-symbol').val();
    var text = $('#notation-text').val();
    $.post(url,{'symbol':symbol, 'text':text},function(data){
        if (data.success)
        {
            location.reload();
        }
    },'json');
});

</script>
