<?php foreach($players as $p):?>
            <tr>
                <td>
                    <a href="#" class="stat-popup" data-type="player" data-id="<?=$p->id?>"><?=$p->last_name.", ".$p->first_name?></a>
                </td>
                <td><?=$p->position?></td>
                <td><?=$p->club_id?></td>
                <td><?=$matchups[$p->club_id]['opp']?></td>
                <td><span class="hide-for-small-only">Week </span><?=$byeweeks[$p->club_id]?></td>
                <td><?=$p->points?></td>
                <td><?=$p->team_name?></td>
                <?php if($this->session->userdata('use_draft_ranks')): ?>
                <td><?=$p->draft_rank?></td>
                <?php endif;?>
            </tr>
        <?php endforeach; ?>
        <!-- <tr id="full-player-list-data" data-page="<?=$in_page?>" data-perpage="<?=$per_page?>" data-total="<?=$total?>">
        </tr> -->