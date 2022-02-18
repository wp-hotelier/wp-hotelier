<?php
/**
 * Hotelier Default Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  2.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Admin_Settings_Default' ) ) :

/**
 * HTL_Admin_Settings_Default Class
 */
class HTL_Admin_Settings_Default {

	/**
	 * Get all published pages.
	 */
	public static function get_pages() {
		$all_pages = array( '' => '' ); // Blank option

		if( ( ! isset( $_GET[ 'page' ] ) || 'hotelier-settings' != $_GET[ 'page' ] ) ) {
			return $all_pages;
		}

		$pages = get_pages();

		if ( $pages ) {
			foreach ( $pages as $page ) {
				$all_pages[ $page->ID ] = $page->post_title;
			}
		}

		return $all_pages;
	}

	/**
	 * Get all registered gateways.
	 */
	public static function get_gateways() {
		$gateways = array();

		foreach ( HTL()->payment_gateways()->payment_gateways() as $gateway_id => $gateway ) {
			$gateways[ $gateway[ 'id' ] ] = $gateway[ 'admin_label' ];
		}

		return $gateways;
	}

	/**
	 * Get room size options.
	 *
	 * A filter is provided to allow extensions to add their own room size
	 */
	public static function get_room_size_options() {
		$options = array(
			'm²'  => 'm²',
			'ft²' => 'ft²'
		);

		return apply_filters( 'hotelier_room_size_options', $options );
	}

	/**
	 * Get booking mode options.
	 *
	 * A filter is provided to allow extensions to add their own booking mode options
	 */
	public static function get_booking_mode_options() {
		$options = array(
			'no-booking'      => esc_html__( 'No booking', 'wp-hotelier' ),
			'manual-booking'  => esc_html__( 'Manual booking', 'wp-hotelier' ),
			'instant-booking' => esc_html__( 'Instant booking ', 'wp-hotelier' )
		);

		return apply_filters( 'hotelier_booking_mode_options', $options );
	}

	/**
	 * Get emails type options.
	 */
	public static function get_emails_type_options() {
		$types = array(
			'plain'     => esc_html__( 'Plain text', 'wp-hotelier' ),
			'html'      => esc_html__( 'HTML', 'wp-hotelier' ),
			'multipart' => esc_html__( 'Multipart text', 'wp-hotelier' )
		);

		return $types;
	}

	/**
	 * Get listing sorting options.
	 */
	public static function get_listing_sorting() {
		$options = array(
			'menu_order' => esc_html__( 'Menu order', 'wp-hotelier' ),
			'date'       => esc_html__( 'Sort by most recent', 'wp-hotelier' ),
			'title'      => esc_html__( 'Sort by title', 'wp-hotelier' ),
		);

		return apply_filters( 'hotelier_listing_sorting_options', $options );
	}

