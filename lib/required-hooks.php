<?php
	

/**
 * Adds the help menu at the bottom of the menu
 *
 * @since 0.4.17
 *
 * @return void
*/
function add_one_click_export_to_exchange_menu() {
	// Exporter menu
	add_submenu_page( 'it-exchange', __( 'One Click Export', 'LION' ), __( 'One Click Export', 'LION' ), 'manage_options', 'it-exchange-one-click-export', 'it_exchange_one_click_export_addon_page' );
}
add_action( 'admin_menu', 'add_one_click_export_to_exchange_menu' );