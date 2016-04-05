<div class="container">
	<div id="footer-bar" class="row">
		<div style="float:right; min-height:20px; padding-right:20px;">
			<?php if (count($this->session->userdata('leagues')) > 1): ?>
				<?php $this->load->view('template/modals/change_league'); ?>
			<?php endif;?>
			<?php if ($this->session->userdata('user_id') == 1 || $this->session->userdata('user_id') == 12): ?>
				<a href="#" id="change-league-link">Change League</a>
			<?php endif; ?>
			<?php $isadmin = ($this->session->userdata('league_admin') || $this->flexi_auth->is_admin()); ?>
			<?php if ($isadmin && stripos($v,'admin') === false) {echo ' <a href = '.site_url().'admin>Admin </a>';} ?>
			<?php if ($isadmin && stripos($v,'admin') > -1) {echo ' <a href = '.site_url().'>User </a>';} ?>

		</div>

	</div>


	<div style="height:50px"></div>
	<div id="footer-nav" class="visible-xs btn-group btn-group-justified">

	</div>
</div>
<div id="is-xs" class="visible-xs"></div>
<script>
$(document).ready(function(){

	//$(".footer-nav-item").not('.footer-nav-item:first').addClass('hidden-xs');

	// For reach defined item in the DOM, create a footer button.
	$(".footer-nav-item").each(function(){
		var name = $(this).data("nav-name").replace("_"," ");
		//var item = "<button class='btn btn-default btn-footer' data-nav-name='"+$(this).data("nav-name")+"'>"+name+"</button>";
		var item = "<div class='btn-group'><a href='#'><button type='button' class='btn btn-footer sm-simple' data-nav-name='"+$(this).data("nav-name")+"'>"+name+"</button></a></div>";
		//var item = "<div class='btn-footer' data-nav-name='"+$(this).data("nav-name")+"'>"+name+"</div>";
		$("#footer-nav").append(item);
	});


	$(".btn-footer").on("click",function(){
		$("div.footer-nav-item:not([data-nav-name='"+$(this).data("nav-name")+"'])").addClass('hidden-xs');
		$("div[data-nav-name='"+$(this).data("nav-name")+"']").removeClass('hidden-xs');
		$(this).addClass("active");
		$(".btn-footer").not($(this)).removeClass("active");
		event.preventDefault();
	});

	$(".btn-footer").first().click();
	//$(".btn-footer").first().addClass("active");

});
</script>
