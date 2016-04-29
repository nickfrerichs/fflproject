<div id="change-league-modal" class="reveal" data-reveal>

                <h5>Your leagues</h5>
                <select id="change-league-select">
                <?php foreach($this->session->userdata('leagues') as $leagueid => $leaguename): ?>
                    <?php if($leagueid == $this->session->userdata('league_id')): ?>
                        <option value=<?=$leagueid?> selected><?=$leaguename?></option>
                    <?php else: ?>
                        <option value=<?=$leagueid?>><?=$leaguename?></option>
                    <?php endif;?>
                <?php endforeach; ?>
                </select>
                <br>
                <br>
                <button class="btn btn-default" type="button" id="change-league-confirm">
                    Confirm
                </button>
                <button class="close-button" data-close aria-label="Close reveal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>

</div>

<script>
$("#footer-bar").on('click','#change-league-link',function(){
    $("#change-league-modal").modal();
});

$("#change-league-confirm").on('click',function(){
    var url="<?=site_url('myteam/settings/ajax_change_current_league')?>";
    var leagueid = $("#change-league-select").val();
    $.post(url,{'leagueid' : leagueid},function(data)
    {
        $("#change-league-modal").foundation('close');
        window.location.href = "<?=site_url()?>";
    });
});
</script>
