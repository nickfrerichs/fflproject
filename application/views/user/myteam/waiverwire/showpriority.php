<?php //print_r($data);?>

<div class="row">
    <div class="column">
        <a href="<?=site_url('myteam/waiverwire')?>">Back to Waiver Wire</a>
        <h4>Waiver Wire Priority</h4>
        <p>If more than one team claims a player before waivers have cleared, first listed team wins.</p>
    </div>
</div>
<div class="row">
    <div class="column">
        <?php if(count($data['priority']) > 0 && ($data['type'] == "standings" || $data['type'] == "draft_order")): ?>
            <table>
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
        <?php else: ?>
            No waiver wire priority set.
            <br><br>
        <?php endif; ?>
    </div>
</div>
