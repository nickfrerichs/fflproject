<?php //print_r($nfl_opp);?>
<?php //print_r($matchups);?>
<div class="row align-center">
    <div class="columns small-12 medium-12 large-6" style="padding:0">
        <div id="ls-c-matchup-dots" class="row">
            <div class="columns small-3"><a href="#"><i class="fi-arrow-left ls-c-dot-arrow" data-way="left"></i></a></div>
            <div class="text-center columns small-6">
                <?php foreach($matchups as $id => $matchup): ?>
                        <i class="fi-marker ls-c-dot" id="ls-c-dot-<?=$id?>"></i>
                <?php endforeach;?>
            </div>
            <div class="columns small-3 text-right"><a href="#"><i class="fi-arrow-right ls-c-dot-arrow" data-way="right"></i></a></div>
        </div>
        <?php foreach($matchups as $id => $matchup): ?>
            <table id="matchup-<?=$id?>" data-id="<?=$id?>" class="ls-c-matchup-table<?php if($id != 0){echo ' hide"';}?>" >
                <thead>
                    <th class="ls-c-playerlight"></th>
                    <th class="text-center"><?=$matchup['home_team']['team']->team_name?></th>
                    <th class="ls-c-teamscore text-left"><?=$matchup['home_team']['points']?></th>
                    <th class="text-center ls-c-position">vs</th>
                    <th class="ls-c-teamscore text-right"><?=$matchup['away_team']['points']?></th>
                    <th class="text-center"><?=$matchup['away_team']['team']->team_name?></th>
                    <th class="ls-c-playerlight"></th>
                </thead>
                <tbody>
                    <?php foreach($matchup['home_team']['starters'] as $key => $s):?>
                            <?php if (isset($matchup['home_team']['starters'][$key]['player'])){$hp = $matchup['home_team']['starters'][$key];}else{$hp=False;}?>
                            <?php if (isset($matchup['away_team']['starters'][$key]['player'])){$ap = $matchup['away_team']['starters'][$key];}else{$ap=False;}?>
                            <?php if ($hp){$hpclass=" ".$hp['teamclass']." p_".$hp['player']->player_id;}else{$hpclass="";}?>
                            <?php if ($ap){$apclass=" ".$ap['teamclass']." p_".$ap['player']->player_id;}else{$apclass="";}?>
                            <tr>
                                <?php if($hp): ?>
                                    <td class="ls-c-playerlight<?=$hpclass?>"></td>
                                    <td class="ls-c-playerbox ls-c-td-left<?=$hpclass?>" data-id="<?=$hp['player']->player_id?>" data-team="<?=$hp['teamclass']?>">
                                        <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $hp)); ?>
                                    </td>
                                    <td class="ls-c-playerscore text-center<?=$hpclass?>">
                                        <?php if($hp){echo $hp['player']->points;}else{echo "-";}?>
                                    </td>
                                <?php else: ?>
                                    <td></td><td class="ls-c-playerbox"></td><td></td>
                                <?php endif;?>
                                <td class="text-center ls-c-position"><?=$s['pos_text']?></td>

                                <?php if($ap): ?>
                                    <td class="ls-c-playerscore text-center<?=$apclass?>">
                                        <?php if($ap){echo $ap['player']->points;}else{echo "-";}?>
                                    </td>
                                    <td class="text-right ls-c-playerbox ls-c-td-right<?=$apclass?>" data-id="<?=$ap['player']->player_id?>" data-team="<?=$ap['teamclass']?>">
                                        <?php $this->load->view('user/season/scores/live/compact_player',array('p' => $ap)); ?>
                                    </td>

                                    <td class="ls-c-playerlight<?=$apclass?>"></td>
                                <?php else: ?>
                                    <td></td><td class="ls-c-playerbox"></td><td></td>
                                <?php endif;?>

                            </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
        <?php endforeach;?>

    </div>
</div>

<script>
adjustDots();

$(".ls-c-dot-arrow").on('click',function(event){
    var matches = [];
    var current = 0;
    var end = 0;
    var newmatch = 0;
    $(".ls-c-matchup-table").each(function(){
        if($(this).hasClass('hide') == false){current = $(this).data('id');}
        end = $(this).data('id');
        matches.push($(this).data('id'))
    });
    var cur_key = $.inArray(current,matches);
    if ($(this).data('way') == "left")
    {
        if(cur_key == 0){newmatch = matches[matches.length-1];}
        else{newmatch = matches[cur_key-1]}
    }
    if ($(this).data('way') == "right")
    {
        if(cur_key == matches.length-1){newmatch = matches[0];}
        else{newmatch = matches[cur_key+1]}
    }
    $('#matchup-'+newmatch).removeClass('hide');
    $('#matchup-'+current).addClass('hide');
    adjustDots();
    event.preventDefault();
});

    function adjustDots()
    {
        $(".ls-c-matchup-table").each(function(){
            if($(this).is(":visible"))
            {
                $("#ls-c-dot-"+$(this).data('id')).removeClass('ls-c-dot-hidden');
            }
            else
            {
                $("#ls-c-dot-"+$(this).data('id')).addClass('ls-c-dot-hidden');
            }
        });
        // Get all matchups
    }
</script>
