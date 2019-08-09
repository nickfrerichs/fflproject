<div class="section">
	<div class="container">

			<div class="is-size-5">Admin Area</div>
			<br>
			<?php if(!$has_leagues): ?>
			<div class="content">
				To get started:
				<ol>
					<li>
						Make sure that you have <b>nflgame</b> intalled, check the wiki on GitHub if you need help.
					</li>
					<?php if($nfl_schedule_status): ?>
						<li>
							Current NFL schedule in your database is: <b><?=$nfl_schedule_status->year?>, <?=$nfl_schedule_status->gt?></b><br>
							If this is not current, run <pre>update.py -schedule</pre>
						</li>
					<?php else: ?>
						<li>No NFL schedule in database<br>Run update.py -schedule</li>
					<?php endif;?> 
					<li><a href="<?=site_url('admin/site/create_league')?>">Create a League.</a></li>
					<li>Use the invite url to sign up users.</li>
					<li>Assign a league admin.</li>
				</ol>
			</div>
			<?php endif;?>

	</div>
</div>