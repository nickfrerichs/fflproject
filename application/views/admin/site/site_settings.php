<!-- <div class="section">
    <div class="columns is-centered" >
        <div class="column"> -->
<div class="section">
	<div class="container">
		<div class="title">Settings</div>
        <div class="is-divider"></div>
		<div class="title is-size-5">General</div>
		<div class="columns">
			<div class="column is-one-third">
				Site Name
			</div>
			<div class="column">
				<?php $this->load->view('components/editable_text',array('id' => 'sitename', 
																				'value' => $settings->name,
																				'url' => site_url('admin/site/ajax_change_item')));?>
			</div>
		</div>

		<hr>
		<div class="title is-size-5">Debugging Options</div>
		<div class="columns">
			<div class="column is-one-third">
				Debug On
			</div>
			<div class="column">
				<?php $this->load->view('components/toggle_switch',
										array('id' => 'debug-user',
												'url' => site_url('admin/site/ajax_toggle_site_setting'),
												'var1' => 'debug_user',
												'is_checked' => $settings->debug_user));
						?>
			</div>
		</div>

		<div class="columns">
			<div class="column is-one-third">
				Debug On, Admins-only
			</div>
			<div class="column">
				<?php $this->load->view('components/toggle_switch',
										array('id' => 'debug-admin',
												'url' => site_url('admin/site/ajax_toggle_site_setting'),
												'var1' => 'debug_admin',
												'is_checked' => $settings->debug_admin));
						?>
			</div>
		</div>
		
		<div class="section">
		<i>These should not be used on production sites.</i>
			<div class="box has-background-grey-lighter">
				<div class="columns">
					<div class="column is-one-third">
						Set Year
					</div>
					<div class="column">
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
					</div>
				</div>

				<div class="columns">
					<div class="column is-one-third">
						Set Week
					</div>
					<div class="column">
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
					</div>
				</div>

				<div class="columns">
					<div class="column is-one-third">
						Set Week Type
					</div>
					<div class="column">
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
					</div>
				</div>


			</div>
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
		</div>
        <div class="is-divider"></div>


		<div class="title">Version Information</div>
		<div class="columns is-mobile">
			<div class="column is-one-third-tablet">
				PHP
			</div>
			<div class="column">
				<?=phpversion()?>
			</div>
		</div>

		<div class="columns is-mobile">
			<div class="column is-one-third-tablet">
				Codeignighter
			</div>
			<div class="column">
				<?=CI_VERSION?>
			</div>
		</div>

		<div class="columns is-mobile">
			<div class="column is-one-third-tablet">
				FFL Project database
			</div>
			<div class="column">
				<?=$settings->db_version?>
			</div>
		</div>
        <div class="is-divider"></div>

	</div>

</div>

<script>
$('#toggle-error-reporting').on('click',function(e){
	e.preventDefault();
	$('#error-reporting').toggleClass('is-hidden');
});

</script>
