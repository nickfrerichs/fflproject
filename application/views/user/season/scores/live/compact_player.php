<?php if($p):?>
        <div><b><?=$p['player']->short_name?></b> <?=$p['player']->club_id?></div>
        <div class="ls-c-gamestatus"></div>
        <div class="progress success ls-c-drivebar hide" role="progressbar">
            <span class="progress-meter" style="width: 25%;">
                <p class="progress-meter-text ls-c-drivebar-text"> 25 </p>
            </span>
        </div>
<?php endif;?>
