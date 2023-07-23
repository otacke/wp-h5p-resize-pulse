<?php
/**
 * Plugin Name: SNORDIAN's H5P Resize Pulse
 * Plugin URI: https://github.com/otacke/wp-h5p-resize-pulse
 * Description: Provides you with a potential workaround for H5P content that won't show in tabs, accordions, lightboxes, etc.
 * Version: 0.1.4
 * Author: Oliver Tacke
 * Author URI: https://www.olivertacke.de/labs
 * License: MIT
 * Text Domain: H5PRESIZEPULSE
 * Domain Path: /languages
 */

namespace H5PRESIZEPULSE;

// as suggested by the WordPress community
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( ! defined( 'H5PRESIZEPULSE_VERSION' ) ) {
	define( 'H5PRESIZEPULSE_VERSION', '0.1.4' );
}

// Load classes
require_once( __DIR__ . '/class-options.php' );

/**
 * Activate the plugin.
 *
 * @since 0.1.0
 */
function on_activation() {
	Options::set_defaults();
	add_capabilities();
}

/**
 * Deactivate the plugin.
 *
 * @since 0.1.0
 */
function on_deactivation() {
}

/**
 * Uninstall the plugin.
 *
 * @since 0.1.0
 */
function on_uninstall() {
	remove_capabilities();
	Options::delete_options();
}

/**
 * Update the plugin.
 *
 * @since 0.1.0
 */
function update() {
	if ( H5PRESIZEPULSE_VERSION === get_option( 'h5presizepulse_version' ) ) {
		return;
	}

	update_option( 'h5presizepulse_version', H5PRESIZEPULSE_VERSION );
}

/**
 * Add capabilities.
 *
 * @since 0.1.0
 */
function add_capabilities() {
	// Add capabilities
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$all_roles = $wp_roles->roles;
	foreach ( $all_roles as $role_name => $role_info ) {
		$role = get_role( $role_name );

		map_capability( $role, $role_info, 'manage_options', 'manage_h5presizepulse_options' );
	}
}

/**
 * Remove capabilities.
 *
 * @since 0.1.0
 */
function remove_capabilities() {
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$all_roles = $wp_roles->roles;
	foreach ( $all_roles as $role_name => $role_info ) {
		$role = get_role( $role_name );

		if ( isset( $role_info['capabilities']['manage_h5presizepulse_options'] ) ) {
			$role->remove_cap( 'manage_h5presizepulse_options' );
		}
	}
}

/**
 * Make sure that a role has or hasn't the provided capability depending on
 * existing roles.
 *
 * @since 0.1.0
 * @param stdClass $role Role object.
 * @param array $role_info Role information.
 * @param string|array $existing_cap Existing capability.
 * @param string $new_cap New capability.
 */
function map_capability( $role, $role_info, $existing_cap, $new_cap ) {
	if ( isset( $role_info['capabilities'][ $new_cap ] ) ) {
		// Already has new cap ...
		if ( ! has_capability( $role_info['capabilities'], $existing_cap ) ) {
			// But shouldn't have it!
			$role->remove_cap( $new_cap );
		}
	} else {
		// Doesn't have new cap ...
		if ( has_capability( $role_info['capabilities'], $existing_cap ) ) {
			// But should have it!
			$role->add_cap( $new_cap );
		}
	}
}

/**
 * Check that role has the needed capabilities.
 *
 * @since 0.1.0
 * @param array $role_capabilities Role capabilities.
 * @param string|array $capability Capabilities to check for.
 * @return boolean True, if role has capability, else false.
 */
function has_capability( $role_capabilities, $capability ) {
	if ( is_array( $capability ) ) {
		foreach ( $capability as $cap ) {
			if ( ! isset( $role_capabilities[ $cap ] ) ) {
				return false;
			}
		}
	} elseif ( ! isset( $role_capabilities[ $capability ] ) ) {
		return false;
	}
	return true;
}

/**
 * Load the text domain for internationalization.
 *
 * @since 0.1.0
 */
function h5presizepulse_load_plugin_textdomain() {
	load_plugin_textdomain( 'H5PRESIZEPULSE', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

/**
 * Initialize the resize pulse script.
 *
 * @since 0.1.0
 */
function initialize_resize_pulse() {
	wp_enqueue_script( 'H5PRESIZEPULSE', plugins_url( '/js/h5p-resize-pulse.js', __FILE__ ), array(), H5PRESIZEPULSE_VERSION );

	// Pass variables to JavaScript
	wp_localize_script(
		'H5PRESIZEPULSE',
		'h5pResizePulseParameters', array(
			'mode' => Options::get_trigger_mode(),
			'timeout' => Options::get_timeout(),
			'selector' => Options::get_trigger_selector()
		)
	);
}

/**
 * Initialize options.
 *
 * @since 0.1.0
 */
function initialize_settings() {
	// Include options
	$h5presizepulse_settings = new Options;
}

// Register hooks
register_activation_hook( __FILE__, 'H5PRESIZEPULSE\on_activation' );
register_deactivation_hook( __FILE__, 'H5PRESIZEPULSE\on_deactivation' );
register_uninstall_hook( __FILE__, 'H5PRESIZEPULSE\on_uninstall' );

// Prepare plugin
add_action( 'plugins_loaded', 'H5PRESIZEPULSE\h5presizepulse_load_plugin_textdomain' );
add_action( 'plugins_loaded', 'H5PRESIZEPULSE\update' );

// Initialize pulse on post/page
add_action( 'the_post', 'H5PRESIZEPULSE\initialize_resize_pulse' );

// Initialize plugin settings
add_action( 'init', 'H5PRESIZEPULSE\initialize_settings' );
