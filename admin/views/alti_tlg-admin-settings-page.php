<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$plugin = new Alti_tlg_Admin( $this->plugin_real_name, $this->plugin_name, $this->version );
$plugin->check_manage_links();
$plugin->check_settings();
$plugin->enqueue_notices();

?>
<div class="wrap" id="alti_tlg">
	<h1><span class="dashicons dashicons-controls-repeat"></span> Tracking link Generator <i class="alti_tlg_by">by alticreation</i> </h1>

	<ul class="alti_tlg_tabs">
		<li><a href="#alti_tlg_tab-1" class="active"><span class="dashicons dashicons-dashboard"></span> Manage</a></li>
		<li><a href="#alti_tlg_tab-2"><span class="dashicons dashicons-admin-settings"></span> Settings</a></li>
		<li><a href="#alti_tlg_tab-3"><span class="dashicons dashicons-welcome-learn-more"></span> Help</a></li>
	</ul>
	<div class="alti_tlg_tabs_container">
		
	
	<div class="alti_tlg_container alti_tlg_tab alti_tlg_tab-1 active">
	<h3><span class="dashicons dashicons-plus-alt"></span> Create a tracking link</h3>
		<form method="POST">
		<table class="form-table">
			<tr>
				<th scope="row"><label for="campaign_page">Page to link</label></th>
				<td><input name="campaign_page" type="text" id="campaign_page" value="<?php echo $plugin->campaign_page; ?>" class="regular-text">
					<p class="description">example: <?php echo home_url(); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="campaign_source">Campaign Source</label></th>
				<td>
					<select name="campaign_source">
						<?php 
						$sources = $plugin->get_sources();
						foreach ($sources as $source) {
							$attributes = "";
							if( $source->source_name == $plugin->campaign_source ) $attributes = " selected='selected' ";
						?>
							<option value="<?php echo $source->source_name; ?>" <?php echo $attributes; ?>><?php echo $source->source_name; ?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="campaign_medium">Campaign Medium</label></th>
				<td>
					<select name="campaign_medium">
						<?php 
						$mediums = $plugin->get_mediums();
						foreach ($mediums as $medium) {
							$attributes = "";
							if( $medium->medium_name == $plugin->campaign_medium ) $attributes = " selected='selected' ";
							?>
							<option value="<?php echo $medium->medium_name; ?>" <?php echo $attributes; ?>><?php echo $medium->medium_name; ?></option>
						<?php
						}
						 ?>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="campaign_name">Campaign Name</label></th>
				<td>
				<input name="campaign_name" type="text" id="campaign_name" value="<?php echo $plugin->campaign_name; ?>" class="regular-text">
				<p class="description">The Campaign Name will be formated once submitted.</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="custom_key">Additional Parameters <div class="description">(optional)</div></label></th>
				<td>
				<input name="custom_key" placeholder="Custom Key" type="text" id="custom_key" value="<?php echo $plugin->custom_key; ?>" class="regular-text">
				<input name="custom_value" placeholder="Custom Value" type="text" id="custom_value" value="<?php echo $plugin->custom_value; ?>" class="regular-text">
				<p class="description">It will generate a custom pair "key" and "value".</p>
				</td>
			</tr>

			</table>
			<p class="submit"><input type="submit" name="submit_manage_links" id="submit" class="button button-primary" value="Generate Link"></p>
		</form>
		
		<hr>
		<h3><span class="dashicons dashicons-menu"></span> Existing generated links</h3>
		<div class="alti_tlg_result">
		<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<td>Campaign Name</td>
					<td>Short Link</td>
					<td>Full Link</td>
					<td>Creator</td>
				</tr>
			</thead>
			<tbody>
			<?php 
$links = $plugin->get_links();

foreach ( $links as $link ) 
{
	$class = '';
	if( !empty($_POST['submit_manage_links']) ) {
		$full_link = $plugin->get_full_link( $_POST['campaign_page'], $_POST['campaign_source'], $_POST['campaign_medium'], $_POST['campaign_name'], $_POST['custom_key'], $_POST['custom_value'] );
		if( !empty($_POST['submit_manage_links']) && !empty( $full_link ) && $full_link == $link->campaign_full_link ) $class = 'alti_tlg_highlight';
	}
?>
<tr class="<?php echo $class; ?>">
<td class="campaign_name"><strong><?php echo $link->campaign_name; ?></strong></td>
<td class="campaign_short_link" data-copy="true"><?php echo $link->campaign_short_link; ?></td>
<td class="campaign_full_link" data-copy="true"><?php echo $link->campaign_full_link; ?></td>
<td class="user_id"><?php echo get_userdata($link->user_id)->user_login; ?></td>
</tr>
<?php
}
			 ?>
			 </tbody>
		</table>
		</div>

	</div>

	<div class="alti_tlg_container alti_tlg_tab alti_tlg_tab-2">
		<form method="POST">
		<h2 class="title"><span class="dashicons dashicons-plus-alt"></span> Add</h2>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="new_campaign_source">Add new Campaign Source</label></th>
				<td><input name="new_campaign_source" type="text" id="new_campaign_source" value="" class="regular-text">
					<p class="description">Example: newsletter, facebook.</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="new_campaign_medium">Add new Campaign Medium</label></th>
				<td><input name="new_campaign_medium" type="text" id="new_campaign_medium" value="" class="regular-text">
					<p class="description">Example: banner, cpc.</p>
				</td>
			</tr>
		</table>
		<h2 class="title"><span class="dashicons dashicons-dismiss"></span> Remove</h2>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="remove_campaign_source">Remove Campaign Source</label></th>
				<td>
					<select name="remove_campaign_source">
						<option value="">Select</option>
						<?php 
						$sources = $plugin->get_sources();
						foreach ($sources as $source) {
							?>
							<option value="<?php echo $source->source_name; ?>"><?php echo $source->source_name; ?></option>
							<?php
						}
						 ?>
					</select>
				</td>
			</tr>
		
			<tr>
				<th scope="row"><label for="remove_campaign_medium">Remove Campaign Medium</label></th>
				<td>
					<select name="remove_campaign_medium">
						<option value="">Select</option>
						<?php 
						$mediums = $plugin->get_mediums();
						foreach ($mediums as $medium) {
							?>
							<option value="<?php echo $medium->medium_name; ?>"><?php echo $medium->medium_name; ?></option>
							<?php
						}
						?>
					</select>
				</td>
			</tr>
		</table>
		<h2 class="title"><span class="dashicons dashicons-admin-plugins"></span> Bitly setting</h2>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="bitly_token">Bitly Token</label></th>
				<td><input name="bitly_token" type="text" id="bitly_token" value="<?php echo get_option( $plugin->plugin_name . '_bitly_token' ); ?>" class="regular-text">   or Empty Bitly Token: <input type="checkbox" name="remove_bitly_token" value="1"> 
					<p class="description">How to get your <a class="alti_tlg_tab_link" href="#alti_tlg_tab-3">Bitly Token</a>?</p>
				</td>
			</tr>

			</table>
			<p class="submit"><input type="submit" name="submit_settings" id="submit" class="button button-primary" value="Save Changes"></p>

		</form>
	</div>

	<div class="alti_tlg_container alti_tlg_tab alti_tlg_tab-3">
		<h2 class="title"><span class="dashicons dashicons-welcome-learn-more"></span> Help</h2>
		<h3>Best Practices</h3>
		<p>
			<a target="_blank" href="https://support.google.com/analytics/answer/1037445?hl=en">Best practices</a> for creating your Campaign.
		</p>
		<h3>Bitly</h3>
		<p>
			Set up the Bitly is not that easy, but doable in a few steps. You will need to: <br>
			1. <a target="_blank" href="https://bitly.com/a/sign_up">Create an account</a> on bitly.com <br>
			2. <a target="_blank" href="https://bitly.com/a/create_oauth_app">Create an application</a> dedicated to communicate with bitly API.<br>
			3. Once your application is set up you will be able to retrieve the <strong>Token</strong>.
		</p>
		<h3>You did not find an answer?</h3>
		<p>
			Then you should visit the <strong>support page</strong> of the plugin on wordpress.org or in the <strong>official page</strong> of the plugin hosted on alticreation.com.
		</p>
	</div>

	</div>

	<?php include dirname( __FILE__ ) . '/alti_tlg-admin-partial.php'; ?>

</div>