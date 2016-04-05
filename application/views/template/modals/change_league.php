<div class="modal fade" id="change-league-modal" aria-hidden="true" style="z-index:1060; top:25%">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h4>Your leagues</h4>
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
                <button class="btn btn-default" type-"button" id="change-league-cancel" data-dismiss="modal">
                    Cancel
                </button>
            </div>
        </div>
    </div>
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
        $("#change-league-modal").modal('hide');
        window.location.href = "<?=site_url()?>";
    });
});
$("#change-league-cancel").on('click',function(){
    $("#change-league-modal").modal('hide');
});
</script>
