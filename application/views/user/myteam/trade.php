<?php //$this->load->view('components/stat_popup'); ?>

<div class="section">
	<div class="container">
		<div class="title">Trades</div>
		<br>
		<div class="has-text-left">

			<?php if(!$this->session->userdata('offseason')): ?>
				<span><a href="<?=site_url('myteam/trade/propose')?>">Propose a Trade</a></span> |
			<?php endif;?>
			<span><a href="<?=site_url('myteam/trade/log')?>">Trade log</a></span>

		</div>


		<!--
		<table class="table"><tbody><tr><td>
		<div class="text-center"><h4>No Current Offers</h4></div>
		</td></tr></tbody></table>
		-->

		<?php if($this->session->userdata('offseason')): ?>
			<?php $this->load->view('user/offseason'); ?>
		<?php else:?>
			<br>
			<div class="title is-size-5">Outstanding Trades</div>

			<div class="f-scrollbar">
				<table class="table is-fullwidth is-striped is-size-7-mobile f-min-width-medium">
					<thead>
						<th>Offer</th><th>Request</th><th>Expires</th><th>Status</th>
					</thead>
					<tbody id="open-trades-tbody">
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>


<script>
$(document).ready(function(){
	load_open_trades();
});

	$("#open-trades-tbody").on('click','.accept-button',function(){
		var tradeid = $(this).val();
		console.log(tradeid);
		var url="<?=site_url('myteam/trade/ajax_accept')?>";
		$.post(url,{'tradeid':tradeid}, function(data){
			console.log(data)
			result = $.parseJSON(data);
			console.log(result);
			if (result.success != true)
			{notice(result.msg);}
			else
			{notice(result.msg,'success');}
			load_open_trades();
        });
	});

	$("#open-trades-tbody").on('click','.decline-button',function(){
		var tradeid = $(this).val();
		console.log(tradeid);
		var url="<?=site_url('myteam/trade/ajax_decline')?>";
		$.post(url,{'tradeid':tradeid}, function(data){
			result = $.parseJSON(data);
			if (result.success != true)
			{notice(result.msg);}
			else {
				notice(result.msg,"success");
			}
			load_open_trades();
		});
	});

	function load_open_trades()
	{
		var url="<?=site_url('myteam/trade/load_open_trades')?>";
		$.post(url,{}, function(data){
			$("#open-trades-tbody").html(data);

        });
	}
</script>
