<?php
/*
 * Plugin Name: iThemes Exchange - One Click Export Add-on
 * Version: 2.0.3
 * Description: Adds the OneClickExport addon to iThemes Exchange
 * Plugin URI: http://ithemes.com/exchange/one-click-export/
 * Author: iThemes
 * Author URI: http://ithemes.com
 * iThemes Package: exchange-addon-mailchimp
 
 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
*/

/**
 * This registers our plugin as a membership addon
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_register_one_click_export_addon() {
	$options = array(
		'name'              => __( 'Export', 'LION' ),
		'description'       => __( 'Add Export Function to Exchange.', 'LION' ),
		'author'            => 'iThemes',
		'author_url'        => 'http://ithemes.com/exchange/one-click-export/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/mailchimp50px.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'misc',
		'settings-callback' => 'it_exchange_one_click_export_settings_callback',
	);
	it_exchange_register_addon( 'one-click-export', $options );
}
add_action( 'it_exchange_register_addons', 'it_exchange_register_one_click_export_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_one_click_export_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'it_exchange_one_click_export_set_textdomain' );

/**
 * Registers Plugin with iThemes updater class
 *
 * @since 1.0.0
 *
 * @param object $updater ithemes updater object
 * @return void
*/
function ithemes_exchange_addon_one_click_export_updater_register( $updater ) { 
	    $updater->register( 'exchange-addon-one-click-export', __FILE__ );
}
//add_action( 'ithemes_updater_register', 'ithemes_exchange_addon_one_click_export_updater_register' );
//require( dirname( __FILE__ ) . '/lib/updater/load.php' );
