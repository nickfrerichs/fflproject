<?php //print_r($open_trades); ?>
<?php $this->load->view('template/modals/stat_popup'); ?>
<div class="container">

	<div><h3>Trades</h3></div>



	<div><h4><a href="<?=site_url('myteam/trade/propose')?>"><button class="btn btn-default">Propose Trade</button></a></h4></div>

	<!--
	<table class="table"><tbody><tr><td>
	<div class="text-center"><h4>No Current Offers</h4></div>
	</td></tr></tbody></table>
-->


	<div class="text-center">
	<h4>Outstanding Trades</h4>
	</div>
	<table class="table table-striped">
		<thead>
			<th>Offer</th><th>Request</th><th>Expires</th><th>Status</th>
		</thead>
		<tbody id="open-trades-tbody">

	</tbody>
	</table>



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
			if (data != "success")
			{showMessage(data);}
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
