<?php $this->load->view('template/modals/stat_popup'); ?>

<!-- Drop modal -->
<div class="reveal large" id="add-modal" data-reveal data-overlay="true" data-multiple-opened-"true">
    <div>
            <div>
                <div class="text-center">
                    <h5>Add Player to <?=$team_name?></h5>
                </div>

                <div class="row align-center">
                    <div class="columns small-8">
                        <input type="text" class="player-list-text-input" data-for="main-list" placeholder="Player search">
                        <select data-for="main-list" class="player-list-position-select">
                            <option value="0">All</option>
                            <?php foreach($positions as $pos): ?>
                                <option value="<?=$pos->id?>"><?=$pos->text_id?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="columns">
                        <table class="table-condensed" >
                            <thead>
                                <th><a href="#" data-order="asc" data-for="main-list" data-by="last_name" class="player-list-a-sort">Name</a></th>
                                <th><a href="#" data-order="asc" data-for="main-list" data-by="position" class="player-list-a-sort">Position</a></th>
                                <th><a href="#" data-order="asc" data-for="main-list" data-by="club_id" class="player-list-a-sort">NFL Team</a></th>
                                <th></th>
                            </thead>
                            <tbody id="main-list" data-by="points" data-order="desc" data-url="<?=site_url('player_search/ajax_admin_get_player_list')?>">

                            </tbody>
                        </table>

                        <div class="row align-center">
                            <div class="columns text-right">
                                <ul class="pagination" role="navigation" aria-label="Pagination">
                                    <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="main-list">Previous</a></li>
                                </ul>
                            </div>
                            <div class="columns small-12 medium-3 text-center small-order-3 medium-order-2">
                                <div class="player-list-total" data-for="main-list"></div>
                                <br class="show-for-small-only">
                            </div>
                            <div class="columns text-left small-order-2 medium-order-3">
                                <ul class="pagination" role="navigation" aria-label="Pagination">
                                    <li class="pagination-next"><a href="#" class="player-list-next" data-for="main-list">Next</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="close-button" data-close aria-label="Close modal" type="button">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
    </div>
</div>

<div class="row">
    <div class="columns">
        <h5><?=$team_name?></h5>
    </div>
</div>

<div class="row">
    <div class="columns">
    <div id='teamlist'>
        <?php //print_r($roster); ?>
        <a href="#" data-open="add-modal">Add player </a>
        <table class="table-condensed table-striped">
            <tr>
                <td>Player</td><td>Team</td><td>Position</td>
            </tr>
            <?php foreach ($roster as $player){ ?>
            <tr>
                <td><?php echo $player->short_name; ?></td>
                <td><?php echo $player->club_id; ?></td>
                <td><?php echo $player->position; ?></td>
                <td>
                    <a href='<?php echo site_url('admin/rosters/removeplayer/'.$teamid.'/'.$player->player_id); ?>'>remove</a>
                </td>
            </tr>


            <?php }?>
        </table>
        </div>
    </div>
</div>

<script>
    $("#add-modal").on("open.zf.reveal",function(){
        $(updatePlayerList("main-list"));
    })

    $("#main-list").on("click",".add-button",function(){
        var url = "<?=site_url('admin/rosters/ajax_addplayer')?>";
        console.log("<?=$teamid?>");
        var teamid = "<?=$teamid?>";
        var playerid = $(this).data('id');
        var playername = $(this).data('name');
        $.post(url,{'teamid':teamid,'playerid':playerid},function(data){
            var d = jQuery.parseJSON(data);
			if (d.success == true)
            {
                notice(playername+" added to <?=$team_name?>");
                $(updatePlayerList("main-list"));
            }
        });
    });

    $("#add-modal").on("closed.zf.reveal",function(){
        location.reload();
    });
</script>
