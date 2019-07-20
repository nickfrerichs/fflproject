<div class="section">
    <div class="container">
        <div class="f-scrollbar">
            <table class="table is-fullwidth is-narrow is-striped f-min-width-small">
                <thead>
                    <th>NFL Pos.</th><th>Description</th>
                </thead>
                <tbody>
                    <?php foreach($defs as $d): ?>
                        <tr>
                            <td><?=$d->pos_text?></td>
                            <td>
                                <?=$d->points?> points for every <?=$d->per?> <?=$d->cat_short_text?>

                                <?php if($d->per != 1): ?>
                                <div style="font-style:italic; font-size:.8em">
                                always round <?php if($d->round){echo "up";}else{echo "down";}?> 
                                </div>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        </div>
    </div>
</div>
