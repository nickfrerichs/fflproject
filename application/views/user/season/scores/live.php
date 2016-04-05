Live

<?php if ($this->flexi_auth->is_admin()):?>
	<?php //print_r($gsis);?>
<?php endif;?>
<div class="container">
	<?php if ($week_type != "POST"):?>
	<?php $this->load->view('user/season/scores/nav', array('years' => $years, 'year' =>$year, 'week' => $week));?>
	<h3> Week <?=$week?></h3>
	<?php else: ?>
	<h3> Playoffs: Round <?=$week?></h3>
	<?php endif; ?>

	<?php //if (count($games) <= 0): ?>
		<?php $g = $games[0]; unset($games[0]); ?>

		<?php $row_count = max(count($g['home']['players']), count($g['away']['players'])); ?>



	<div class="row">
		<?php if($g['home']['id'] != 0):?>
			<?php if($g['away']['id'] != 0): ?>
				<div class="col-md-6">
			<?php else: ?>
				<div class="col-xs-12">
			<?php endif; ?>
				<?php $this->load->view('user/season/scores/display_team',
							array('team' => $g['home'], 'gsis' => $gsis));?>

			</div>
		<?php endif; ?>
		<?php if($g['home']['id'] != 0):?>
			<?php if($g['away']['id'] != 0): ?>
				<div class="col-md-6">
			<?php else: ?>
				<div class="col-xs-12">
			<?php endif; ?>

				<?php $this->load->view('user/season/scores/display_team',
							array('team' => $g['away'], 'gsis' => $gsis));?>

			</div>
		<?php endif; ?>
	</div>


	<h3>
		Other Games
	</h3>


	<?php foreach($games as $g):?>

		<div class="col-xs-12 col-lg-6">
		<?php if($g['home']['id'] != 0):?>
			<?php if($g['away']['id'] != 0): ?>
				<div class="col-xs-12 col-md-6">
			<?php else: ?>
				<div class="col-xs-12">
			<?php endif; ?>
				<?php $this->load->view('user/season/scores/display_team_min',
								array('team' => $g['home'])); ?>

			</div>
		<?php endif; ?>
		<?php if($g['away']['id'] != 0):?>
			<?php if($g['home']['id'] != 0): ?>
				<div class="col-xs-12 col-md-6">
			<?php else: ?>
				<div class="col-xs-12">
			<?php endif; ?>
				<?php $this->load->view('user/season/scores/display_team_min',
						array('team' => $g['away'])); ?>
			</div>

		<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		setInterval(function(){get_stats();}, 10000);
		get_stats();
	});

	function get_stats(){

		$.ajax({
			url: "<?=site_url('season/scores/get_live_json')?>",
			type: "GET",
			dataType: "json",
			success: function(alldata){
				console.log(alldata);
				$.each(alldata.players, function(player_id, points){

					var fadetime = 400;
					e = ('.points_'+player_id).trim();

					if (points != null)
					{

						if (player_id == 5714)
						{
							console.log($(e).first().text().trim());
							console.log(points.toString());
						}
						if ($(e).first().text().trim() != points.toString())
						{

							updateScore(e,points.toString());
						}
					}
				});
				$.each(alldata.teams, function(team_id, points){
					e = ('.teampoints_'+team_id).trim();
					if(points != null)
					{
						if ($(e).first().text() != points.toString())
						{
							updateScore(e,points.toString());
						}

					}
				});
				$.each(alldata.live_games, function(gsis_id, data){

					if (data.gametime.charAt(0) != 'f')
					{

						text = "";
						// Make points green if game is live
						e = ('.active_'+data.off_club_id).trim();
						console.log(e);
						$(e).addClass('active-points');

						e = ('.active_'+data.def_club_id).trim();
						$(e).addClass('active-points');


						if (data.note == "XP")
						{
							text = data.off_club_id+ " XP Good";
						}

						if (data.note == "FG")
						{
							text = data.off_club_id+" FG Good";
						}

						if (data.note == "FGM")
						{
							text = data.off_club_id+" FG No Good";
						}

						if (data.note == "KICKOFF")
						{
							text = data.def_club_id+" Kickoff";
						}
						if (data.note == "PUNT")
						{

							text = data.off_club_id+" Punting";
						}
						if (data.note == "PENALTY")
						{
							text = "Penalty";
						}
						if (data.note == "TD")
						{
							text = data.off_club_id+" Touchdown";
						}
						if (data.note == "2PPF")
						{
							text = data.off_club_id+" 2pt try failed";
						}
						if (text != "")
						{
							updateStatusItem('.off_'+data.off_club_id, text, data.details, 'ls-active');
							updateStatusItem('.off_'+data.def_club_id, text, text, 'ls-inactive');
							updateStatusItem('.def_'+data.def_club_id, text, data.details,'ls-active');
							updateStatusItem('.def_'+data.off_club_id, text, text,'ls-inactive');
						}
						else
						{
							// Offensive player - team has ball
							//text = "<i>"+data.quarter+" "+data.time+"</i> "+data.down+' & '+data.to_go+' '+data.yard_line;

							e = ('.off_'+data.off_club_id).trim();
							updateStatusItem(e, data.status, data.details, 'ls-active');

							// Offensive player - team on defense.
							e = ('.off_'+data.def_club_id).trim();
							text = data.status;
							text = data.gametime+" "+data.def_club_id+" on defense.";
							//text = data.def_club_id+' on defense.';
							updateStatusItem(e, text, text, 'ls-inactive');

							// Defensive player - team on defense
							e = ('.def_'+data.def_club_id).trim();
							//data.quarter+' '+data.time+' '+
							text = data.status;
							updateStatusItem(e, data.status, data.details, 'ls-active');

							// Defensive player - team on offense.
							e = ('.def_'+data.off_club_id).trim();

							text = data.status;
							text = data.off_club_id+' on offense.';
							updateStatusItem(e, text, 'ls-inactive');

							//e = ('.time_'+gsis_id).trim();
							//text = data.gametime;
							//$(e).addClass('clock');
							//$(e).html(text);
							//updateStatusItem(e, text, 'time');
						}
					}
					else if(data.gametime.charAt(0) == 'H')
					{
						text = alldata.gamescores.off_club_id;

						updateStatusItem('.off_'+data.off_club_id, text, text, 'ls-inactive');
						updateStatusItem('.off_'+data.def_club_id, text, text, 'ls-inactive');
						updateStatusItem('.def_'+data.def_club_id, text, text,'ls-inactive');
						updateStatusItem('.def_'+data.off_club_id, text, text,'ls-inactive');
					}
					else
					{
						text = data.match;
						setFinal(('.off_'+data.off_club_id).trim(), text);
						setFinal(('.off_'+data.def_club_id).trim(), text);
						setFinal(('.def_'+data.def_club_id).trim(), text);
						setFinal(('.def_'+data.off_club_id).trim(), text);

						// Clear clock element
						e = ('.status_'+gsis_id).trim();
						text = "Final";
						$(e).addClass('clock');
						$(e).html(text);

						// remove green points
						e = ('.active_'+data.off_club_id).trim();
						$(e).removeClass('active-points');

						e = ('.active_'+data.def_club_id).trim();
						$(e).removeClass('active-points');
					}

				});
			}
		});
	}

	function setFinal(element, text)
	{
		if ($(element).first().text() != text)
		{
			$(element).removeClass('ls-active');
			$(element).text(text);
		}
	}

	function updateStatusItem(element, status, details, cssclass)
	{
		fadetime = 400;

		if ($(element).first().text().trim() != status.trim())
		{

			if (element == '.off_BAL')
			{
				//console.log("element:"+$(element).text());
				//console.log(" status:"+status);
			}

			if (status != details)
			{
				$(element).animate({opacity:0}, fadetime/4);
				setTimeout(function()
				{
					$(element).text(details);
				},fadetime/4);
				$(element).animate({opacity:1}, fadetime);

				setTimeout(function()
				{
					$(element).animate({opacity:0}, fadetime/4);
					setTimeout(function(){

						$(element).first().text(status);

					},fadetime/4);
					$(element).animate({opacity:1}, fadetime);
				},6000);
			}
			else{$(element).text(status);}

			if (cssclass == 'ls-active'){
				$(element).addClass('ls-active');
				$(element).removeClass('ls-inactive');
				$(element).removeClass('nonlive-score');
			}
			if (cssclass == 'ls-inactive'){
				$(element).addClass('ls-inactive');
				$(element).removeClass('ls-active');
				$(element).removeClass('nonlive-score');
			}
		}
		else
		{
			if ($(element).text() != "")
			{
				//console.log(element);
				//console.log('SAME element:'+$(element).text());
				//console.log(' SAME status:'+status);
			}
		}

	}

	function updateScore(element, points)
	{
		console.log(element);
		fadetime = 400;
		$(element).animate({opacity: 0}, fadetime/4);

		setTimeout(function()
		{

			if ((parseInt(points) - parseInt($(element).first().text()) >= 0))
				{$(element).css('color','#00E600');}
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
