<?php
/**
 * Plugin Name:       Tracking Link Generator
 * Plugin URI:        
 * Description:       Generates links for Analytics tools and short link. Enter your Campaign Name, Source, Medium (UTM link) to generate a full link and a short link (trough the bitly API) all in once
 * Version:           0.2
 * Author:            Alexis Blondin
 * Author URI:        
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       alti_tlg
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function activate_alti_tlg() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alti_tlg.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alti_tlg-activator.php';
	
	$plugin_ = new Alti_tlg();
	$activation = new Alti_tlg_Activator( $plugin_->get_plugin_name() );
	$activation->run();

}

function deactivate_alti_tlg() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alti_tlg.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-alti_tlg-deactivator.php';
	
	$plugin_ = new Alti_tlg();
	$plugin = new Alti_tlg_Deactivator( $plugin_->get_plugin_name() );
	$plugin->deactivate();

}

register_activation_hook( __FILE__, 'activate_alti_tlg' );
register_deactivation_hook( __FILE__, 'deactivate_alti_tlg' );

require plugin_dir_path( __FILE__ ) . 'includes/class-alti_tlg.php';

$plugin = new Alti_tlg();
$plugin->run();

