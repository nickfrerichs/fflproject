<!-- <div class="section">
    <div class="columns is-centered" >
        <div class="column"> -->
<div class="section">
    <div class="columns is-centered">
        <div class="column">
			<div class="container fflp-lg-container">
				<div class="is-size-5">Settings</div>
				<?php //print_r($settings); ?>
				<table class="table is-fullwidth fflp-table-fixed">
					<thead>
					</thead>
					<tbody>
						<tr>
							<td><strong>Site name</strong></td>
							<td id="sitename-field" class="text-center" colspan=2>


								<?php $this->load->view('components/editable_text',array('id' => 'sitename', 
                                                                                        'value' => $settings->name,
                                                                                        'url' => site_url('admin/site/ajax_change_item')));?>

							</td>
						</tr>
					</tbody>
				</table>
				<div class="is-size-5">Debugging Options</div>
				<table class="table is-fullwidth fflp-table-fixed">
					<tbody>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Show debug info site-wide for all users.">Debug On</span></td>
							<td colspan=2>
								<?php $this->load->view('components/toggle_switch',
                                                array('id' => 'debug-user',
													  'url' => site_url('admin/site/ajax_toggle_site_setting'),
													  'var1' => 'debug_user',
                                                      'is_checked' => $settings->debug_user));
                        		?>
							</td>
						</tr>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Show debug info site-wide for Site Admins only.">Debug On, Admins-only</span></td>
							<td colspan=2>
								<?php $this->load->view('components/toggle_switch',
                                                array('id' => 'debug-admin',
													  'url' => site_url('admin/site/ajax_toggle_site_setting'),
													  'var1' => 'debug_admin',
                                                      'is_checked' => $settings->debug_admin));
                        		?>
							</td>
						</tr>
						<th colspan=3><small>Set year, week, and week type are for testing only.  Should not be used on production sites.</small></th>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Administratively set the current year.">Set Year</span></td>
							<td colspan=2>
								<?php 
									// Inputs: $id, $value, $blank_value, $url, $options, $selected_val
									$options = array('Off' => '-1');
									for($i = $current_year; $i >= $current_year-10; $i--){$options[$i] = $i;}
									$this->load->view('components/editable_select',
												array('id' => 'debug-year',
													  'options' => $options,
													  'url' => site_url('admin/site/ajax_change_item'),
													  'selected_val' => $settings->debug_year));
								?>
							</td>
						</tr>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Administratively set the current week.">Set Week</span></td>
							<td colspan=2>
								<?php 
									// Inputs: $id, $value, $blank_value, $url, $options, $selected_val
									$options = array('Off' => '-1');
									for($i = 0; $i <= 17; $i++){$options[$i] = $i;}
									$this->load->view('components/editable_select',
												array('id' => 'debug-week',
													  'options' => $options,
													  'url' => site_url('admin/site/ajax_change_item'),
													  'selected_val' => $settings->debug_week));
								?>
							</td>
						</tr>
						<tr>
							<td><span data-tooltip class="has-tip top" title="Administratively set the current week type.">Set Week Type</span></td>
							<td colspan=2>
							<?php 
									// Inputs: $id, $value, $blank_value, $url, $options, $selected_val
									$options = array('Off' => '-1');
									foreach($week_types as $id => $text_id){$options[$text_id] = $id;}
									$this->load->view('components/editable_select',
												array('id' => 'debug-weektype',
													  'options' => $options,
													  'url' => site_url('admin/site/ajax_change_item'),
													  'selected_val' => $settings->debug_week_type_id));
								?>
							</td>
						</tr>
						<tr>
							<td colspan=3>
								<div class="content">
									<a href="#" id="toggle-error-reporting">How do I enable PHP error reporting?</a>
									<div id="error-reporting" class="is-hidden">
										<ul>
											<li>Create a file called fflp_ci_env.php in the document root.</li>
											<li>Add the following content to the file:
												<pre>&lt;?php $fflp_ci_env = 'development'; ?&gt;</pre>
												</li>

										</ul>
									</div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
				<div class="is-size-5">Version Information</div>
				<table class="table is-fullwidth fflp-table-fixed">
					<tr>
						<td>PHP</td><td colspan=2> <?=phpversion()?></td>
					</tr>
					<tr>
						<td>Codeigniter</td><td colspan=2> <?=CI_VERSION?></td>
					</tr>
					<tr>
						<td>FFL Project database</td><td colspan=2> <?=$settings->db_version?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
$('#toggle-error-reporting').on('click',function(e){
	e.preventDefault();
	$('#error-reporting').toggleClass('is-hidden');
});

</script>
