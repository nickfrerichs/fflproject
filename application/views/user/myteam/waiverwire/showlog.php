
<div class="section">
    <div class="container">
        <a href="<?=site_url('myteam/waiverwire')?>">Back to Waiver Wire</a>
        <br><br>
        <div class="title">Waiver Wire Log</div>
        <hr>
        <p>Players dropped before <b><?=date("n/j/y g:i a",$clear_time)?></b> have cleared waivers.</p>
        <hr>
        <div class="f-scrollbar">
            <table class="table is-fullwidth is-striped is-size-7-mobile" style="min-width:300px;">
                <thead>
                </thead>
                <tbody>
                    <?php foreach($log as $l): ?>
                        <?php if($clear_time < $l->transaction_date != "" && $l->drop_id != ""):?>
                            <tr style="background-color:#CCCCCC">
                        <?php else: ?>
                            <tr>
                        <?php endif; ?>
                            <td>
                                <?=date("n/j/y g:i a",$l->transaction_date)?>
                                <?php if($l->priority_used): ?>
                                    <br><span style="font-size:.8em;font-style:italic">WW Priority</span>
                                <?php endif;?>
                            </td>
                            <td>
                                <div>
                                    <?php if($l->drop_id == ""): ?>
                                    <b>Drop:</b> No One
                                <?php else:?>
                                    <b>Drop:</b> <?=$l->drop_first.' ',$l->drop_last?> (<?=$l->drop_pos.' - '.$l->drop_club_id?>)
                                <?php endif;?>
                                </div>
                                <div>
                                    <b>Pick up:</b> <?=$l->pickup_first.' ',$l->pickup_last?> (<?=$l->pickup_pos.' - '.$l->pickup_club_id?>)
                                </div>
                            </td>
                            <td>
                                <div>
                                    <b>Team:</b> <?=$l->team_name?>
                                </div>
                                <div>
                                    <b>Owner:</b> <?=$l->owner_first.' '.$l->owner_last?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
