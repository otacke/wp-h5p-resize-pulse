<?php

namespace H5PRESIZEPULSE;

/**
 * Display and handle the settings page
 *
 * @package H5PRESIZEPULSE
 * @since 0.1.0
 */
class Options {

	// Timeout in ms
	const TIMEOUT_DEFAULT = 500;
	const TIMEOUT_MIN     = 250;

	// Waiting for PHP 7 to hit the mainstream ...
	private static $option_slug = 'h5presizepulse_option';
	private static $options;

	/**
	 * Start up
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Set defaults.
	 *
	 * @since 0.1.0
	 */
	public static function set_defaults() {
		// Set version
		update_option( 'h5presizepulse_version', H5PRESIZEPULSE_VERSION );

		if ( get_option( 'h5presizepulse_defaults_set' ) ) {
			return; // No need to set defaults
		}

		// Remember that defaults have been set
		update_option( 'h5presizepulse_defaults_set', true );

		// Set defaults
		update_option(
			self::$option_slug,
			array(
				'timeout' => self::TIMEOUT_DEFAULT,
			)
		);
	}

	/**
	 * Delete options.
	 *
	 * @since 0.1.0
	 */
	public static function delete_options() {
		delete_option( self::$option_slug );
		delete_site_option( self::$option_slug );
		delete_option( 'h5presizepulse_defaults_set' );
		delete_option( 'h5presizepulse_version' );
	}

	/**
	 * Add options page.
	 *
	 * @since 0.1.0
	 */
	public function add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'H5P Resize Pulse',
			'manage_h5presizepulse_options',
			'h5presizepulse-admin',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback.
	 *
	 * @since 0.1.0
	 */
	public function create_admin_page() {
		// Set class property
		?>
		<div class="wrap">
			<h2>H5P Resize Pulse</h2>
			<form method="post" action="options.php">
				<?php
				// Print out all hidden setting fields
				settings_fields( 'h5presizepulse_option_group' );
				do_settings_sections( 'h5presizepulse-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings.
	 *
	 * @since 0.1.0
	 */
	public function page_init() {
		register_setting(
			'h5presizepulse_option_group',
			'h5presizepulse_option',
			array( $this, 'sanitize' )
		);

		add_settings_section(
			'general_settings',
			__( 'General', 'h5presizepulse' ),
			array( $this, 'print_general_section_info' ),
			'h5presizepulse-admin'
		);

		add_settings_field(
			'timeout',
			__( 'Time interval', 'h5presizepulse' ),
			array( $this, 'timeout_callback' ),
			'h5presizepulse-admin',
			'general_settings'
		);
	}

	/**
	 * Sanitize each setting field as needed.
	 *
	 * @since 0.1.0
	 * @param array $input Contains all settings fields as array keys.
	 * @return array Output.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['timeout'] ) ) {

			// Parse integer value
			if (
				'string' === gettype( $input['timeout'] ) ||
				'integer' === gettype( $input['timeout'] )
			) {
				$new_input['timeout'] = intval( $input['timeout'] );
			} else {
				$new_input['timeout'] = self::TIMEOUT_DEFAULT;
			}

			// Sanitize for minimum value
			if ( $new_input['timeout'] < self::TIMEOUT_MIN ) {
				$new_input['timeout'] = self::TIMEOUT_MIN;
			}
		}

		return $new_input;
	}

	/**
	 * Print section text for general settings.
	 *
	 * @since 0.1.0
	 */
	public function print_general_section_info() {
	}

	/**
	 * Get timeout option.
	 *
	 * @since 0.1.0
	 */
	public function timeout_callback() {
		// I don't like this mixing of HTML and PHP, but it seems to be WordPress custom
		?>
		<label for="timeout">
		<input
			type="number"
			min="<?php echo self::TIMEOUT_MIN; ?>"
			name="h5presizepulse_option[timeout]"
			id="timeout"
			value="<?php echo self::get_timeout(); ?>"
		/>
		<?php
			echo __( 'Time interval to trigger H5P resizing in milliseconds.', 'h5presizepulse' );
		?>
		<p class="description">
		<?php
			echo __( 'The smaller this value, the quicker H5P content will render, but the more likely it is to stall the user\'s browser. Choose wisely!', 'h5presizepulse' );
		?>
		</p>
		</label>
		<?php
	}

	/**
	 * Get timeout value.
	 *
	 * @since 0.1.0
	 * @return string Timeout value.
	 */
	public static function get_timeout() {
		return self::$options['timeout'];
	}

	/**
	 * Init function for the class.
	 *
	 * @since 0.1.0
	 */
	static function init() {
		self::$options = get_option( self::$option_slug, false );
	}
}
Options::init();
