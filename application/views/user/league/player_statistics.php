<?php //print_r($years); ?>
<?php $this->load->view('template/modals/stat_popup'); ?>
<?php $this->load->view('template/modals/player_news_popup'); ?>
<div class="row callout">
	<div class="columns">
		<div class="row">
			<div class="columns">
				<h5>All Players</h5>
			</div>
		</div>

		<div class="row">
			<div class="columns medium-4 large-3 small-12">
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
						<th>Wk <?=$this->session->userdata('current_week')?> Opp.</th>
						<th>Bye</th>
						<th><a href="#" data-order="desc" data-for="main-list" data-by="points" class="player-list-a-sort">Points</a></th>
						<th>Team</th>
						<?php if($this->session->userdata('use_draft_ranks')):?>
						<th><a href="#" data-order="asc" data-for="main-list" data-by="draft_rank" class="player-list-a-sort">Rank</a></th>
						<?php endif; ?>
					</thead>
					<tbody id="main-list" data-by="points" data-order="desc" data-url="<?=site_url('player_search/ajax_full_player_list')?>">

					</tbody>
				</table>
				<div class="player-list-total" data-for="main-list"></div>
				<div>
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
		</div>
	</div>
</div>
<script>

$(updatePlayerList("main-list"));

</script>
