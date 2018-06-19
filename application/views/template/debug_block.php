<div class="section">
    <div class="is-size-5">Debugging On</div>
    <br>
    <div class="section" style="border-style:solid;border-width:1px;border-color:black">
        <span class="is-size-6">Variables available to this view</span> <a href="" id="debug-toggle-var-dump">(Show)</a>
        <pre id="debug-var-dump" style="font-size:.8em" class="is-hidden">
                <?=var_dump($this->_ci_cached_vars)?>
        </pre>
    </div>
</div>

<script>
    $('#debug-toggle-var-dump').on('click',function(e){
        console.log($(this).text());
        e.preventDefault();
        if ($(this).text() == "(Show)")
        {
            $('#debug-var-dump').removeClass('is-hidden');
            $(this).text("(Hide)")
        }
        else if ($(this).text() == "(Hide)")
        {
            $('#debug-var-dump').addClass('is-hidden');
            $(this).text("(Show)")
        }
    });
</script>