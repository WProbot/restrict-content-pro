<?php
/**
 * Uninstall Restrict Content Pro
 *
 * Deletes the following plugin data:
 *      - RCP post meta
 *      - RCP term meta
 *      - Pages created and used by RCP
 *      - Clears scheduled RCP cron events
 *      - Options added by RCP
 *      - RCP database tables
 *
 * @package     Restrict Content Pro
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2017, Restrict Content Pro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Load RCP file.
include_once( 'restrict-content-pro.php' );

global $wpdb, $rcp_options;

if( isset( $rcp_options['uninstall_on_delete'] ) ) {

	// Delete all post meta.
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE 'rcp\_%'" );

	// Delete all term meta.
	$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key LIKE 'rcp\_%'" );

	// Delete the plugin pages.
	$rcp_pages = array( 'registration_page', 'redirect', 'account_page', 'edit_profile', 'update_card' );
	foreach( $rcp_pages as $page_option ) {
		$page_id = isset( $rcp_options[ $page_option ] ) ? $rcp_options[ $page_option ] : false;
		if( $page_id ) {
			wp_delete_post( $page_id, true );
		}
	}

	// Clear scheduled cron events.
	wp_clear_scheduled_hook( 'rcp_expired_users_check' );
	wp_clear_scheduled_hook( 'rcp_send_expiring_soon_notice' );
	wp_clear_scheduled_hook( 'rcp_check_member_counts' );

	// Remove all plugin settings.
	$options = array( 'rcp_settings', 'rcp_db_version', 'rcp_discounts_db_version', 'rcp_payments_db_version', 'rcp_is_installed', 'rcp_version', 'rcp_license_status' );
	foreach( $options as $option) {
		delete_option( $option );
	}

	// Remove all database tables.
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rcp_discounts" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rcp_payments" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rcp_payment_meta" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "rcp_subscription_level_meta" );
	$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "restrict_content_pro" );

}