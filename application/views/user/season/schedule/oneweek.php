<?php //print_r($schedule); ?>
<div class="columns is-multiline <?php if(isset($small) && $small){echo "is-size-7";}?>">
    <div class="column is-4 has-background-link has-text-light is-size-5 is-hidden-mobile">
        Home
    </div>
    <div class="column is-2 has-background-link has-text-light is-size-5 is-hidden-mobile">
        Score
    </div>
    <div class="column is-4 has-background-link has-text-light is-size-5 is-hidden-mobile">
        Away
    </div>
    <div class="column is-2 has-background-link has-text-light is-size-5 is-hidden-mobile">
        Score
    </div>
        <?php foreach($schedule as $s):?>
        <div class="column is-6-tablet is-12-mobile has-background-light">
            <div class="columns is-multiline is-mobile">
                <div class="column is-8">
                    <a href="<?=site_url('league/teams/view/'.$s->home_id)?>"><?=$s->home_name?></a>
                </div>
                <div class="column is-4">
                    <?=$s->home_score?>
                </div>
            </div>
        </div>
        <div class="column is-6-tablet is-12-mobile has-background-light">
            <div class="columns is-multiline is-mobile">
                <div class="column is-8">
                    <a href="<?=site_url('league/teams/view/'.$s->away_id)?>"><?=$s->away_name?></a>
                </div>
                <div class="column is-4">
                    <?=$s->away_score?>
                </div>
            </div>
        </div>
        <hr>
        <?php endforeach;?>
    
</div>