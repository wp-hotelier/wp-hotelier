<?php
/**
 * Hotelier Admin Settings Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Settings' ) ) :

/**
 * HTL_Admin_Settings Class
 *
 * Creates the Settings page.
 */
class HTL_Admin_Settings {

	/**
    * Holds the hotelier_options array
    */
    private $options = array();

    /**
    * Holds the tabs array
    */
	private $settings_tabs = array();

	/**
    * Holds the registered settings array
    */
	private $registered_settings = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'add_settings_menu_page' ), 9 );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
		add_action( 'admin_init', array( $this, 'add_separator' ) );
		add_action( 'init', array( $this, 'registered_settings' ) );

		$this->includes();
		$this->options = (array) get_option( 'hotelier_settings' );
	}

	/**
	 * Include required files.
	 */
	public function includes() {
		include_once 'class-htl-admin-settings-default.php';
		include_once 'class-htl-admin-settings-fields.php';
	}

	/**
	 * Add menu items
	 */
	public function add_settings_menu_page() {
		add_menu_page( '', esc_html__( 'Hotelier', 'wp-hotelier' ), 'manage_hotelier', 'hotelier-settings', array( $this, 'create_settings_page' ), '', '45.5' );

		add_submenu_page( 'hotelier-settings', esc_html__( 'WP Hotelier Settings', 'wp-hotelier' ), esc_html__( 'Settings', 'wp-hotelier' ), 'manage_hotelier', 'hotelier-settings', array( $this, 'create_settings_page' ) );
	}

	/**
	 * Register menu separator
	 */
	public function add_separator( $position ) {
		global $menu;

		$menu[ $position ] = array(
			0	=>	'',
			1	=>	'read',
			2	=>	'separator' . $position,
			3	=>	'',
			4	=>	'wp-menu-separator hotelier'
		);
	}

	/**
	 * Get settings tabs.
	 */
	public function get_settings_tabs() {
		if ( empty( $this->settings_tabs ) ) {
			$settings_tabs = array();

			$settings_tabs[ 'general' ]                = esc_html__( 'General', 'wp-hotelier' );
			$settings_tabs[ 'rooms-and-reservations' ] = esc_html__( 'Rooms & reservations', 'wp-hotelier' );
			$settings_tabs[ 'seasonal-prices' ]        = esc_html__( 'Seasonal prices', 'wp-hotelier' );
			$settings_tabs[ 'payment' ]                = esc_html__( 'Payment gateways', 'wp-hotelier' );
			$settings_tabs[ 'tax' ]                    = esc_html__( 'Tax', 'wp-hotelier' );
			$settings_tabs[ 'emails' ]                 = esc_html__( 'Emails', 'wp-hotelier' );
			$settings_tabs[ 'tools' ]                  = esc_html__( 'Tools', 'wp-hotelier' );

			$this->settings_tabs = apply_filters( 'hotelier_get_settings_tabs', $settings_tabs );
		}

		return $this->settings_tabs;
	}

	/**
	 * Settings page.
	 *
	 * Renders the settings page contents.
	 */
	public function create_settings_page() {

		// Include settings tabs
		$tabs = $this->get_settings_tabs();

		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET[ 'tab' ], $tabs ) ? $_GET[ 'tab' ] : 'general';

		ob_start();
		?>
		<div class="wrap htl-ui-scope hotelier-settings hotelier-settings--<?php echo esc_attr( $active_tab ); ?>">

			<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-navigation.php'; ?>

			<div class="hotelier-settings-wrapper">
				<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-header.php'; ?>

				<div class="hotelier-settings-panel">
					<form method="post" action="options.php">
						<table class="hotelier-settings-table-form">
							<?php
							settings_fields( 'hotelier_settings' );
							do_action( 'hotelier_settings_tab_top_' . $active_tab );
							do_settings_fields( 'hotelier_settings_' . $active_tab, 'hotelier_settings_' . $active_tab );
							?>
						</table>
						<?php submit_button( false, 'htl-ui-button htl-ui-button--save-settings', 'submit', false ); ?>
					</form>
				</div>

				<?php include_once HTL_PLUGIN_DIR . 'includes/admin/settings/views/html-settings-pro-section.php'; ?>
			</div>
		</div><!-- .wrap -->
		<?php
		echo ob_get_clean();
	}

	/**
	 * Retrieve the array of plugin settings.
	 */
	public function registered_settings() {
		return HTL_Admin_Settings_Default::settings();
	}

	/**
	 * Add all settings sections and fields.
	 */
	public function add_settings() {
		if ( false == get_option( 'hotelier_settings' ) ) {
			add_option( 'hotelier_settings' );
		}

		foreach( $this->registered_settings() as $tab => $settings ) {
			add_settings_section(
				'hotelier_settings_' . $tab,
				__return_null(),
				'__return_false',
				'hotelier_settings_' . $tab
			);

			foreach ( $settings as $option ) {
				$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

				add_settings_field(
					'hotelier_settings[ ' . $option[ 'id' ] . ' ]',
					$name,
					array( $this, 'default_callback' ),
					'hotelier_settings_' . $tab,
					'hotelier_settings_' . $tab,
					array(
						'section'       => $tab,
						'id'            => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
						'class'         => isset( $option[ 'class' ] ) ? $option[ 'class' ] : null,
						'type'          => isset( $option[ 'type' ] ) ? $option[ 'type' ] : 'text',
						'desc'          => ! empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
						'subdesc'       => ! empty( $option[ 'subdesc' ] ) ? $option[ 'subdesc' ] : '',
						'name'          => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
						'size'          => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
						'options'       => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
						'std'           => isset( $option[ 'std' ] ) ? $option[ 'std' ] : null,
						'multiple'      => isset( $option[ 'multiple' ] ) ? $option[ 'multiple' ] : null,
						'placeholder'   => isset( $option[ 'placeholder' ] ) ? $option[ 'placeholder' ] : null,
						'min'           => isset( $option[ 'min' ] ) ? $option[ 'min' ] : null,
						'max'           => isset( $option[ 'max' ] ) ? $option[ 'max' ] : null,
						'toggle'        => isset( $option[ 'toggle' ] ) ? true : null,
						'show-if'       => isset( $option[ 'show-if' ] ) ? $option[ 'show-if' ] : null,
						'show-element'  => isset( $option[ 'show-element' ] ) ? $option[ 'show-element' ] : null,
						'empty-message' => isset( $option[ 'empty-message' ] ) ? $option[ 'empty-message' ] : null,
					)
				);
			}
		}

		// Creates our settings in the options table
		register_setting( 'hotelier_settings', 'hotelier_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Settings Sanitization
	 *
	 *
	 * @param array $input The value inputted in the field
	 *
	 * @return string $input Sanitizied value
	 */
	function sanitize_settings( $input = array() ) {
		if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
			return $input;
		}

		parse_str( $_POST[ '_wp_http_referer' ], $referrer );

		$settings = $this->registered_settings();
		$tab      = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

		// Save unchecked checkboxes
		foreach ( $settings[ $tab ] as $index => $args ) {
			if ( isset( $args[ 'type' ] ) && ( $args[ 'type' ] == 'checkbox' ) ) {
				if ( ! isset( $input[ $args[ 'id' ] ] ) ) {
					$input[ $args[ 'id' ] ] = 0;
				}
			}
		}

		$input = $input ? $input : array();
		$input = apply_filters( 'hotelier_settings_' . $tab . '_sanitize', $input );

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type = isset( $settings[ $tab ][ $key ][ 'type' ] ) ? $settings[ $tab ][ $key ][ 'type' ] : false;

			if ( $type ) {
				// Field type specific filter
				$value = apply_filters( 'hotelier_settings_sanitize_' . $type, $value, $key );
			}

			// Field ID specific filter
			$value = apply_filters( 'hotelier_settings_sanitize_' . $key, $value, $key );

			// Save value
			$input[ $key ] = $value;
		}

		// Loop through the settings and unset any that are empty for the tab being saved
		if ( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $value ) {

				if ( is_numeric( $key ) ) {
					$key = $value[ 'id' ];
				}

				if ( empty( $input[ $key ] ) ) {
					unset( $this->options[ $key ] );
				}
			}
		}

		// Merge new settings with the existing
		$output = array_merge( $this->options, $input );

		add_settings_error( 'hotelier-notices', '', esc_html__( 'Your settings have been saved.', 'wp-hotelier' ), 'updated' );

		return $output;
	}

	/**
	 * Default field callback
	 *
	 * Default output is empty (a simple warning). We will use a filter to create the field.
	 *
	 * @param array $args Arguments passed by the setting
	 */
	function default_callback( $args ) {
		$html = sprintf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-hotelier' ), $args[ 'id' ] );
		echo apply_filters( 'hotelier_settings_' . $args[ 'type' ] . '_callback', $html, $args );
	}
}

endif;

return new HTL_Admin_Settings();
