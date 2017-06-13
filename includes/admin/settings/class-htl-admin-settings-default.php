<?php
/**
 * Hotelier Default Settings.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin
 * @version  1.0.0
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
			'no-booking'      => esc_html__( 'No booking', 'hotelier' ),
			'manual-booking'  => esc_html__( 'Manual booking', 'hotelier' ),
			'instant-booking' => esc_html__( 'Instant booking ', 'hotelier' )
		);

		return apply_filters( 'hotelier_booking_mode_options', $options );
	}

	/**
	 * Get emails type options.
	 */
	public static function get_emails_type_options() {
		$types = array(
			'plain'     => esc_html__( 'Plain text', 'hotelier' ),
			'html'      => esc_html__( 'HTML', 'hotelier' ),
			'multipart' => esc_html__( 'Multipart text', 'hotelier' )
		);

		return $types;
	}

	/**
	 * Get listing sorting options.
	 */
	public static function get_listing_sorting() {
		$options = array(
			'menu_order' => esc_html__( 'Menu order', 'hotelier' ),
			'date'       => esc_html__( 'Sort by most recent', 'hotelier' ),
			'title'      => esc_html__( 'Sort by title', 'hotelier' ),
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
						'id'   => 'hotelier_info',
						'name' => '<strong>' . esc_html__( 'Hotel info', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'hotel_name' => array(
						'id'   => 'hotel_name',
						'name' => esc_html__( 'Hotel name', 'hotelier' ),
						'desc' => __( 'The name of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_address' => array(
						'id'   => 'hotel_address',
						'name' => esc_html__( 'Hotel address', 'hotelier' ),
						'desc' => __( 'The address of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_postcode' => array(
						'id'   => 'hotel_postcode',
						'name' => esc_html__( 'Hotel postcode', 'hotelier' ),
						'desc' => __( 'The postcode/zip of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_locality' => array(
						'id'   => 'hotel_locality',
						'name' => esc_html__( 'Hotel locality', 'hotelier' ),
						'desc' => __( 'The locality of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_telephone' => array(
						'id'   => 'hotel_telephone',
						'name' => esc_html__( 'Hotel telephone', 'hotelier' ),
						'desc' => __( 'The telephone number of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_fax' => array(
						'id'   => 'hotel_fax',
						'name' => esc_html__( 'Hotel fax', 'hotelier' ),
						'desc' => __( 'The fax number of your hotel.', 'hotelier' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_email' => array(
						'id'   => 'hotel_email',
						'name' => esc_html__( 'Hotel email', 'hotelier' ),
						'desc' => __( 'The email address of your hotel.', 'hotelier' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' )
					),
					'hotel_checkin' => array(
						'id'   => 'hotel_checkin',
						'name' => esc_html__( 'Check-in', 'hotelier' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '12',
							'to' => '21',
						),
					),
					'hotel_checkout' => array(
						'id'   => 'hotel_checkout',
						'name' => esc_html__( 'Check-out', 'hotelier' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '8',
							'to' => '13',
						),
					),
					'hotel_pets' => array(
						'id'   => 'hotel_pets',
						'name' => esc_html__( 'Pets', 'hotelier' ),
						'desc' => __( 'Are pets allowed?', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => ''
					),
					'hotel_pets_message' => array(
						'id'   => 'hotel_pets_message',
						'name' => esc_html__( 'Pets instructions', 'hotelier' ),
						'desc' => __( 'If you need to give some special instructions, use this field.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Pets are allowed', 'hotelier' ),
					),
					'hotel_accepted_cards' => array(
						'id'   => 'hotel_accepted_cards',
						'name' => esc_html__( 'Accepted credit cards', 'hotelier' ),
						'desc' => sprintf( __( 'The accepted credit cards for payments made at the hotel. These are not used to pay the deposit (configure the <a href="%s">Payment Gateways</a> settings for that).', 'hotelier' ), '?page=hotelier-settings&tab=payment' ),
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
						'id'   => 'hotelier_pages',
						'name' => '<strong>' . esc_html__( 'Hotelier pages', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'listing_page' => array(
						'id'      => 'listing_page',
						'name'    => esc_html__( 'Listing page', 'hotelier' ),
						'desc'    => __( 'This is the page (the listing page) where guests will see the rooms that are available for the selected dates. The [hotelier_listing] shortcode must be on this page.', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'booking_page' => array(
						'id'      => 'booking_page',
						'name'    => esc_html__( 'Booking page', 'hotelier' ),
						'desc'    => __( 'This is the booking page where guests will complete their reservations. The [hotelier_booking] shortcode must be on this page.', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'terms_page' => array(
						'id'      => 'terms_page',
						'name'    => esc_html__( 'Terms page', 'hotelier' ),
						'desc'    => __( 'If set, guests will be asked to agree to the hotel terms before to request a new reservation.', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'enforce_ssl_booking' => array(
						'id'   => 'enforce_ssl_booking',
						'name' => esc_html__( 'Enforce SSL booking', 'hotelier' ),
						'desc' => __( 'Enforce SSL (HTTPS) on the booking page (you must have an SSL certificate installed to use this option).', 'hotelier' ),
						'type' => 'checkbox'
					),
					'unforce_ssl_booking' => array(
						'id'   => 'unforce_ssl_booking',
						'name' => esc_html__( 'Force HTTP leaving booking', 'hotelier' ),
						'desc' => __( 'Force HTTP when leaving the booking page.', 'hotelier' ),
						'type' => 'checkbox'
					),
					'hotelier_endpoints' => array(
						'id'   => 'hotelier_endpoints',
						'name' => '<strong>' . esc_html__( 'Hotelier endpoints', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'reservation_received' => array(
						'id'      => 'reservation_received',
						'name'    => esc_html__( 'Reservation received', 'hotelier' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the page guests are sent to after completing their reservation.', 'hotelier' ),
						'type'    => 'text',
						'std'     => 'reservation-received'
					),
					'pay_endpoint' => array(
						'id'      => 'pay_endpoint',
						'name'    => esc_html__( 'Pay reservation', 'hotelier' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the payment form (for reservations generated by the admin).', 'hotelier' ),
						'type'    => 'text',
						'std'     => 'pay-reservation'
					),
					'currency_settings' => array(
						'id'   => 'currency_settings',
						'name' => '<strong>' . esc_html__( 'Currency settings', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'currency' => array(
						'id'      => 'currency',
						'name'    => esc_html__( 'Currency', 'hotelier' ),
						'desc'    => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'hotelier' ),
						'type'    => 'select',
						'options' => htl_get_currencies()
					),
					'currency_position' => array(
						'id'      => 'currency_position',
						'name'    => esc_html__( 'Currency position', 'hotelier' ),
						'desc'    => __( 'Choose the location of the currency sign.', 'hotelier' ),
						'type'    => 'select',
						'options' => array(
							'before' => esc_html__( 'Before - $10', 'hotelier' ),
							'after'  => esc_html__( 'After - 10$', 'hotelier' )
						)
					),
					'thousands_separator' => array(
						'id'   => 'thousands_separator',
						'name' => esc_html__( 'Thousands separator', 'hotelier' ),
						'desc' => __( 'This sets the thousand separator (usually , or .) of displayed prices.', 'hotelier' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => ','
					),
					'decimal_separator' => array(
						'id'   => 'decimal_separator',
						'name' => esc_html__( 'Decimal separator', 'hotelier' ),
						'desc' => __( 'This sets the decimal separator (usually , or .) of displayed prices.', 'hotelier' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => '.'
					),
					'price_num_decimals' => array(
						'id'   => 'price_num_decimals',
						'name' => esc_html__( 'Number of decimals', 'hotelier' ),
						'desc' => __( 'This sets the number of decimals points shown in displayed prices.', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
				)
			),
			/* Room Settings */
			'rooms-and-reservations' => apply_filters( 'hotelier_settings_rooms_and_reservations',
				array(
					'room_settings' => array(
						'id'   => 'room_settings',
						'name' => '<strong>' . esc_html__( 'Room settings', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'room_size_unit' => array(
						'id'      => 'room_size_unit',
						'name'    => esc_html__( 'Room size unit', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_room_size_options()
					),
					'listing_settings' => array(
						'id'   => 'listing_settings',
						'name' => '<strong>' . esc_html__( 'Listing settings', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'listing_sorting' => array(
						'id'      => 'listing_sorting',
						'name'    => esc_html__( 'Default room sorting', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_listing_sorting()
					),
					'low_room_threshold' => array(
						'id'   => 'low_room_threshold',
						'name' => esc_html__( 'Low room availability threshold', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'room_unavailable_visibility' => array(
						'id'   => 'room_unavailable_visibility',
						'name' => esc_html__( 'Show rooms unavailable', 'hotelier' ),
						'desc' => __( 'Show rooms that are unavailable for the selected dates.', 'hotelier' ),
						'type' => 'checkbox'
					),
					'room_images' => array(
						'id'   => 'room_images',
						'name' => '<strong>' . esc_html__( 'Room images', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'room_images_description' => array(
						'id'   => 'room_images_description',
						'desc' => sprintf( __( 'These settings affect the display and dimensions of images in your website, but the display on the front-end will still be affected by the CSS of your theme. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'hotelier' ), esc_url( 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ) ),
						'type' => 'description'
					),
					'room_catalog_image_size' => array(
						'id'   => 'room_catalog_image_size',
						'name' => esc_html__( 'Catalog images', 'hotelier' ),
						'desc' => __( 'This size is usually used when you list the rooms.', 'hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '300',
							'height' => '300',
							'crop'   => 1
						),
					),
					'room_single_image_size' => array(
						'id'   => 'room_single_image_size',
						'name' => esc_html__( 'Single room image', 'hotelier' ),
						'desc' => __( 'This size is the size used on the single room page.', 'hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '600',
							'height' => '600',
							'crop'   => 1
						),
					),
					'room_thumbnail_image_size' => array(
						'id'   => 'room_thumbnail_image_size',
						'name' => esc_html__( 'Room thumbnails', 'hotelier' ),
						'desc' => __( 'This size is usually used for the gallery of images on the room page.', 'hotelier' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '75',
							'height' => '75',
							'crop'   => 1
						),
					),
					'room_lightbox' => array(
						'id'   => 'room_lightbox',
						'name' => esc_html__( 'Enable lightbox for room images', 'hotelier' ),
						'desc' => __( 'Room gallery images will open in a lightbox.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true
					),
					'reservation_settings' => array(
						'id'   => 'reservation_settings',
						'name' => '<strong>' . esc_html__( 'Reservation settings', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'booking_mode' => array(
						'id'      => 'booking_mode',
						'name'    => esc_html__( 'Booking mode', 'hotelier' ),
						'desc'    => __( '<ul><li><strong>No booking</strong>Show only the room details.</li><li><strong>Manual booking</strong>Guests will be able to request a reservation and the admin will approve or reject the booking manually.</li><li><strong>Instant booking</strong>Guests will be able to make a reservation without manual approval from the admin.</li></ul>', 'hotelier' ),
						'type'    => 'radio',
						'std'     => 'manual-booking',
						'options' => self::get_booking_mode_options()
					),
					'booking_additional_information' => array(
						'id'   => 'booking_additional_information',
						'name' => esc_html__( 'Show additional information', 'hotelier' ),
						'desc' => __( 'Show the "arrival estimated time" and the "special requests" field in the booking form.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'booking_months_advance' => array(
						'id'   => 'booking_months_advance',
						'name' => esc_html__( 'Months in advance', 'hotelier' ),
						'desc' => __( 'Only allow reservations for "XX" months from current date (0 unlimited).', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_arrival_date' => array(
						'id'   => 'booking_arrival_date',
						'name' => esc_html__( 'Arrival date', 'hotelier' ),
						'desc' => __( 'Arrival date must be "XX" days from current date.', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_minimum_nights' => array(
						'id'   => 'booking_minimum_nights',
						'name' => esc_html__( 'Minimum nights', 'hotelier' ),
						'desc' => __( 'Minimum number of nights a guest can book.', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '1'
					),
					'booking_maximum_nights' => array(
						'id'   => 'booking_maximum_nights',
						'name' => esc_html__( 'Maximum nights', 'hotelier' ),
						'desc' => __( 'Maximum number of nights a guest can book (0 unlimited).', 'hotelier' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_hold_minutes' => array(
						'id'   => 'booking_hold_minutes',
						'name' => esc_html__( 'Hold reservation (minutes)', 'hotelier' ),
						'desc' => __( 'Hold reservation (for unpaid reservations that require a deposit) for "XX" minutes. When this limit is reached, the pending reservation will be cancelled. Type "0" to disable. Reservations created by admin are not cancelled.', 'hotelier' ),
						'type' => 'booking_hold_minutes',
						'size' => 'small',
						'std'  => '60'
					),
				)
			),
			/* Seasonal Prices Settings */
			'seasonal-prices' => apply_filters( 'hotelier_settings_seasonal_prices',
				array(
					'seasonal_prices_info' => array(
						'id'   => 'seasonal_prices_info',
						'name' => '<strong>' . esc_html__( 'Seasonal prices schema', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'seasonal_prices_description' => array(
						'id'   => 'seasonal_prices_description',
						'desc' => __( 'Define here your global price schema, adding one rate for each date range. Rooms will have a default price (used when no rules are found) and a specific price for each season. To use this schema edit a room, select <em>Seasonal prices</em> in the <em>Price</em> dropdown, and enter the price amount of each season.', 'hotelier' ),
						'type' => 'description'
					),
					'seasonal_prices_schema' => array(
						'id'   => 'seasonal_prices_schema',
						'name' => esc_html__( 'Price schema', 'hotelier' ),
						'desc' => __( 'Each date range should have have a different price. The last rule defined overrides any previous rules.', 'hotelier' ),
						'type' => 'seasonal_prices_table'
					),
				)
			),
			/* Payment Settings */
			'payment' => apply_filters( 'hotelier_settings_payment',
				array(
					'payment_gateways' => array(
						'id'   => 'payment_gateways',
						'name' => esc_html__( 'Payment gateways', 'hotelier' ),
						'type' => 'gateways',
						'options' => HTL()->payment_gateways()->payment_gateways()
					),
					'default_gateway' => array(
						'id'   => 'default_gateway',
						'name' => esc_html__( 'Default gateway', 'hotelier' ),
						'type' => 'gateway_select',
						'options' => HTL()->payment_gateways()->payment_gateways()
					)
				)
			),
			/* Emails Settings */
			'emails' => apply_filters( 'hotelier_settings_emails',
				array(
					'emails_general_options' => array(
						'id'   => 'emails_general_options',
						'name' => '<strong>' . esc_html__( 'Email options', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_admin_notice' => array(
						'id'       => 'emails_admin_notice',
						'name'     => esc_html__( 'Reservation notification emails', 'hotelier' ),
						'desc'     => sprintf( __( 'Enter the email address(es) (comma separated) that should receive a notification anytime a reservation is made. Default to <code>%s</code>', 'hotelier' ), get_option( 'admin_email' ) ),
						'type'     => 'email',
						'multiple' => true,
						'std'      => get_option( 'admin_email' ),
					),
					'emails_from_name' => array(
						'id'   => 'emails_from_name',
						'name' => esc_html__( '"From" name', 'hotelier' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ),
					),
					'emails_from_email_address' => array(
						'id'   => 'emails_from_email_address',
						'name' => esc_html__( '"From" email address', 'hotelier' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' ),
					),
					'emails_type' => array(
						'id'      => 'emails_type',
						'name'    => esc_html__( 'Email type', 'hotelier' ),
						'type'    => 'select',
						'options' => self::get_emails_type_options(),
						'std'     => 'html'
					),
					'emails_logo' => array(
						'id'   => 'emails_logo',
						'name' => esc_html__( 'Email logo', 'hotelier' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of Hotelier emails. Displayed on HTML emails only.', 'hotelier' ),
						'type' => 'upload',
					),
					'emails_footer_text' => array(
						'id'   => 'emails_footer_text',
						'name' => esc_html__( 'Email footer text', 'hotelier' ),
						'desc' => __( 'The text to appear in the footer of Hotelier emails.', 'hotelier' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ) . ' - ' . __( 'Powered by WP Hotelier', 'hotelier' ),
					),
					'emails_new_reservation' => array(
						'id'   => 'emails_new_reservation',
						'name' => '<strong>' . esc_html__( 'New reservation', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_new_reservation_description' => array(
						'id'   => 'emails_new_reservation_description',
						'desc' => __( 'New reservation emails are sent to the admin when a reservation (or a booking request in "manual" mode) is made.', 'hotelier' ),
						'type' => 'description'
					),
					'emails_new_reservation_enabled' => array(
						'id'   => 'emails_new_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_new_reservation_subject' => array(
						'id'   => 'emails_new_reservation_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - New hotel reservation #{reservation_number}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - New hotel reservation #{reservation_number}', 'hotelier' ),
					),
					'emails_new_reservation_heading' => array(
						'id'   => 'emails_new_reservation_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'New hotel reservation', 'hotelier' ),
					),
					'emails_request_received' => array(
						'id'   => 'emails_request_received',
						'name' => '<strong>' . esc_html__( 'Request received', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_request_received_description' => array(
						'id'   => 'emails_request_received_description',
						'desc' => __( 'Request received emails are sent to guests when they request a booking ("manual" booking mode only).', 'hotelier' ),
						'type' => 'description'
					),
					'emails_request_received_enabled' => array(
						'id'   => 'emails_request_received_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_request_received_subject' => array(
						'id'   => 'emails_request_received_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'hotelier' ),
					),
					'emails_request_received_heading' => array(
						'id'   => 'emails_request_received_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Request received', 'hotelier' ),
					),
					'emails_confirmed_reservation' => array(
						'id'   => 'emails_confirmed_reservation',
						'name' => '<strong>' . esc_html__( 'Confirmed reservation', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_confirmed_reservation_description' => array(
						'id'   => 'emails_confirmed_reservation_description',
						'desc' => __( 'Reservation confirmed emails are sent to guests when their reservations are marked confirmed. By the admin (when sent manually) or automatically (after payment if required or immediately in "instant" booking mode).', 'hotelier' ),
						'type' => 'description'
					),
					'emails_confirmed_reservation_enabled' => array(
						'id'   => 'emails_confirmed_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_confirmed_reservation_subject' => array(
						'id'   => 'emails_confirmed_reservation_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'hotelier' ),
					),
					'emails_confirmed_reservation_heading' => array(
						'id'   => 'emails_confirmed_reservation_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Thank you for your reservation', 'hotelier' ),
					),
					'emails_guest_invoice' => array(
						'id'   => 'emails_guest_invoice',
						'name' => '<strong>' . esc_html__( 'Guest invoice', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_guest_invoice_description' => array(
						'id'   => 'emails_guest_invoice_description',
						'desc' => __( 'Guest invoice emails can be sent to guests containing their reservation information and payment links. Use these emails when you (the admin) create a reservation manually that requires a payment (deposit).', 'hotelier' ),
						'type' => 'description'
					),
					'emails_guest_invoice_enabled' => array(
						'id'   => 'emails_guest_invoice_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_guest_invoice_subject' => array(
						'id'   => 'emails_guest_invoice_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Invoice for reservation #{reservation_number}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'hotelier' ),
					),
					'emails_guest_invoice_heading' => array(
						'id'   => 'emails_guest_invoice_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'hotelier' ),
					),
					'emails_cancelled_reservation' => array(
						'id'   => 'emails_cancelled_reservation',
						'name' => '<strong>' . esc_html__( 'Cancelled reservation', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_cancelled_reservation_description' => array(
						'id'   => 'emails_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to the admin when the guest cancels his reservation.', 'hotelier' ),
						'type' => 'description'
					),
					'emails_cancelled_reservation_enabled' => array(
						'id'   => 'emails_cancelled_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_cancelled_reservation_subject' => array(
						'id'   => 'emails_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - Cancelled reservation #{reservation_number}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - Cancelled reservation #{reservation_number}', 'hotelier' ),
					),
					'emails_cancelled_reservation_heading' => array(
						'id'   => 'emails_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'hotelier' ),
					),



					'emails_guest_cancelled_reservation' => array(
						'id'   => 'emails_guest_cancelled_reservation',
						'name' => '<strong>' . esc_html__( 'Guest cancelled reservation', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'emails_guest_cancelled_reservation_description' => array(
						'id'   => 'emails_guest_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to guests when reservations have been marked cancelled.', 'hotelier' ),
						'type' => 'description'
					),
					'emails_guest_cancelled_reservation_enabled' => array(
						'id'   => 'emails_guest_cancelled_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'hotelier' ),
						'desc' => __( 'Enable this email notification.', 'hotelier' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_guest_cancelled_reservation_subject' => array(
						'id'   => 'emails_guest_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'hotelier' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'hotelier' ),
					),
					'emails_guest_cancelled_reservation_heading' => array(
						'id'   => 'emails_guest_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'hotelier' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'hotelier' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'hotelier' ),
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
						'name' => esc_html__( 'Install Hotelier pages', 'hotelier' ),
						'desc' => __( 'This tool will install all the missing Hotelier pages. Pages already defined and set up will not be replaced.', 'hotelier' ),
						'type' => 'button'
					),
					'send_test_email' => array(
						'id'   => 'send_test_email',
						'name' => esc_html__( 'Send test email', 'hotelier' ),
						'desc' => __( 'Test if your WordPress installation is sending emails correctly.', 'hotelier' ),
						'type' => 'button'
					),
					'template_debug_mode' => array(
						'id'   => 'template_debug_mode',
						'name' => esc_html__( 'Template debug mode', 'hotelier' ),
						'desc' => __( 'This tool will disable template overrides for logged-in administrators for debugging purposes.', 'hotelier' ),
						'type' => 'checkbox'
					),
					'clear_sessions' => array(
						'id'   => 'clear_sessions',
						'name' => esc_html__( 'Cleanup guest sessions', 'hotelier' ),
						'desc' => __( 'This tool will delete all guest session data from the database (including any current live booking).', 'hotelier' ),
						'type' => 'button'
					),
					'delete_completed_bookings' => array(
						'id'   => 'delete_completed_bookings',
						'name' => esc_html__( 'Delete completed bookings', 'hotelier' ),
						'desc' => __( 'This tool will delete all completed bookings from the database.', 'hotelier' ),
						'type' => 'button'
					),
					'remove_data_uninstall' => array(
						'id'   => 'remove_data_uninstall',
						'name' => esc_html__( 'Remove data on uninstall', 'hotelier' ),
						'desc' => __( 'This tool will remove all Hotelier, Rooms and Reservations data when using the "Delete" link on the plugins screen.', 'hotelier' ),
						'type' => 'checkbox'
					),
					'server_settings' => array(
						'id'   => 'server_settings',
						'name' => '<strong>' . esc_html__( 'Server settings & info', 'hotelier' ) . '</strong>',
						'type' => 'header'
					),
					'hotelier_version' => array(
						'id'   => 'hotelier_version',
						'name' => esc_html__( 'Hotelier version', 'hotelier' ),
						'type' => 'info'
					),
					'theme_name' => array(
						'id'   => 'theme_name',
						'name' => esc_html__( 'Theme name', 'hotelier' ),
						'type' => 'info'
					),
					'theme_version' => array(
						'id'   => 'theme_version',
						'name' => esc_html__( 'Theme version', 'hotelier' ),
						'type' => 'info'
					),
					'parent_theme_name' => array(
						'id'   => 'parent_theme_name',
						'name' => esc_html__( 'Parent theme name', 'hotelier' ),
						'type' => 'info'
					),
					'parent_theme_version' => array(
						'id'   => 'parent_theme_version',
						'name' => esc_html__( 'Parent theme version', 'hotelier' ),
						'type' => 'info'
					),
					'listing_page_info' => array(
						'id'   => 'listing_page_info',
						'name' => esc_html__( 'Listing page', 'hotelier' ),
						'type' => 'info'
					),
					'booking_page_info' => array(
						'id'   => 'booking_page_info',
						'name' => esc_html__( 'Booking page', 'hotelier' ),
						'type' => 'info'
					),
					'server_info' => array(
						'id'   => 'server_info',
						'name' => esc_html__( 'Server info', 'hotelier' ),
						'type' => 'info'
					),
					'php_version' => array(
						'id'   => 'php_version',
						'name' => esc_html__( 'PHP version', 'hotelier' ),
						'type' => 'info'
					),
					'wp_memory_limit' => array(
						'id'   => 'wp_memory_limit',
						'name' => esc_html__( 'WP memory limit', 'hotelier' ),
						'type' => 'info'
					),
					'wp_debug' => array(
						'id'   => 'wp_debug',
						'name' => esc_html__( 'WP debug', 'hotelier' ),
						'type' => 'info'
					),
					'php_post_max_size' => array(
						'id'   => 'php_post_max_size',
						'name' => esc_html__( 'PHP post max size', 'hotelier' ),
						'type' => 'info'
					),
					'php_post_max_upload_size' => array(
						'id'   => 'php_post_max_upload_size',
						'name' => esc_html__( 'PHP max upload size', 'hotelier' ),
						'type' => 'info'
					),
					'php_time_limit' => array(
						'id'   => 'php_time_limit',
						'name' => esc_html__( 'PHP time limit', 'hotelier' ),
						'type' => 'info'
					),
					'php_max_input_vars' => array(
						'id'   => 'php_max_input_vars',
						'name' => esc_html__( 'PHP max input vars', 'hotelier' ),
						'type' => 'info'
					),
					'fsockopen_cURL' => array(
						'id'   => 'fsockopen_cURL',
						'name' => esc_html__( 'fsockopen/cURL', 'hotelier' ),
						'type' => 'info'
					),
					'domdocument' => array(
						'id'   => 'domdocument',
						'name' => esc_html__( 'DOMDocument', 'hotelier' ),
						'type' => 'info'
					),
					'log_directory_writable' => array(
						'id'   => 'log_directory_writable',
						'name' => esc_html__( 'Log directory writable', 'hotelier' ),
						'type' => 'info'
					),
				)
			),
		);

		return apply_filters( 'hotelier_settings_fields', $settings );
	}
}

endif;
