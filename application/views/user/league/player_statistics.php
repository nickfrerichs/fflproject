<?php //print_r($years); ?>
<?php $this->load->view('components/stat_popup'); ?>
<?php //$this->load->view('template/modals/player_news_popup'); ?>
<div class="section">
	<div class="container">
		<div class="title">Player Statistics</div>
		<?php //Show the player list using player_search_table component

		$headers['Name'] = array('by' => 'last_name', 'order' => 'asc');
		$headers['Position'] = array('by' => 'position', 'order' => 'asc');
		$headers['NFL Team'] = array('by' => 'club_id', 'order' => 'asc');
		$headers['Wk '.$this->session->userdata('current_week').' Opp.'] = array('classes' => array('hide-for-small-only'));
		$headers['Bye'] = array();
		$headers['Points'] = array('by' => 'points', 'order' => 'asc');
		$headers['Team'] = array();
		if ($this->session->userdata('use_draft_ranks'))
			$headers['Draft Rank'] = array('by' => 'draft_rank', 'order' => 'asc');

		$pos_dropdown['All'] = 0;
		foreach($positions as $p)
			$pos_dropdown[$p->text_id] = $p->id;
		$this->load->view('components/player_search_table',
						array('id' => 'full-player-list',
							'url' => site_url('load_content/ajax_full_player_list'),
							'order' => 'desc',
							'by' => 'points',
							'pos_dropdown' => $pos_dropdown,
							'headers' => $headers));


		?>
	</div>
</div>

<script>
$(loadContent('full-player-list'));
//$(updatePlayerList("main-list"));

</script>
