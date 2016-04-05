<div class="table-heading text-center"><h4>
    <?php if(($scheduled_start_time > $current_time) && ($start_time > $current_time)): ?>
    Draft Begins
    <?php elseif (empty($current_pick)): ?>
    End of Draft
    <?php else: ?>
    Now Picking
    <?php endif;?>
</h4></div>
<div style="border-top: 1px solid #bbb">
</div>

<?php //print_r($current_pick);?>
<?php if(($scheduled_start_time > $current_time) && empty($current_pick)): // Draft is in the future?>
    <div class="d-block-team-name"><?=date('D M j - g:i a',$scheduled_start_time)?></div>
    <div id="countdown" class="d-block-clock" data-deadline=""
        data-currenttime="<?=$current_time?>" data-seconds="-1"
        data-paused="" data-starttime="<?=$start_time?>" data-teamid="">
    </div>
<?php elseif (empty($current_pick)): // Draft is over??>
    <div class="d-block-team-name">Draft is over.</div>
<?php else: ?>
    <div class="d-block-team-name"><?=$current_pick->team_name?></div>
    <?php if($current_pick->logo): ?>
        <div>
            <img id="d-block-team-logo" src="<?=$logo_url?>">
        </div>
    <?php else: ?>
        <div>
            <img id="d-block-team-logo" src="<?=$default_logo_url?>">
        </div>
    <?php endif; ?>
    <div class="d-block-round">Round <?=$current_pick->round?>

    Pick <?=$current_pick->pick?></div>

    <div id="countdown" class="d-block-clock" data-deadline="<?=$current_pick->deadline?>"
        data-currenttime="<?=$current_time?>" data-seconds="<?=$seconds_left?>"
        data-paused="<?=$paused?>" data-starttime="<?=$start_time?>" data-teamid ="<?=$current_pick->team_id?>">...
    </div>
<?php endif; ?>

<script>

</script>
