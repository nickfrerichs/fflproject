<?php //print_r($data);?>
<div class="container">
    <div class="row">
        <br>
        <a href="<?=site_url('myteam/waiverwire')?>">Back to Waiver Wire</a>
        <h4>Waiver Wire Priority</h4>
        <h5>If more than one team claims a player before waivers have cleared, lowest priority team wins.</h5>
        <table class="table table-striped">
        <?php if($data['type'] == "standings"): ?>
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
        <?php elseif($data['type'] == "draft_order"): ?>
            <?php foreach($data['priority'] as $key => $p): ?>
            <tr>
                <td><?=$key+1?></td>
                <td><?=$p->team_name?></td>
                <td><?=$p->first_name?> <?=$p->last_name?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </table>
    </div>
</div>
