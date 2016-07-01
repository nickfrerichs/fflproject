<div class="row">
    <div class="columns">
        <?php //print_r($picks);?>
        <table>
            <thead>
                <th>Round</th>
                <th>Original Pick Owner</th>
                <th>Current Pick Owner</th>
            </thead>
            <tbody>
                <?php foreach($picks as $p): ?>
                    <tr>
                        <td><?=$p->round?></td>
                        <td><?=$p->org_team_name?></td>
                        <td><?=$p->pick_team_name?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
