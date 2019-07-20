
	<div class="section">
		<div class="container">
			<div class="tabs is-small is-boxed fflp-tabs-active">
				<ul>
					<li class="is-active" data-for="career-tab" data-load-content="career-list"><a>Career</a></li>
					<li class="" data-for="best-week-tab" data-load-content="best-week-list"><a>Best Week</a></li>
				</ul>
			</div>
			
			<!-- Avg Week -->
			<div id="best-week-tab" class="is-hidden stat-div">

				<?php

				$headers = array();
				$headers['Rank'] = array();
				$headers['Name'] = array();
				$headers['Position'] = array();		
				$headers['Points'] = array();	
				$headers['Weak'] = array();
				$headers['Year'] = array();
				$headers['Owner'] = array();


				$dropdowns['pos']['options']['All'] = 0;
				$dropdowns['pos']['label'] = "Position";
				foreach($positions as $p)
					$dropdowns['pos']['options'][$p->text_id] = $p->id;

				$dropdowns['year']['options']['All'] = 0;
				$dropdowns['year']['label'] = "Year";
				foreach($years as $y)
					$dropdowns['year']['options'][$y->year] = $y->year;

				$this->load->view('components/player_search_table',
								array('id' => 'best-week-list',
									'url' => site_url('load_content/history_player_best_week_list'),
									'order' => 'desc',
									'by' => 'points',
									'dropdowns' => $dropdowns,
									'headers' => $headers,
									'disable_search' => True));


				?>
			</div>

			<!-- Career -->
			<div id="career-tab" class="stat-div">

				<?php 

				$headers = array();
				$headers['Rank'] = array();
				$headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
				$headers['Position'] = array('by' => 'position', 'order' => 'asc');		
				$headers['Avg. Points'] = array('by' => 'avg_points', 'order' => 'desc');	
				$headers['Total Points'] = array('by' => 'total_points', 'order' => 'desc');
				$headers['Games'] = array();
				

				$dropdowns['pos']['options']['All'] = 0;
				$dropdowns['pos']['label'] = "Position";
				foreach($positions as $p)
					$dropdowns['pos']['options'][$p->text_id] = $p->id;

				$dropdowns['year']['options']['All'] = 0;
				$dropdowns['year']['label'] = "Year";
				foreach($years as $y)
					$dropdowns['year']['options'][$y->year] = $y->year;

				$this->load->view('components/player_search_table',
								array('id' => 'career-list',
									'url' => site_url('load_content/history_player_career_list'),
									'order' => 'desc',
									'by' => 'avg_points',
									'dropdowns' => $dropdowns,
									'headers' => $headers,
									'disable_search' => True));


				?>
			</div>
		</div>
	</div>


<script>

loadContent('career-list');

</script>
