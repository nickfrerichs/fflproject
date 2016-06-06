<div class="row">
	<div class="columns">
		<h5>Settings</h5>
		<?php //print_r($settings); ?>
		<table>
			<thead>
			</thead>
			<tbody>
				<tr>
					<td><strong>Site name</strong></td>
					<td id="sitename-field" class="text-center">
                    <?php if($settings->name != ""):?>
                        <?=$settings->name?>
                    <?php else: ?>
                        (not set)
                    <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="#" id="sitename-control" class="change-control" data-url="<?=site_url('admin/site/ajax_change_item')?>"
                            data-type="text">Change</a>
                        <a href="#" id="sitename-cancel" class="cancel-control"></a>
                    </td>
				</tr>
			</tbody>
		</table>
		<br>
		<h5>Debugging Options</h5>
		<table>

			<tbody>
				<tr>
					<td colspan=2><span data-tooltip class="has-tip top" title="Show debug info site-wide for all users.">Debug On</span></td>
					<td class="text-center">
						<div class="switch small">
                        <input  class="switch-input toggle-control" data-item="debug_user" data-url="<?=site_url('admin/site/ajax_toggle_site_setting')?>"
                            id="debug_user" type="checkbox" <?php if($settings->debug_user){echo "checked";}?>>
                        <label class="switch-paddle" for="debug_user">
                        </label>
                    	</div>
                	</td>
				</tr>
				<tr>
					<td colspan=2><span data-tooltip class="has-tip top" title="Show debug info site-wide for Site Admins only.">Debug On, Admins-only</span></td>
					<td class="text-center">
						<div class="switch small">
                        <input  class="switch-input toggle-control" data-item="debug_admin" data-url="<?=site_url('admin/site/ajax_toggle_site_setting')?>"
                            id="debug_admin" type="checkbox" <?php if($settings->debug_admin){echo "checked";}?>>
                        <label class="switch-paddle" for="debug_admin">
                        </label>
                    	</div>
                	</td>
				</tr>
				<th colspan=3><small>Set year, week, and week type are for testing only.  Should not be used on production sites.</small></th>
				<tr>
					<td><span data-tooltip class="has-tip top" title="Administratively set the current year.">Set Year</span></td>
					<td>
						<select id="change-year" disabled>
							<option value="-1">Off</option>
							<?php for($i = $current_year; $i >= $current_year-10; $i--): ?>
								<option value=<?=$i?>
									<?php if($settings->debug_year == $i){echo "selected";}?>
									><?=$i?></option>
							<?php endfor;?>
						</select>
					</td>
					<td class="text-center">
						<a class="change" data-for="change-year">Change</a>
                    </td>
				</tr>
				<tr>
					<td><span data-tooltip class="has-tip top" title="Administratively set the current week.">Set Week</span></td>
					<td>
						<select id="change-week" disabled>
							<option value="-1">Off</option>
							<?php for($i = 0; $i <= 17; $i++): ?>
								<option value=<?=$i?>
									<?php if($settings->debug_week == $i){echo "selected";}?>
									><?=$i?></option>
							<?php endfor;?>
						</select>
					</td>
					<td class="text-center">
						<a class="change" data-for="change-week">Change</a>
                    </td>
				</tr>
				<tr>
					<td><span data-tooltip class="has-tip top" title="Administratively set the current week type.">Set Week Type</span></td>
					<td>
						<select id="change-weektype" disabled>
							<option value="-1">Off</option>
							<?php foreach($week_types as $id => $text_id): ?>
								<option value=<?=$id?>
									<?php if($settings->debug_week_type_id == $id){echo "selected";}?>
									><?=$text_id?></option>
							<?php endforeach;?>
						</select>
					</td>
					<td class="text-center">
						<a class="change" data-for="change-weektype">Change</a>
                    </td>
				</tr>


			</tbody>
		</table>
	</div>
</div>

<script>
$('.change').on('click',function(){
	var ele = $(this).data('for');
	if ($(this).text() == "Change")
	{
		$('#'+ele).prop("disabled",false);
		$(this).text("Cancel");
	}
	else if($(this).text() == "Cancel")
	{
		$('#'+ele).prop("disabled",true);
		$(this).text("Change");
	}
});

$('#change-week, #change-year, #change-weektype').on('change', function(){
	var type = $(this).attr('id').replace('change-',"");
	var url = "<?=site_url('admin/site/ajax_change_item')?>";
	var value = $(this).val();

	$.post(url,{'type':type,'value':value},function(data){
		var d = $.parseJSON(data);
		if(d.success)
		{
			location.reload();
		}
	});
});

</script>