	/**
	 * Retrieve the array of plugin settings.
	 */
	public static function settings() {
		/**
		 * Filters are provided for each settings section to allow
		 * extensions and other plugins to add their own settings
		 */
		$settings = array(
			/* General Settings */
			'general' => apply_filters( 'hotelier_settings_general',
				array(
					'hotelier_info' => array(
						'id'    => 'hotelier_info',
						'name'  => '<strong>' . esc_html__( 'Hotel info', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'hotel_name' => array(
						'id'   => 'hotel_name',
						'name' => esc_html__( 'Hotel name', 'wp-hotelier' ),
						'desc' => __( 'The name of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_address' => array(
						'id'   => 'hotel_address',
						'name' => esc_html__( 'Hotel address', 'wp-hotelier' ),
						'desc' => __( 'The address of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_postcode' => array(
						'id'   => 'hotel_postcode',
						'name' => esc_html__( 'Hotel postcode', 'wp-hotelier' ),
						'desc' => __( 'The postcode/zip of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_locality' => array(
						'id'   => 'hotel_locality',
						'name' => esc_html__( 'Hotel locality', 'wp-hotelier' ),
						'desc' => __( 'The locality of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_telephone' => array(
						'id'   => 'hotel_telephone',
						'name' => esc_html__( 'Hotel telephone', 'wp-hotelier' ),
						'desc' => __( 'The telephone number of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_fax' => array(
						'id'   => 'hotel_fax',
						'name' => esc_html__( 'Hotel fax', 'wp-hotelier' ),
						'desc' => __( 'The fax number of your hotel.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_email' => array(
						'id'   => 'hotel_email',
						'name' => esc_html__( 'Hotel email', 'wp-hotelier' ),
						'desc' => __( 'The email address of your hotel.', 'wp-hotelier' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' )
					),
					'hotel_checkin' => array(
						'id'   => 'hotel_checkin',
						'name' => esc_html__( 'Check-in', 'wp-hotelier' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '12',
							'to' => '21',
						),
					),
					'hotel_checkout' => array(
						'id'   => 'hotel_checkout',
						'name' => esc_html__( 'Check-out', 'wp-hotelier' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '8',
							'to' => '13',
						),
					),
					'hotel_pets' => array(
						'id'           => 'hotel_pets',
						'name'         => esc_html__( 'Are pets allowed?', 'wp-hotelier' ),
						'subdesc'      => esc_html__( 'Enable this option if pets are accepted at your hotel.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => 'hotel_pets_message',
					),
					'hotel_pets_message' => array(
						'id'   => 'hotel_pets_message',
						'name' => esc_html__( 'Pets instructions', 'wp-hotelier' ),
						'desc' => __( 'If you need to give some special instructions, use this field.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Pets are allowed', 'wp-hotelier' ),
					),
					'hotel_special_requests_message' => array(
						'id'   => 'hotel_special_requests_message',
						'name' => esc_html__( 'Special requests message', 'wp-hotelier' ),
						'desc' => __( 'This description will appear under the special requests textarea.', 'wp-hotelier' ),
						'type' => 'textarea',
						'std'  => esc_html__( 'Special requests cannot be guaranteed but we will do our best to meet your needs.', 'wp-hotelier' ),
					),
					'hotel_accepted_cards' => array(
						'id'   => 'hotel_accepted_cards',
						'name' => esc_html__( 'Accepted credit cards', 'wp-hotelier' ),
						'desc' => sprintf( __( 'The accepted credit cards for payments made at the hotel. These are not used to pay the deposit (configure the <a href="%s">Payment Gateways</a> settings for that).', 'wp-hotelier' ), '?page=hotelier-settings&tab=payment' ),
						'type' => 'card_icons',
						'options'  => apply_filters( 'hotelier_hotel_accepted_cards', array(
							'mastercard' => 'Mastercard',
							'visa'       => 'Visa',
							'amex'       => 'American Express',
							'discover'   => 'Discover',
							'maestro'    => 'Maestro',
							'visa_e'     => 'Visa Electron',
							'cirrus'     => 'Cirrus',
						) ),
					),
					'hotelier_pages' => array(
						'id'    => 'hotelier_pages',
						'name'  => '<strong>' . esc_html__( 'Hotelier pages', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'listing_page' => array(
						'id'      => 'listing_page',
						'name'    => esc_html__( 'Listing page', 'wp-hotelier' ),
						'desc'    => sprintf( __( 'This is the page (the listing page) where guests will see the rooms that are available for the selected dates. The <code>[hotelier_listing]</code> shortcode must be on this page. You can ignore this option if you decide to <a href="%s">disable the listing page</a>.', 'wp-hotelier' ), '?page=hotelier-settings&tab=rooms-and-reservations' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'booking_page' => array(
						'id'      => 'booking_page',
						'name'    => esc_html__( 'Booking page', 'wp-hotelier' ),
						'desc'    => __( 'This is the booking page where guests will complete their reservations. The <code>[hotelier_booking]</code> shortcode must be on this page.', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'terms_page' => array(
						'id'      => 'terms_page',
						'name'    => esc_html__( 'Terms page', 'wp-hotelier' ),
						'desc'    => __( 'If set, guests will be asked to agree to the hotel terms before to request a new reservation.', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'enforce_ssl_booking' => array(
						'id'           => 'enforce_ssl_booking',
						'name'         => esc_html__( 'Enforce SSL booking', 'wp-hotelier' ),
						'subdesc'         => __( 'Enforce SSL (HTTPS) on the booking page (you must have an SSL certificate installed to use this option).', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => 'unforce_ssl_booking',
					),
					'unforce_ssl_booking' => array(
						'id'      => 'unforce_ssl_booking',
						'name'    => esc_html__( 'Force HTTP leaving booking', 'wp-hotelier' ),
						'subdesc' => __( 'Force HTTP when leaving the booking page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'hotelier_endpoints' => array(
						'id'    => 'hotelier_endpoints',
						'name'  => '<strong>' . esc_html__( 'Hotelier endpoints', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'reservation_received' => array(
						'id'      => 'reservation_received',
						'name'    => esc_html__( 'Reservation received', 'wp-hotelier' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the page guests are sent to after completing their reservation.', 'wp-hotelier' ),
						'type'    => 'text',
						'std'     => 'reservation-received'
					),
					'pay_endpoint' => array(
						'id'      => 'pay_endpoint',
						'name'    => esc_html__( 'Pay reservation', 'wp-hotelier' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the payment form (for reservations generated by the admin).', 'wp-hotelier' ),
						'type'    => 'text',
						'std'     => 'pay-reservation'
					),
					'currency_settings' => array(
						'id'    => 'currency_settings',
						'name'  => '<strong>' . esc_html__( 'Currency settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'currency' => array(
						'id'      => 'currency',
						'name'    => esc_html__( 'Currency', 'wp-hotelier' ),
						'desc'    => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => htl_get_currencies()
					),
					'currency_position' => array(
						'id'      => 'currency_position',
						'name'    => esc_html__( 'Currency position', 'wp-hotelier' ),
						'desc'    => __( 'Choose the location of the currency sign.', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => array(
							'before' => esc_html__( 'Before - $10', 'wp-hotelier' ),
							'after'  => esc_html__( 'After - 10$', 'wp-hotelier' )
						)
					),
					'thousands_separator' => array(
						'id'   => 'thousands_separator',
						'name' => esc_html__( 'Thousands separator', 'wp-hotelier' ),
						'desc' => __( 'This sets the thousand separator (usually , or .) of displayed prices.', 'wp-hotelier' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => ','
					),
					'decimal_separator' => array(
						'id'   => 'decimal_separator',
						'name' => esc_html__( 'Decimal separator', 'wp-hotelier' ),
						'desc' => __( 'This sets the decimal separator (usually , or .) of displayed prices.', 'wp-hotelier' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => '.'
					),
					'price_num_decimals' => array(
						'id'   => 'price_num_decimals',
						'name' => esc_html__( 'Number of decimals', 'wp-hotelier' ),
						'desc' => __( 'This sets the number of decimals points shown in displayed prices.', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'coupon_settings' => array(
						'id'    => 'coupon_settings',
						'name'  => '<strong>' . esc_html__( 'Coupon settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'enable_coupons' => array(
						'id'     => 'enable_coupons',
						'name'   => esc_html__( 'Enable coupons', 'wp-hotelier' ),
						'subdesc'   => __( 'Enable the use of coupon codes.', 'wp-hotelier' ),
						'type'   => 'checkbox',
						'toggle' => true,
					),
					'privacy_settings' => array(
						'id'    => 'privacy_settings',
						'name'  => '<strong>' . esc_html__( 'Privacy settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'privacy_remove_reservation_data_on_erasure_request' => array(
						'id'     => 'privacy_remove_reservation_data_on_erasure_request',
						'name'   => esc_html__( 'Account erasure requests', 'wp-hotelier' ),
						'subdesc'   => __( 'When handling an account erasure request, should personal data within reservations be retained or removed?', 'wp-hotelier' ),
						'type'   => 'checkbox',
						'toggle' => true,
					),
					'privacy_settings_snippet' => array(
						'id'   => 'privacy_settings_snippet',
						'name' => esc_html__( 'Privacy snippet', 'wp-hotelier' ),
						'desc' => sprintf( __( 'Optionally add some text about your website privacy policy to show on the booking form. %1$s will be replaced by a link to your <a href="%2$s">privacy policy page</a>.', 'wp-hotelier' ), '<code>[privacy_policy]</code>', admin_url( 'privacy.php' ) ),
						'std'  => esc_html__( 'Your personal data will be used to support your experience throughout this website, to process your reservations, and for other purposes described in our [privacy_policy].', 'wp-hotelier' ),
						'type' => 'textarea'
					),
				)
			),
			/* Room Settings */
			'rooms-and-reservations' => apply_filters( 'hotelier_settings_rooms_and_reservations',
				array(
					'room_settings' => array(
						'id'    => 'room_settings',
						'name'  => '<strong>' . esc_html__( 'Room settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'room_size_unit' => array(
						'id'      => 'room_size_unit',
						'name'    => esc_html__( 'Room size unit', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_room_size_options()
					),
					'room_hide_gallery' => array(
						'id'      => 'room_hide_gallery',
						'name'    => esc_html__( 'Hide room gallery', 'wp-hotelier' ),
						'subdesc' => __( 'Hide the gallery on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_hide_datepicker' => array(
						'id'      => 'room_hide_datepicker',
						'name'    => esc_html__( 'Hide room datepicker', 'wp-hotelier' ),
						'subdesc' => __( 'Hide the datepicker on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_hide_price' => array(
						'id'      => 'room_hide_price',
						'name'    => esc_html__( 'Hide room price', 'wp-hotelier' ),
						'subdesc' => __( 'Hide the price and related info on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_hide_details' => array(
						'id'      => 'room_hide_details',
						'name'    => esc_html__( 'Hide room details', 'wp-hotelier' ),
						'subdesc' => __( 'Hide details on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_hide_related' => array(
						'id'      => 'room_hide_related',
						'name'    => esc_html__( 'Hide related rooms', 'wp-hotelier' ),
						'subdesc' => __( 'Hide related rooms on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_hide_rates' => array(
						'id'      => 'room_hide_rates',
						'name'    => esc_html__( 'Hide room rates', 'wp-hotelier' ),
						'subdesc' => __( 'Hide rates on the room page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'datepicker_settings' => array(
						'id'    => 'datepicker_settings',
						'name'  => '<strong>' . esc_html__( 'Datepicker settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'datepicker_move_both_months' => array(
						'id'      => 'datepicker_move_both_months',
						'name'    => esc_html__( 'Move both months', 'wp-hotelier' ),
						'subdesc' => __( 'Move both months when selecting a date in the datepicker.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'datepicker_autoclose' => array(
						'id'      => 'datepicker_autoclose',
						'name'    => esc_html__( 'Autoclose', 'wp-hotelier' ),
						'subdesc' => __( 'Autoclose the datepicker after selecting the dates.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'listing_settings' => array(
						'id'    => 'listing_settings',
						'name'  => '<strong>' . esc_html__( 'Listing settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'listing_disabled' => array(
						'id'      => 'listing_disabled',
						'name'    => esc_html__( 'Disable listing page', 'wp-hotelier' ),
						'subdesc' => __( 'Disable the listing page completely. Useful if you decide to use the AJAX Room Booking widget in the single room pages and you do not consider necessary the listing page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'listing_sorting' => array(
						'id'      => 'listing_sorting',
						'name'    => esc_html__( 'Default room sorting', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_listing_sorting()
					),
					'low_room_threshold' => array(
						'id'   => 'low_room_threshold',
						'name' => esc_html__( 'Low room availability threshold', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'room_unavailable_visibility' => array(
						'id'      => 'room_unavailable_visibility',
						'name'    => esc_html__( 'Show rooms unavailable', 'wp-hotelier' ),
						'subdesc' => __( 'Show rooms that are unavailable for the selected dates.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'book_now_redirect_to_booking_page' => array(
						'id'           => 'book_now_redirect_to_booking_page',
						'name'         => esc_html__( 'Book now direct', 'wp-hotelier' ),
						'subdesc'      => __( 'Redirect to the booking page after successful addition (this will not allow multiple rooms on the same reservations).', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => 'book_now_allow_quantity_selection',
					),
					'book_now_allow_quantity_selection' => array(
						'id'      => 'book_now_allow_quantity_selection',
						'name'    => esc_html__( 'Allow quantity selection', 'wp-hotelier' ),
						'subdesc' => __( 'Allow quantity selection, then redirect to the booking page.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'room_images' => array(
						'id'    => 'room_images',
						'name'  => '<strong>' . esc_html__( 'Room images', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'room_images_description' => array(
						'id'   => 'room_images_description',
						'desc' => sprintf( __( 'These settings affect the display and dimensions of images in your website, but the display on the front-end will still be affected by the CSS of your theme. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-hotelier' ), esc_url( 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ) ),
						'type' => 'description'
					),
					'room_catalog_image_size' => array(
						'id'   => 'room_catalog_image_size',
						'name' => esc_html__( 'Catalog images', 'wp-hotelier' ),
						'desc' => __( 'This size is usually used when you list the rooms.', 'wp-hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '300',
							'height' => '300',
							'crop'   => 1
						),
					),
					'room_single_image_size' => array(
						'id'   => 'room_single_image_size',
						'name' => esc_html__( 'Single room image', 'wp-hotelier' ),
						'desc' => __( 'This size is the size used on the single room page.', 'wp-hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '600',
							'height' => '600',
							'crop'   => 1
						),
					),
					'room_thumbnail_image_size' => array(
						'id'   => 'room_thumbnail_image_size',
						'name' => esc_html__( 'Room thumbnails', 'wp-hotelier' ),
						'desc' => __( 'This size is usually used for the gallery of images on the room page.', 'wp-hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '75',
							'height' => '75',
							'crop'   => 1
						),
					),
					'room_lightbox' => array(
						'id'      => 'room_lightbox',
						'name'    => esc_html__( 'Enable lightbox for room images', 'wp-hotelier' ),
						'subdesc' => __( 'Room gallery images will open in a lightbox.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'std'     => true,
						'toggle'  => true,
					),
					'reservation_settings' => array(
						'id'    => 'reservation_settings',
						'name'  => '<strong>' . esc_html__( 'Reservation settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'booking_mode' => array(
						'id'      => 'booking_mode',
						'name'    => esc_html__( 'Booking mode', 'wp-hotelier' ),
						'desc'    => __( '<ul><li><strong>No booking</strong>Show only the room details.</li><li><strong>Manual booking</strong>Guests will be able to request a reservation and the admin will approve or reject the booking manually.</li><li><strong>Instant booking</strong>Guests will be able to make a reservation without manual approval from the admin.</li></ul>', 'wp-hotelier' ),
						'type'    => 'switch',
						'std'     => 'manual-booking',
						'options' => self::get_booking_mode_options()
					),
					'booking_admin_reservation_confirmed' => array(
						'id'      => 'booking_admin_reservation_confirmed',
						'name'    => esc_html__( 'Confirm admin reservations', 'wp-hotelier' ),
						'subdesc' => __( 'Confirm admin (manual) reservations immediately if they do not require payment.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'booking_additional_information' => array(
						'id'      => 'booking_additional_information',
						'name'    => esc_html__( 'Show additional information', 'wp-hotelier' ),
						'subdesc' => __( 'Show the "arrival estimated time" and the "special requests" field in the booking form.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'std'     => true,
						'toggle'  => true,
					),
					'booking_number_of_guests_selection' => array(
						'id'           => 'booking_number_of_guests_selection',
						'name'         => esc_html__( 'Show number of guests selection', 'wp-hotelier' ),
						'subdesc'      => __( 'Show a dropdown where the guest can select the number of adults and children for each room.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => 'booking_number_of_guests_selection_type',
					),
					'booking_number_of_guests_selection_type' => array(
						'id'      => 'booking_number_of_guests_selection_type',
						'name'    => esc_html__( 'Number of guests selection type', 'wp-hotelier' ),
						'desc' => __( 'Select where to show the guest selection. It is recommended to display the selection in the listing page when using "per person" extras. If the listing page is disabled, this option only takes effect on the booking page. It is always possible to show the selection via the AJAX Room Booking widget in the single room pages.', 'wp-hotelier' ),
						'std'     => 'booking-page',
						'type'    => 'switch',
						'options' => array(
							'booking-page' => esc_html__( 'Booking page', 'wp-hotelier' ),
							'listing-page' => esc_html__( 'Listing page', 'wp-hotelier' ),
						),
					),
					'booking_months_advance' => array(
						'id'   => 'booking_months_advance',
						'name' => esc_html__( 'Months in advance', 'wp-hotelier' ),
						'desc' => __( 'Only allow reservations for "XX" months from current date (0 unlimited).', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_arrival_date' => array(
						'id'   => 'booking_arrival_date',
						'name' => esc_html__( 'Arrival date', 'wp-hotelier' ),
						'desc' => __( 'Arrival date must be "XX" days from current date.', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_minimum_nights' => array(
						'id'   => 'booking_minimum_nights',
						'name' => esc_html__( 'Minimum nights', 'wp-hotelier' ),
						'desc' => __( 'Minimum number of nights a guest can book.', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '1'
					),
					'booking_maximum_nights' => array(
						'id'   => 'booking_maximum_nights',
						'name' => esc_html__( 'Maximum nights', 'wp-hotelier' ),
						'desc' => __( 'Maximum number of nights a guest can book (0 unlimited).', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_hold_minutes' => array(
						'id'   => 'booking_hold_minutes',
						'name' => esc_html__( 'Hold reservation (minutes)', 'wp-hotelier' ),
						'desc' => __( 'Hold reservation (for unpaid reservations that require a deposit) for "XX" minutes. When this limit is reached, the pending reservation will be cancelled. Type "0" to disable. Reservations created by admin will be not cancelled.', 'wp-hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '60'
					),
				)
			),
			/* Seasonal Prices Settings */
			'seasonal-prices' => apply_filters( 'hotelier_settings_seasonal_prices',
				array(
					'seasonal_prices_info' => array(
						'id'    => 'seasonal_prices_info',
						'name'  => '<strong>' . esc_html__( 'Seasonal prices schema', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'seasonal_prices_description' => array(
						'id'   => 'seasonal_prices_description',
						'desc' => __( 'Define here your global price schema, adding one rate for each date range. Rooms will have a default price (used when no rules are found) and a specific price for each season. To use this schema edit a room, select <em>Seasonal prices</em> as your <em>Price type</em> and enter the price amount of each season.', 'wp-hotelier' ),
						'type' => 'description'
					),
					'seasonal_prices_schema' => array(
						'id'   => 'seasonal_prices_schema',
						'name' => esc_html__( 'Price schema', 'wp-hotelier' ),
						'desc' => __( 'Each date range should have have a different price. The last rule defined overrides any previous rules. When you re-order the schema, remember to update the prices on already created rooms.', 'wp-hotelier' ),
						'type' => 'seasonal_prices_table'
					),
				)
			),
			/* Payment Settings */
			'payment' => apply_filters( 'hotelier_settings_payment',
				array(
					'payment_gateways' => array(
						'id'      => 'payment_gateways',
						'name'    => esc_html__( 'Payment gateways', 'wp-hotelier' ),
						'type'    => 'multi_checkbox',
						'options' => self::get_gateways()
					),
					'default_gateway' => array(
						'id'      => 'default_gateway',
						'name'    => esc_html__( 'Default gateway', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_gateways()
					),
				)
			),
			/* General Settings */
			'tax' => apply_filters( 'hotelier_settings_tax',
				array(
					'tax_info' => array(
						'id'    => 'tax_info',
						'name'  => '<strong>' . esc_html__( 'Tax settings', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'tax_enabled' => array(
						'id'                => 'tax_enabled',
						'name'              => esc_html__( 'Enable tax', 'wp-hotelier' ),
						'type'              => 'checkbox',
						'toggle'            => true,
						'show-if'           => true,
						'show-element'      => array(
							'tax_rate',
							'tax_in_deposit',
						)
					),
					'tax_rate' => array(
						'id'          => 'tax_rate',
						'name'        => esc_html__( 'Tax rate %', 'wp-hotelier' ),
						'desc'        => __( 'Enter a tax rate (percentage) to 4 decimal places. Use a point (.) for the decimal separator.', 'wp-hotelier' ),
						'type'        => 'percentage',
						'placeholder' => '5.0000',
					),
					'tax_in_deposit' => array(
						'id'                => 'tax_in_deposit',
						'name'              => esc_html__( 'Enable tax on deposits', 'wp-hotelier' ),
						'type'              => 'checkbox',
						'toggle'            => true,
					),
				)
			),
			/* Emails Settings */
			'emails' => apply_filters( 'hotelier_settings_emails',
				array(
					'emails_general_options' => array(
						'id'    => 'emails_general_options',
						'name'  => '<strong>' . esc_html__( 'Email options', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_admin_notice' => array(
						'id'       => 'emails_admin_notice',
						'name'     => esc_html__( 'Reservation notification emails', 'wp-hotelier' ),
						'desc'     => sprintf( __( 'Enter the email address(es) (comma separated) that should receive a notification anytime a reservation is made. Default to <code>%s</code>', 'wp-hotelier' ), get_option( 'admin_email' ) ),
						'type'     => 'email',
						'multiple' => true,
						'std'      => get_option( 'admin_email' ),
					),
					'emails_from_name' => array(
						'id'   => 'emails_from_name',
						'name' => esc_html__( '"From" name', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ),
					),
					'emails_from_email_address' => array(
						'id'   => 'emails_from_email_address',
						'name' => esc_html__( '"From" email address', 'wp-hotelier' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' ),
					),
					'emails_type' => array(
						'id'      => 'emails_type',
						'name'    => esc_html__( 'Email type', 'wp-hotelier' ),
						'type'    => 'select',
						'options' => self::get_emails_type_options(),
						'std'     => 'html'
					),
					'emails_logo' => array(
						'id'   => 'emails_logo',
						'name' => esc_html__( 'Email logo', 'wp-hotelier' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of Hotelier emails. Displayed on HTML emails only.', 'wp-hotelier' ),
						'type' => 'upload',
					),
					'emails_footer_text' => array(
						'id'   => 'emails_footer_text',
						'name' => esc_html__( 'Email footer text', 'wp-hotelier' ),
						'desc' => __( 'The text to appear in the footer of Hotelier emails.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ) . ' - ' . __( 'Powered by WP Hotelier', 'wp-hotelier' ),
					),
					'emails_new_reservation' => array(
						'id'    => 'emails_new_reservation',
						'name'  => '<strong>' . esc_html__( 'New reservation', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_new_reservation_description' => array(
						'id'   => 'emails_new_reservation_description',
						'desc' => __( 'New reservation emails are sent to the admin when a reservation (or a booking request in "manual" mode) is made.', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_new_reservation_enabled' => array(
						'id'           => 'emails_new_reservation_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_new_reservation_subject',
							'emails_new_reservation_heading',
						)
					),
					'emails_new_reservation_subject' => array(
						'id'   => 'emails_new_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - New hotel reservation #{reservation_number}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - New hotel reservation #{reservation_number}', 'wp-hotelier' ),
					),
					'emails_new_reservation_heading' => array(
						'id'   => 'emails_new_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'New hotel reservation', 'wp-hotelier' ),
					),
					'emails_request_received' => array(
						'id'    => 'emails_request_received',
						'name'  => '<strong>' . esc_html__( 'Request received', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_request_received_description' => array(
						'id'   => 'emails_request_received_description',
						'desc' => __( 'Request received emails are sent to guests when they request a booking ("manual" booking mode only).', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_request_received_enabled' => array(
						'id'           => 'emails_request_received_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_request_received_subject',
							'emails_request_received_heading',
						)
					),
					'emails_request_received_subject' => array(
						'id'   => 'emails_request_received_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-hotelier' ),
					),
					'emails_request_received_heading' => array(
						'id'   => 'emails_request_received_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Request received', 'wp-hotelier' ),
					),
					'emails_confirmed_reservation' => array(
						'id'    => 'emails_confirmed_reservation',
						'name'  => '<strong>' . esc_html__( 'Confirmed reservation', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_confirmed_reservation_description' => array(
						'id'   => 'emails_confirmed_reservation_description',
						'desc' => __( 'Reservation confirmed emails are sent to guests when their reservations are marked confirmed. By the admin (when sent manually) or automatically (after payment if required or immediately in "instant" booking mode).', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_confirmed_reservation_enabled' => array(
						'id'           => 'emails_confirmed_reservation_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_confirmed_reservation_subject',
							'emails_confirmed_reservation_heading',
						)
					),
					'emails_confirmed_reservation_subject' => array(
						'id'   => 'emails_confirmed_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-hotelier' ),
					),
					'emails_confirmed_reservation_heading' => array(
						'id'   => 'emails_confirmed_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Thank you for your reservation', 'wp-hotelier' ),
					),
					'emails_guest_invoice' => array(
						'id'    => 'emails_guest_invoice',
						'name'  => '<strong>' . esc_html__( 'Guest invoice', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_guest_invoice_description' => array(
						'id'   => 'emails_guest_invoice_description',
						'desc' => __( 'Guest invoice emails can be sent to guests containing their reservation information and payment links. Use these emails when you (the admin) create a reservation manually that requires a payment (deposit).', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_guest_invoice_enabled' => array(
						'id'           => 'emails_guest_invoice_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_guest_invoice_subject',
							'emails_guest_invoice_heading',
						)
					),
					'emails_guest_invoice_subject' => array(
						'id'   => 'emails_guest_invoice_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Invoice for reservation #{reservation_number}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'wp-hotelier' ),
					),
					'emails_guest_invoice_heading' => array(
						'id'   => 'emails_guest_invoice_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'wp-hotelier' ),
					),
					'emails_cancelled_reservation' => array(
						'id'    => 'emails_cancelled_reservation',
						'name'  => '<strong>' . esc_html__( 'Cancelled reservation', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_cancelled_reservation_description' => array(
						'id'   => 'emails_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to the admin when the guest cancels his reservation.', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_cancelled_reservation_enabled' => array(
						'id'           => 'emails_cancelled_reservation_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_cancelled_reservation_subject',
							'emails_cancelled_reservation_heading',
						)
					),
					'emails_cancelled_reservation_subject' => array(
						'id'   => 'emails_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - Cancelled reservation #{reservation_number}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - Cancelled reservation #{reservation_number}', 'wp-hotelier' ),
					),
					'emails_cancelled_reservation_heading' => array(
						'id'   => 'emails_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'wp-hotelier' ),
					),
					'emails_guest_cancelled_reservation' => array(
						'id'    => 'emails_guest_cancelled_reservation',
						'name'  => '<strong>' . esc_html__( 'Guest cancelled reservation', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'emails_guest_cancelled_reservation_description' => array(
						'id'   => 'emails_guest_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to guests when reservations have been marked cancelled.', 'wp-hotelier' ),
						'type' => 'description'
					),
					'emails_guest_cancelled_reservation_enabled' => array(
						'id'           => 'emails_guest_cancelled_reservation_enabled',
						'name'         => esc_html__( 'Enable/disable', 'wp-hotelier' ),
						'subdesc'      => __( 'Enable this email notification.', 'wp-hotelier' ),
						'type'         => 'checkbox',
						'std'          => true,
						'toggle'       => true,
						'show-if'      => true,
						'show-element' => array(
							'emails_guest_cancelled_reservation_subject',
							'emails_guest_cancelled_reservation_heading',
						)
					),
					'emails_guest_cancelled_reservation_subject' => array(
						'id'   => 'emails_guest_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-hotelier' ),
					),
					'emails_guest_cancelled_reservation_heading' => array(
						'id'   => 'emails_guest_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'wp-hotelier' ),
					),
				)
			),
			/* License Settings */
			'licenses' => apply_filters( 'hotelier_settings_licenses',
				array()
			),
			/* Tools Settings */
			'tools' => apply_filters( 'hotelier_settings_tools',
				array(
					'install_pages' => array(
						'id'   => 'install_pages',
						'name' => esc_html__( 'Install Hotelier pages', 'wp-hotelier' ),
						'desc' => __( 'This tool will install all the missing Hotelier pages. Pages already defined and set up will not be replaced.', 'wp-hotelier' ),
						'type' => 'tool_button'
					),
					'send_test_email' => array(
						'id'   => 'send_test_email',
						'name' => esc_html__( 'Send test email', 'wp-hotelier' ),
						'desc' => __( 'Test if your WordPress installation is sending emails correctly.', 'wp-hotelier' ),
						'type' => 'tool_button'
					),
					'template_debug_mode' => array(
						'id'      => 'template_debug_mode',
						'name'    => esc_html__( 'Template debug mode', 'wp-hotelier' ),
						'subdesc' => __( 'This tool will disable template overrides for logged-in administrators for debugging purposes.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'clear_sessions' => array(
						'id'   => 'clear_sessions',
						'name' => esc_html__( 'Cleanup guest sessions', 'wp-hotelier' ),
						'desc' => __( 'This tool will delete all guest session data from the database (including any current live booking).', 'wp-hotelier' ),
						'type' => 'tool_button'
					),
					'delete_completed_bookings' => array(
						'id'   => 'delete_completed_bookings',
						'name' => esc_html__( 'Delete completed bookings', 'wp-hotelier' ),
						'desc' => __( 'This tool will delete all completed bookings from the database.', 'wp-hotelier' ),
						'type' => 'tool_button'
					),
					'remove_data_uninstall' => array(
						'id'      => 'remove_data_uninstall',
						'name'    => esc_html__( 'Remove data on uninstall', 'wp-hotelier' ),
						'subdesc' => __( 'This tool will remove all Hotelier, Rooms and Reservations data when using the "Delete" link on the plugins screen.', 'wp-hotelier' ),
						'type'    => 'checkbox',
						'toggle'  => true,
					),
					'server_settings' => array(
						'id'    => 'server_settings',
						'name'  => '<strong>' . esc_html__( 'Server settings & info', 'wp-hotelier' ) . '</strong>',
						'type'  => 'header',
						'class' => 'htl-ui-row--section-description'
					),
					'hotelier_version' => array(
						'id'   => 'hotelier_version',
						'name' => esc_html__( 'WP Hotelier version', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'theme_name' => array(
						'id'   => 'theme_name',
						'name' => esc_html__( 'Theme name', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'theme_version' => array(
						'id'   => 'theme_version',
						'name' => esc_html__( 'Theme version', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'parent_theme_name' => array(
						'id'   => 'parent_theme_name',
						'name' => esc_html__( 'Parent theme name', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'parent_theme_version' => array(
						'id'   => 'parent_theme_version',
						'name' => esc_html__( 'Parent theme version', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'listing_page_info' => array(
						'id'   => 'listing_page_info',
						'name' => esc_html__( 'Listing page', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'booking_page_info' => array(
						'id'   => 'booking_page_info',
						'name' => esc_html__( 'Booking page', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'server_info' => array(
						'id'   => 'server_info',
						'name' => esc_html__( 'Server info', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'php_version' => array(
						'id'   => 'php_version',
						'name' => esc_html__( 'PHP version', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'wp_memory_limit' => array(
						'id'   => 'wp_memory_limit',
						'name' => esc_html__( 'WP memory limit', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'wp_debug' => array(
						'id'   => 'wp_debug',
						'name' => esc_html__( 'WP debug', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'php_post_max_size' => array(
						'id'   => 'php_post_max_size',
						'name' => esc_html__( 'PHP post max size', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'php_post_max_upload_size' => array(
						'id'   => 'php_post_max_upload_size',
						'name' => esc_html__( 'PHP max upload size', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'php_time_limit' => array(
						'id'   => 'php_time_limit',
						'name' => esc_html__( 'PHP time limit', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'php_max_input_vars' => array(
						'id'   => 'php_max_input_vars',
						'name' => esc_html__( 'PHP max input vars', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'fsockopen_cURL' => array(
						'id'   => 'fsockopen_cURL',
						'name' => esc_html__( 'fsockopen/cURL', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'domdocument' => array(
						'id'   => 'domdocument',
						'name' => esc_html__( 'DOMDocument', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'mbstring' => array(
						'id'   => 'mbstring',
						'name' => esc_html__( 'Multibyte string (mbstring)', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
					'log_directory_writable' => array(
						'id'   => 'log_directory_writable',
						'name' => esc_html__( 'Log directory writable', 'wp-hotelier' ),
						'type' => 'info',
						'class' => 'htl-ui-row--server-info'
					),
				)
			),
		);

		return apply_filters( 'hotelier_settings_fields', $settings );
	}
}

endif;
