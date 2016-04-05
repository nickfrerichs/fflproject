<script type="text/javascript">
$(document).ready(function(){
	setInterval(function(){get_stats();}, 10000);
	get_stats();
});

function get_stats(){
	$.ajax({
		url: "<?=site_url('statistics/scores/get_live_json')?>",
		type: "GET",
		dataType: "json",
		success: function(data){
			$.each(data.players, function(player_id, points){
				var fadetime = 400;
				e = ('#points_'+player_id).trim();
				if (points != null)
				{

					if ($(e).text().trim() != points.toString())
					{
						updateScore(e,points.toString());	
					}
				}
			});
			$.each(data.teams, function(team_id, points){
				e = ('#teampoints_'+team_id).trim();
				if(points != null)
				{
					if ($(e).text() != points.toString())
					{
						updateScore(e,points.toString());
					}

				}
			});
			$.each(data.live_games, function(gsis_id, data){
				if (data.quarter != 'f')
				{
					e = ('.t_'+data.off_club_id).trim();
					$(e).addClass('live-score-points');
					console.log(e);
					e = ('.t_'+data.def_club_id).trim();
					$(e).addClass('live-score-points');
					console.log(e);

					// Offensive player - team has ball
					e = ('.off_'+data.off_club_id).trim();
					text = data.down+' & '+data.to_go+' '+data.yard_line;
					updateStatusItem(e, text, 'normal');

					// Offensive player - team on defense.
					e = ('.off_'+data.def_club_id).trim();
					text = data.down+' & '+data.to_go+' '+data.yard_line;
					text = data.def_club_id+' on defense.';
					updateStatusItem(e, text, 'soft');

					// Defensive player - team on defense
					e = ('.def_'+data.def_club_id).trim();
					//data.quarter+' '+data.time+' '+
					text = data.down+' & '+data.to_go+' '+data.yard_line;
					updateStatusItem(e, text, 'normal');

					// Defensive player - team on offense.
					e = ('.def_'+data.off_club_id).trim();
					text = data.off_club_id+' on offense.';
					text = data.down+' & '+data.to_go+' '+data.yard_line;
					updateStatusItem(e, text, 'soft');

					e = ('.time_'+gsis_id).trim();
					text = "("+data.quarter+" "+data.time+")";
					updateStatusItem(e, text, 'soft');
				}
				else
				{
					text = data.match;
					setFinal(('.off_'+data.off_club_id).trim(), text);
					setFinal(('.off_'+data.def_club_id).trim(), text);
					setFinal(('.def_'+data.def_club_id).trim(), text);
					setFinal(('.def_'+data.off_club_id).trim(), text);

					e = ('.t_'+data.off_club_id).trim();
					$(e).removeClass('live-score-points');
					e = ('.t_'+data.def_club_id).trim();
					$(e).removeClass('live-score-points');
				}

			});
		}
	});
}

function setFinal(element, text)
{
	if ($(element).text() != text)
	{
		$(element).text(text);
	}
}

function updateStatusItem(element, text, cssclass)
{
	if ($(element).text() != text)
	{
		$(element).text(text);
		if (cssclass == 'normal'){
			$(element).addClass('live-score-normal');
			$(element).removeClass('live-score-soft');
			$(element).removeClass('nonlive-score');
		}
		if (cssclass == 'soft'){
			$(element).addClass('live-score-soft');
			$(element).removeClass('live-score-normal');
			$(element).removeClass('nonlive-score');
		}
	}
		
}

function updateScore(element, points)
{
	fadetime = 400;
	$(element).animate({opacity: 0}, fadetime/4);
	setTimeout(function()
	{
		if ((parseInt(points) - parseInt($(element).text()) > 0))
			{$(element).css('color','#00CC00');}
		else
			{$(element).css('color','#FF0000');}
		$(element).css('font-weight','bold');	
		$(element).text(points);
	},fadetime/4);
	$(element).animate({opacity: 1}, fadetime);
	setTimeout(function()
	{
		$(element).removeAttr('style');
	},30000);
}
</script>

<?php //print_r($gsis); ?>
<?php //print_r($games); ?>

<?php $this->load->view('user/statistics/scores/nav', 
        array('years' => $years, 'year' =>$year, 'week' => $week));?>


<?php $g = $games[0]; unset($games[0]); ?>



