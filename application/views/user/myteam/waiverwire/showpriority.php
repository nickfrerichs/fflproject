<?php //print_r($data);?>

<div class="section">
    <div class="container">
    
    <div>
        <a href="<?=site_url('myteam/waiverwire')?>">Back to Waiver Wire</a>
    </div>
    <br>

        <div class="title is-size-5" >Waiver Wire Priority</div>
            <div class="notification">
                - If more than one team claims a player before waivers have cleared, the first listed team wins.<br>
                - The team winning the claim immediately moves to the last priority position.
            </div>
            <?php if(count($data['priority']) > 0 && ($data['type'] == "standings" || $data['type'] == "draft_order")): ?>
            <div class="f-scrollbar">
                <table class="table is-fullwidth is-size-7-mobile f-min-width-small">
                    <thead>
                        <th>Priority</th>
                        <th>Team Name</th>
                        <th>Owner</th>
                    </thead>
                    <tbody>
                        <?php foreach($data['priority'] as $key => $p): ?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><?=$p->team_name?></td>
                            <td><?=$p->first_name?> <?=$p->last_name?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                No waiver wire priority set.
                <br><br>
            <?php endif; ?>


        <br>
        <div class="title is-size-5">Waiver Wire Rules</div>
        <div class="notification">
            <?php if ($settings->type == "auto"):?>
            - Waiver wire approvals are automatic and the priority list will be used when contention for the same player occurs.
            <?php elseif($settings->type == "semi-automatic"): ?>
            - Waiver wire approvals are automatic unless contention for the same player occurs.
            <?php else: ?>
            - League admins approve all waiver wire requests.
            <?php endif;?>
            <?php if($settings->waiver_wire_disable_gt):?>
            <br>
            - Once a player's game time has started, any waiver wire requests will be held until after the week's final game is complete.
            <?php endif;?>
            <?php if($settings->waiver_wire_disable_days):?>
                <?php $dow = array(0=>'Sun',1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat');?>
                <br>
                - The Waiver wire is disabled on the following days: 
                <?php foreach(str_split($settings->waiver_wire_disable_days) as $i => $d):?>
                    <?=$dow[$d]?>.
                <?php endforeach;?>
            <?php endif;?>
        </div>

    </div>

</div>
