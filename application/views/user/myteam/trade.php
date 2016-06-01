<?php //print_r($open_trades); ?>
<?php $this->load->view('template/modals/stat_popup'); ?>

<div class="row">
	<div class="column"><h4>Trades</h4></div>
</div>


<div class="row align-justify">
	<?php if(!$this->session->userdata('offseason')): ?>
		<div class="column small-12"><a href="<?=site_url('myteam/trade/propose')?>">Propose a Trade</a></div>
	<?php endif;?>
	<div class="column small-12"><a href="#">Trade log</a></div>
</div>

<!--
<table class="table"><tbody><tr><td>
<div class="text-center"><h4>No Current Offers</h4></div>
</td></tr></tbody></table>
-->

<?php if($this->session->userdata('offseason')): ?>
	<div class="row">
	<div class="column">
		<h5 class="text-center">It's the offseason.</h5>
	</div>
</div>
<?php else:?>
<div class="row">
	<div class="column text-center">
		<h5>Outstanding Trades</h5>
	</div>
</div>
<div class="row">
	<div class="column">
		<table class="table table-striped">
			<thead>
				<th>Offer</th><th>Request</th><th>Expires</th><th>Status</th>
			</thead>
			<tbody id="open-trades-tbody">
		</tbody>
		</table>
	</div>
</div>
<?php endif; ?>



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
			{notice(data.msg);}
			else {
				notice("Trade declined.","success");
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
