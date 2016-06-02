<div class="row">
	<div class="columns">
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

			</tbody>
		</table>
	</div>
</div>