<!-- Display the game I'm involved in -->
<div class="container">
	<div class="page-heading text-center">This is the live scoring page, Matt</div>
	<div class="page-heading text-center"> Week <?=$week?></div>
	<div class="col-xs-12">
		<?php $p_count = max(count($g['home']['players']), count($g['away']['players'])); ?>
		<table class="table table-condensed table-striped light-bg text-l2">
			<thead class="text-l2">
				<th class="hidden-xxs"></th>
				<th colspan='2'><?=$g['home']['team_name']?></th>
				
				<th id ="teampoints_<?=$g['home']['id']?>" class="text-right"><?=$g['home']['points']?></th>
				<th class="hidden-xxs"></th>
				<th colspan='2'><?=$g['away']['team_name']?></th>
				<th id ="teampoints_<?=$g['away']['id']?>" class="text-right"><?=$g['away']['points']?></th>
			</thead>

			<tbody>
			<?php for($i=0; $i<$p_count; $i++): ?>
                <tr>
                    <?php if(isset($g['home']['players'][$i])): ?>
                        <?php $h = $g['home']['players'][$i]?>
                        <?php if (isset($h['points'])){$points = $h['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs">
                        	<?=$h['pos']?>
                        </td>
                        <td class="">
                        	<a href="<?=site_url('statistics/player/id/'.$h['id'].'/'.$year)?>"><?=$h['name']?></a>
                        </td>
                        <?php if(stripos($h['pos'],'_D')===false){$p='off_';}else{$p='def_';}?>
                        <td class="hidden-xxs">
                        	<span class="time_<?=$gsis[$h['club_id']]['gsis']?>">
                        	</span>
                        	<span class="<?=$p.$h['club_id']?> nonlive-score">
                        		<?=$gsis[$h['club_id']]['match']?>
                        	</span>
                        </td>
                        <td class="">
                        	<div id = "points_<?=$h['id']?>" class = "t_<?=$h['club_id']?>">
                        	<?=$points?>
                        	</div>
                        </td>
                    <?php else: ?> 
                        <td class="hidden-xxs">-</td><td class="">-</td><td class="text-right">-</td>
                    <?php endif; ?>
                 
                    <?php if(isset($g['away']['players'][$i])): ?>
                        <?php $a = $g['away']['players'][$i]?>
                        <?php if (isset($a['points'])){$points = $a['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs">
                        	<?=$a['pos']?>
                        </td>
                        <td class="">
                        	<a href="<?=site_url('statistics/player/id/'.$a['id'].'/'.$year)?>"><?=$a['name']?></a>
                        </td>
                        <?php if(stripos($a['pos'],'_D')===false){$p='off_';}else{$p='def_';}?>
                        <td class="hidden-xxs">
                        	<span class="time_<?=$gsis[$a['club_id']]['gsis']?>">
                        	</span>
                        	<span class="<?=$p.$a['club_id']?> nonlive-score">
                        		<?=$gsis[$a['club_id']]['match']?>
                        	</span>
                        	
                        </td>
                        <td>
                        	<div d = "points_<?=$a['id']?>" class = "t_<?=$a['club_id']?>">
                        	<?=$points?>
                        </div>
                        </td>
                    <?php else: ?>
                        <td class="hidden-xxs">-</td><td class="">-</td><td class="r-align">-</td>
                    <?php endif; ?>
                </tr>
             <?php endfor; ?>
			</tbody>
		</table>
	</div>


<!-- Display the league's other games -->
<div class="table-heading text-center">Other games</div>
<?php foreach($games as $g): ?>
	<div class="col-md-6">
		<?php $p_count = max(count($g['home']['players']), count($g['away']['players'])); ?>
		<table class="table table-condensed table-striped">
			<thead class="text-l2">
				<th class="hidden-xxs"></th><th><?=$g['home']['team_name']?></th>
				<th id ="teampoints_<?=$g['home']['id']?>" class="text-right"><?=$g['home']['points']?></th>
				<th></th>
				<th class="hidden-xxs"></th><th><?=$g['away']['team_name']?></th>
				<th id ="teampoints_<?=$g['away']['id']?>" class="text-right"><?=$g['away']['points']?></th>
			</thead>
			<tbody>
				<?php for($i=0; $i<$p_count; $i++): ?>
                <tr>
                    <?php if(isset($g['home']['players'][$i])): ?>
                        <?php $h = $g['home']['players'][$i]?>
                        <?php if (isset($h['points'])){$points = $h['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs"><?=$h['pos']?></td>
                        <td class="small-text"><a href="<?=site_url('statistics/player/id/'.$h['id'].'/'.$year)?>"><?=$h['name']?></a></td>
                        <td id = "points_<?=$h['id']?>" class="text-right t_<?=$h['club_id']?>"><?=$points?></td>
                    <?php else: ?> 
                        <td class="hidden-xxs">-</td><td class="small-text">-</td><td class="text-right">-</td>
                    <?php endif; ?>
                    <td></td>
                    <?php if(isset($g['away']['players'][$i])): ?>
                        <?php $a = $g['away']['players'][$i]?>
                        <?php if (isset($a['points'])){$points = $a['points'];}else{$points = '-';}?>
                        <td class="hidden-xxs"><?=$a['pos']?></td>
                        <td class="small-text"><a href="<?=site_url('statistics/player/id/'.$a['id'].'/'.$year)?>"><?=$a['name']?></a></td>
                        <td id = "points_<?=$a['id']?>" class="text-right t_<?=$a['club_id']?>"><?=$points?></td>
                    <?php else: ?>
                        <td class="hidden-xxs">-</td><td class="small-text">-</td><td class="r-align">-</td>
                    <?php endif; ?>
                </tr>
             <?php endfor; ?>
			</tbody>
		</table>
	</div>
<?php endforeach; ?>
</div>