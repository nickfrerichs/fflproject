<?php //print_r($nfl_opp);?>
<?php //print_r($matchups);?>

<?php $this->load->view('components/stat_popup'); ?>

    <div class="columns is-centered">
        <div class="column is-12-tablet is-6-desktop">
            <div id="ls-c-matchup-dots" class="columns is-mobile">
                    <div class="column is-3 has-text-left">
                        <span class="icon"> 
                            <a href="#"><i class="fa fa-arrow-left ls-c-dot-arrow" data-way="left"></i></a>
                        </span>
                    </div>
                    <div class="text-center column is-6 has-text-centered">
                        <?php foreach($matchups as $id => $matchup): ?>
                            <span class="icon"> 
                                <a href="#"><i class="fa fa-circle ls-c-dot"id="ls-c-dot-<?=$id?>"></i></a>
                            </span>
                        <?php endforeach;?>
                    </div>
                    <div class="column is-3 has-text-right">
                        <span class="icon">
                            <a href="#"><i class="fa fa-arrow-right ls-c-dot-arrow" data-way="right"></i></a>
                        </span>
                    </div>

            </div>
            <?php foreach($matchups as $id => $matchup): ?>
                <?php $this->load->view('user/season/scores/live/compact_table',array('id' => $id, 'matchup' => $matchup, 'compact' => True)); ?>
            <?php endforeach;?>
            <div id="lsdata" class="is-hidden"></div>
        </div>
    </div>


<script>
adjustDots();
//sse_on("sse_live_scores");

$(".ls-c-playerbox").on('click',function(){
    showStatsPopup($(this).data('id'),'player');
});

$(".ls-c-dot-arrow").on('click',function(event){
    var matches = [];
    var current = 0;
    var end = 0;
    var newmatch = 0;
    $(".ls-matchup-table").each(function(){
        if($(this).hasClass('is-hidden') == false){current = $(this).data('id');}
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
    $('#matchup-'+newmatch).removeClass('is-hidden');
    $('#matchup-'+current).addClass('is-hidden');
    adjustDots();
    event.preventDefault();
});

    function adjustDots()
    {
        $(".ls-matchup-table").each(function(){
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
