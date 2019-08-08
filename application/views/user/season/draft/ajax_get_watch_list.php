<?php if(count($players) == 0):?>
	<tr><td colspan="4" class="has-text-centered" style="padding-top:160px; padding-bottom:160px;">You aren't watching any players</td></tr>
<?php else: ?>
<?php foreach ($players as $key => $p): ?>
	<tr class="watch-avail-<?=$p->id?>">
		<td class="has-text-centered">

					<?=$p->rank?>


			<!-- <div>
				<?php //if($key != 0 || ($this->in_page!=""&&$this->in_page!=0)): ?>
				<a href="#" class="btn-draft" data-value="up_<?=$p->id?>"><i class="fi-arrow-up" style="font-size:1.2em"></i></a>
				<?php //else: ?>
					<span class=""></span>
				<?php //endif;?>
			</div>

			<div>
				<?php //if(count($players) != $key+1 || $this->in_page != floor($total_players/$this->per_page)):?>
				<a href="#" class="btn-draft" data-value="down_<?=$p->id?>"><i class="fi-arrow-down" style="font-size:1.2em"></i></a>
				<?php //else: ?>
					<span class=""></span>
				<?php //endif;?>
			</div> -->
		</td>

		<td>
			<div style="display:inline-block">
				<?php if($key != 0 || ($p->order!=1)): ?>
				<a href="#" class="btn-draft" data-value="up_<?=$p->id?>">
					<span class="icon has-text-link">
						<i class="fa fa-angle-double-up is-size-5"></i>
					</span>
				</a>
				<?php else: ?>
				<span class="icon has-text-grey-lighter">
						<i class="fa fa-angle-double-up is-size-5"></i>
					</span>
				<?php endif;?>
				<br>
				<?php if(count($players) != $key+1 || $p->order != $total_players):?>
				<a href="#" class="btn-draft" data-value="down_<?=$p->id?>">
				<span class="icon has-text-link">
					<i class="fa fa-angle-double-down is-size-5"></i>
				</span>
				</a>
				<?php else: ?>
				<span class="icon has-text-grey-lighter">
					<i class="fa fa-angle-double-down is-size-5"></i>
				</span>
				<?php endif;?>
			</div>
			<div style="display:inline-block">
				<div>
				<span class="has-text-right is-size-7"><?=$p->order?>. </span><strong><span class="selected-player-name"><?=$p->first_name.' '.$p->last_name?></span></strong>
				</div>
				<div>
					<?=$p->club_id?> - <?=$p->position?> <span style="font-size: .8em">(bye <?=$byeweeks[$p->club_id]?>)</span>
				</div>
			</div>
		</td>
		<td>

<?php //print_r($players);?>



	<button class="button is-small is-link btn-draft" data-value="watch_<?=$p->id?>">Unwatch</button>
	<?php if($draft_team_id == $team_id && !$paused): ?>
	<button class="button is-small is-link btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>">Draft</button>
	<?php else: ?>
	<button class="button is-small is-link btn-draft" value="draft_<?=$p->id?>" data-value="draft_<?=$p->id?>" disabled>Draft</button>
	<?php endif; ?>

</td>

	</tr>

<?php endforeach; ?>

<?php endif;?>

<script>
//$(".reload-foundation").foundation();


// $('.jbox-draft-context').on('click',function(){

// 	new jBox('Tooltip',{
// 		title: "My title",
// 		content: "My content",
// 		attach: 
// 	});


// });


$('.jbox-draft-context').jBox('Tooltip',{
	theme: 'TooltipDark',
	trigger: 'click touchclick',
	// position: {x: 'right',y: 'bottom'},
	content: 'My Content',
	closeOnMouseleave: true
});


</script>
