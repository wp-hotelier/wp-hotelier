<?php
/**
 * Room Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Room' ) ) :

/**
 * HTL_Room Class
 */
class HTL_Room {
	/**
	 * The room (post) ID.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * $post Stores post data
	 *
	 * @var $post WP_Post
	 */
	public $post = null;

	/**
	 * Get things going
	 */
	public function __construct( $room ) {
		if ( is_numeric( $room ) ) {
			$this->id   = absint( $room );
			$this->post = get_post( $this->id );
		} elseif ( $room instanceof HTL_Room ) {
			$this->id   = absint( $room->id );
			$this->post = $room->post;
		} elseif ( isset( $room->ID ) ) {
			$this->id   = absint( $room->ID );
			$this->post = $room;
		}
	}

	/**
	 * __get function.
	 *
	 * @since 2.2
	 */
	public function __get( $key ) {
		$value = get_post_meta( $this->id, '_' . $key, true );

		if ( in_array( $key, array( 'max_guests' ) ) ) {
			$value = $value ? $value : 1;

		} elseif ( 'max_children' === $key ) {
			$value = $value ? $value : 0;

		}

		if ( ! empty( $value ) ) {
			$this->$key = $value;
		}

		return $value;
	}

	/**
	 * Get the room's post data.
	 *
	 * @return object
	 */
	public function get_post_data() {
		return $this->post;
	}

	/**
	 * Returns whether or not the room post exists.
	 *
	 * @return bool
	 */
	public function exists() {
		return empty( $this->post ) ? false : true;
	}

	/**
	 * Returns whether or not the room is visible.
	 *
	 * @param bool $show_out_of_stock
	 *
	 * @return bool
	 */
	public function is_visible( $show_out_of_stock = false ) {
		$visible = true;

		if ( ! $this->post ) {
			$visible = false;

		// Published/private
		} elseif ( $this->post->post_status !== 'publish' ) {
			$visible = false;

		// Out of stock visibility
		} elseif ( $this->get_stock_rooms() === 0 ) {
			$visible = $show_out_of_stock ? true : false;
		}

		return apply_filters( 'hotelier_room_is_visible', $visible, $this->id );
	}

	/**
	 * Get the title of the post.
	 *
	 * @return string
	 */
	public function get_title() {
		$title = $this->post->post_title;

		return apply_filters( 'hotelier_room_title', $title, $this );
	}

	/**
	 * Get room gallery ids.
	 *
	 * @return array
	 */
	public function get_gallery_attachment_ids() {
		return apply_filters( 'hotelier_room_gallery_attachment_ids', array_filter( (array) explode( ',', $this->room_image_gallery ) ), $this );
	}

	/**
	 * Returns the room's type.
	 *
	 * @return string type
	 */
	public function get_room_type() {
		return $this->room_type;
	}

	/**
	 * Returns the room's type formatted.
	 *
	 * @return string type
	 */
	public function get_room_type_formatted() {
		$room_type_formatted = explode( '_', $this->room_type );

		return ucfirst( $room_type_formatted[ 0 ] );
	}

	/**
	 * Checks if a room is variable.
	 *
	 * @return bool
	 */
	public function is_variable_room() {
		return $this->get_room_type() == 'variable_room' ? true : false;
	}

	/**
	 * Checks if the rate exists on the room_rate taxonomy.
	 *
	 * @param string $rate_name Room rate
	 *
	 * @return bool
	 */
	public function rate_term_exists( $rate_name ) {
		$get_room_rates = get_terms( 'room_rate', 'hide_empty=0' );

		if ( empty( $get_room_rates ) || is_wp_error( $get_room_rates ) ) {
			// room_rate taxonomy empty
			return false;
		}

		$term = term_exists( $rate_name, 'room_rate' );

		if ( $term !== 0 && $term !== null ) {
			return true;
		} else {
			// term does not exist
			return false;
		}
	}

	/**
	 * Returns the number of allowed guests.
	 *
	 * @return int max_guests
	 */
	public function get_max_guests() {
		return absint( apply_filters( 'hotelier_get_max_guests', $this->max_guests, $this->id ) );
	}

	/**
	 * Returns the number of allowed children.
	 *
	 * @return int max_children
	 */
	public function get_max_children() {
		return absint( apply_filters( 'hotelier_get_max_children', $this->max_children, $this->id ) );
	}

	/**
	 * Returns the total number of rooms available in the structure.
	 *
	 * @return int stock_rooms
	 */
	public function get_stock_rooms() {
		return absint( apply_filters( 'hotelier_get_stock_rooms', $this->stock_rooms, $this->id ) );
	}

	/**
	 * Returns the number of rooms available on a given date.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param array $exclude
	 * @return int
	 */
	public function get_available_rooms( $checkin, $checkout = false, $exclude = array() ) {
		// Add 1 day to checkin if checkout is not provided
		if ( ! $checkout ) {
			$checkout = new DateTime( $checkin );
			$checkout = $checkout->modify( '+1 day' );
			$checkout = $checkout->format( 'Y-m-d' );
		}

		$reserved_rooms  = $this->get_reserved_rooms( $checkin, $checkout, $exclude );
		$available_rooms = $this->get_stock_rooms() - $reserved_rooms;
		$available_rooms = ( $available_rooms < 0 ) ? 0 : $available_rooms;

		return apply_filters( 'hotelier_get_available_rooms', $available_rooms, $this );
	}

	/**
	 * Get room's required minimum nights
	 *
	 * @return int
	 */
	public function get_min_nights() {
		return apply_filters( 'hotelier_per_room_minimum_nights', htl_get_option( 'booking_minimum_nights', 1 ), $this->id );
	}

	/**
	 * Get room's required maximum nights
	 *
	 * @return int
	 */
	public function get_max_nights() {
		return apply_filters( 'hotelier_per_room_maximum_nights', htl_get_option( 'booking_maximum_nights', 0 ), $this->id );
	}

	/**
	 * Check if the room requires a minimum of nights
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @return bool
	 */
	public function check_min_nights( $checkin, $checkout ) {
		$checkin               = new DateTime( $checkin );
		$checkout              = new DateTime( $checkout );
		$diff_checkin_checkout = date_diff( $checkin, $checkout )->days;

		$min_nights = $this->get_min_nights();

		$passed_check = true;

		if ( $min_nights && $diff_checkin_checkout < $min_nights ) {
			$passed_check = false;
		}

		$passed_check = apply_filters( 'hotelier_check_min_nights_passed', $passed_check, $checkin, $checkout, $this );

		return $passed_check;
	}

	/**
	 * Check if the room requires a maximum of nights
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @return bool
	 */
	public function check_max_nights( $checkin, $checkout ) {
		$checkin               = new DateTime( $checkin );
		$checkout              = new DateTime( $checkout );
		$diff_checkin_checkout = date_diff( $checkin, $checkout )->days;

		$max_nights = $this->get_max_nights();

		$passed_check = true;

		if ( $max_nights && $diff_checkin_checkout > $max_nights ) {
			$passed_check = false;
		}

		$passed_check = apply_filters( 'hotelier_check_max_nights_passed', $passed_check, $checkin, $checkout, $this );

		return $passed_check;
	}

	/**
	 * Checks if the room has enough rooms on a given date.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param int $qty
	 * @param array $exclude
	 * @return bool
	 */
	public function has_enough_rooms( $checkin, $checkout, $qty, $exclude = array() ) {
		$qty            = absint( $qty );
		$reserved_rooms = $this->get_reserved_rooms( $checkin, $checkout, $exclude );
		$pending_rooms  = $this->get_pending_rooms();
		$has_enough     = false;

		if ( ( $this->get_stock_rooms() > 0 ) && ( $this->get_stock_rooms() >= ( $reserved_rooms - $pending_rooms + $qty ) ) ) {
			$has_enough = true;
		}

		return apply_filters( 'hotelier_room_has_enough_rooms', $has_enough, $checkin, $checkout, $qty, $this->id );
	}

	/**
	 * Checks if the room is available on a given date.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param int $qty
	 * @param array $exclude
	 * @param bool $force
	 * @return bool
	 */
	public function is_available( $checkin, $checkout = false, $qty = 1, $exclude = array(), $force = false ) {
		$checkout         = $checkout ? $checkout : $checkin;
		$is_available     = false;
		$has_enough_rooms = $this->has_enough_rooms( $checkin, $checkout, $qty, $exclude ) ? true : false;

		if ( $has_enough_rooms && $this->check_min_nights( $checkin, $checkout ) && $this->check_max_nights( $checkin, $checkout ) ) {
			$is_available = true;
		}

		$is_available = apply_filters( 'hotelier_room_is_available', $is_available, $this->id, $checkin, $checkout, $qty, $exclude );

		// When forcing a booking, we want at least the number of rooms to be available
		$is_available = $force && $has_enough_rooms ? true : $is_available;

		return $is_available;
	}

	/**
	 * Checks if the room is available on a given date
	 * and if not, get the reason.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param int $qty
	 * @param array $exclude
	 * @param bool $force
	 * @return array
	 */
	public function is_available_with_reason( $checkin, $checkout = false, $qty = 1, $exclude = array(), $force = false ) {
		$reason           = '';
		$checkout         = $checkout ? $checkout : $checkin;
		$is_available     = false;
		$has_enough_rooms = $this->has_enough_rooms( $checkin, $checkout, $qty, $exclude ) ? true : false;
		$has_min_nights   = $this->check_min_nights( $checkin, $checkout );
		$has_max_nights   = $this->check_max_nights( $checkin, $checkout );

		if ( ! $has_min_nights || ! $has_max_nights ) {
			$min_nights = $this->get_min_nights();
			$max_nights = $this->get_max_nights();
			$reason     = htl_get_room_not_available_min_max_info( $min_nights, $max_nights, $this );
		}

		if ( $has_enough_rooms && $has_min_nights && $has_max_nights ) {
			$is_available = true;
		}

		$is_available = apply_filters( 'hotelier_room_is_available', $is_available, $this->id, $checkin, $checkout, $qty, $exclude );
		$reason       = apply_filters( 'hotelier_room_is_available_reason_text', $reason, $is_available, $this->id, $checkin, $checkout, $qty, $exclude );

		// When forcing a booking, we want at least the number of rooms to be available
		$is_available = $force && $has_enough_rooms ? true : $is_available;

		return array(
			'is_available' => $is_available,
			'reason'       => $reason,
		);
	}

	/**
	 * Gets the count of reserved rooms on a given date.
	 *
	 * @param string $checkin
	 * @param string $checkout
	 * @param array $exclude
	 * @return int
	 */
	public function get_reserved_rooms( $checkin, $checkout, $exclude = array() ) {
		global $wpdb;

		$sql          = $wpdb->prepare( "SELECT room_id, checkin, checkout, rb.reservation_id FROM {$wpdb->prefix}hotelier_rooms_bookings rb, {$wpdb->prefix}hotelier_bookings b WHERE rb.reservation_id = b.reservation_id AND rb.room_id = %d AND (%s < b.checkout AND %s > b.checkin) AND b.status <> 'cancelled' AND b.status <> 'refunded' AND b.status <> 'completed'", $this->id, $checkin, $checkout );
		$reservations = $wpdb->get_results( $sql );

		// Iterate each day to calculate the exact number of reservations
		$checkin              = new DateTime( $checkin );
		$checkout             = new DateTime( $checkout );
		$interval             = new DateInterval( 'P1D' );
		$daterange            = new DatePeriod( $checkin, $interval ,$checkout );
		$reserved_rooms_total = array();

		foreach( $daterange as $date ) {
			$reserved_rooms = 0;

			foreach ( $reservations as $reservation ) {
				if ( is_array( $exclude ) && in_array( $reservation->reservation_id, $exclude ) ) {
					continue;
				}

				$reservation_checkin  = new DateTime( $reservation->checkin );
				$reservation_checkout = new DateTime( $reservation->checkout );

				if ( $date >= $reservation_checkin && $date < $reservation_checkout ) {
					$reserved_rooms++;
				}
			}

			$reserved_rooms_total[] = $reserved_rooms;
		}

		$count = count( $reserved_rooms_total ) > 0 ? max( $reserved_rooms_total ) : 0;

		return apply_filters( 'hotelier_get_reserved_rooms', $count, $this );
	}

	/**
	 * Checks if there are some pending reservations.
	 *
	 * @return int
	 */
	public function get_pending_rooms() {
		global $wpdb;

		$reservation_id = null !== HTL()->session ? absint( HTL()->session->get( 'reservation_awaiting_payment' ) ) : 0;

		if ( ! $reservation_id > 0 ) {
			$count = 0;
		} else {
			$sql      = $wpdb->prepare( "SELECT room_id FROM {$wpdb->prefix}hotelier_rooms_bookings rb, {$wpdb->prefix}hotelier_bookings b WHERE b.status = 'pending' OR b.status = 'failed' AND rb.reservation_id = b.reservation_id AND b.reservation_id = %d AND rb.room_id = %d", $reservation_id, $this->id  );
			$result   = $wpdb->get_results( $sql );
			$count    = count( $result );
		}

		return apply_filters( 'hotelier_get_pending_rooms', $count, $this );
	}

	/**
	 * Returns the room facilities.
	 *
	 * @return string
	 */
	public function get_facilities() {
		$facilities = array();

		// Get room categories
		$terms = get_the_terms( $this->id, 'room_facilities' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$facilities[] = $term->name;
			}

			$facilities = implode( ', ', $facilities );
		}

		return $facilities;
	}

	/**
	 * Returns the room's bed size.
	 *
	 * @return string bed_size
	 */
	public function get_bed_size() {
		return $this->bed_size;
	}

	/**
	 * Returns the room's number of beds.
	 *
	 * @return int beds
	 */
	public function get_beds() {
		return absint( $this->beds );
	}

	/**
	 * Returns the room's number of bathrooms.
	 *
	 * @return int bathrooms
	 */
	public function get_bathrooms() {
		return absint( $this->bathrooms );
	}

	/**
	 * Returns the room's size.
	 *
	 * @return string room_size
	 */
	public function get_room_size() {
		return $this->room_size;
	}

	/**
	 * Returns the room's size with unit symbol.
	 *
	 * @return string get_formatted_room_size
	 */
	public function get_formatted_room_size() {
		$formatted_room_size = $this->room_size ? $this->room_size . ' ' . htl_get_option( 'room_size_unit', 'm²' ) : '';

		return apply_filters( 'hotelier_room_size', $formatted_room_size, $this );
	}

	/**
	 * Returns the room's additional details.
	 *
	 * @return string _room_additional_details
	 */
	public function get_additional_details() {
		return $this->room_additional_details;
	}

	/**
	 * Returns the room's price type.
	 *
	 * @return string price_type
	 */
	public function get_price_type() {
		return $this->price_type;
	}

	/**
	 * Checks if the price is per day.
	 *
	 * @return bool
	 */
	public function is_price_per_day() {
		return ( $this->get_price_type() == 'per_day' ) ? true : false;
	}

	/**
	 * Checks if the room has a seasonal price.
	 *
	 * @return bool
	 */
	public function has_seasonal_price() {
		return ( $this->get_price_type() == 'seasonal_price' ) ? true : false;
	}

	/**
	 * Returns the room's regular price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_regular_price( $checkin, $checkout = false ) {
		$checkin   = new DateTime( $checkin );
		$checkout  = $checkout ? new DateTime( $checkout ) : $checkin;
		$interval  = new DateInterval( 'P1D' );
		$daterange = new DatePeriod( $checkin, $interval ,$checkout );

		$price = 0;

		if ( $this->is_price_per_day() ) {
			if ( $this->regular_price_day ) {
				// Different price for each day
				foreach( $daterange as $date ) {

					// 0 (for Sunday) through 6 (for Saturday)
					$day_index = $date->format( 'w' );

					// We need to sum the price of each day
					if ( $this->regular_price_day[ $day_index ] ) {
						$price += $this->regular_price_day[ $day_index ];
					}
				}

			} else {
				// The room has a price per day but empty price
				$price = 0;
			}

		} else {
			// Same price for all days
			foreach( $daterange as $date ) {
				if ( $this->regular_price ) {
					$price += $this->regular_price;
				}
			}
		}

		$price = apply_filters( 'hotelier_get_regular_price', $price, $checkin, $checkout, $this );

		if ( $price > 0 ) {

			return $price;

		} else {

			return false;

		}
	}

	/**
	 * Returns the room's sale price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_sale_price( $checkin, $checkout = false ) {
		$checkin   = new DateTime( $checkin );
		$checkout  = $checkout ? new DateTime( $checkout ) : $checkin;
		$interval  = new DateInterval( 'P1D' );
		$daterange = new DatePeriod( $checkin, $interval ,$checkout );

		$price = 0;

		if ( $this->is_price_per_day() ) {
			if ( $this->sale_price_day ) {
				// Different price for each day
				foreach( $daterange as $date ) {

					// 0 (for Sunday) through 6 (for Saturday)
					$day_index = $date->format( 'w' );

					// We need to sum the price of each day
					if ( $this->sale_price_day[ $day_index ] ) {
						$price += $this->sale_price_day[ $day_index ];
					}
				}

			} else {
				// The room has a price per day but empty price
				$price = 0;
			}

		} else {
			// Same price for all days
			foreach( $daterange as $date ) {
				if ( $this->sale_price ) {
					$price += $this->sale_price;
				}
			}
		}

		$price = apply_filters( 'hotelier_get_sale_price', $price, $checkin, $checkout, $this );

		if ( $price > 0 ) {
			return $price;

		} else {

			return false;

		}
	}

	/**
	 * Returns the room's seasonal price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_seasonal_price( $checkin, $checkout = false ) {
		$checkin   = new DateTime( $checkin );
		$checkout  = $checkout ? new DateTime( $checkout ) : $checkin;

		$interval  = new DateInterval( 'P1D' );
		$daterange = new DatePeriod( $checkin, $interval ,$checkout );

		$price = 0;
		$rules = htl_get_seasonal_prices_schema();

		if ( is_array( $rules ) ) {
			// Reverse the array, last rules have a higher precedence
			$rules = array_reverse( $rules );
		}

		foreach( $daterange as $date ) {
			$curr_date = $date->getTimestamp();

			if ( $rules ) {
				$has_seasonal_price = false;

				foreach ( $rules as $key => $rule ) {
					$begin = new DateTime( $rule[ 'from' ] );
					$end   = new DateTime( $rule[ 'to' ] );

					// Check if the seasonal range repeats every year
					$repeats_every_year = isset( $rule[ 'every_year' ] ) ? true : false;

					if ( $repeats_every_year ) {
						$checkin_year = $checkin->format('Y');
						$begin_year   = $begin->format('Y');
						$years_diff   = $checkin_year - $begin_year;

						// Add 'x' years to the seasonal rule
						if ( $years_diff > 0 ) {
							$begin->add( new DateInterval( 'P' . $years_diff . 'Y' ) );
							$end->add( new DateInterval( 'P' . $years_diff . 'Y' ) );
						}
					}

					if ( $curr_date >= $begin->getTimestamp() && $curr_date <= $end->getTimestamp() ) {

						if ( isset( $this->seasonal_price[ $rule[ 'index' ] ] ) && $this->seasonal_price[ $rule[ 'index' ] ] > 0 ) {
							// Rule found, use seasonal price
							$price += $this->seasonal_price[ $rule[ 'index' ] ];
							$has_seasonal_price = true;

							// Exit
							break;
						}
					}
				}

				if ( ! $has_seasonal_price ) {
					// Rule not found, use default price
					if ( $this->seasonal_base_price ) {
						$price += $this->seasonal_base_price;
					}
				}
			}
		}

		if ( $price > 0 ) {
			return apply_filters( 'hotelier_get_seasonal_price', $price, $this );

		} else {

			return false;

		}
	}

	/**
	 * Returns whether or not the room is on sale.
	 *
	 * @return bool
	 */
	public function is_on_sale( $checkin, $checkout = false ) {
		$checkout   = $checkout ? $checkout : $checkin;
		$is_on_sale = ( ! $this->has_seasonal_price() && $this->get_sale_price( $checkin, $checkout ) && $this->get_regular_price( $checkin, $checkout ) && $this->get_sale_price( $checkin, $checkout ) < $this->get_regular_price( $checkin, $checkout ) ) ? true : false;

		return apply_filters( 'hotelier_room_is_on_sale', $is_on_sale, $checkin, $checkout, $this );
	}

	/**
	 * Returns the room's price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_price( $checkin, $checkout = false, $use_this = false ) {
		$checkout = $checkout ? $checkout : $checkin;

		if ( class_exists( 'HTL_APS_Room' ) && ! $use_this ) {
			$room  = new HTL_APS_Room( $this );
			$price = $room->get_price( $checkin, $checkout );

		} else {

			if ( $this->has_seasonal_price() ) {
				$price = $this->get_seasonal_price( $checkin, $checkout );
			} else if ( $this->is_on_sale( $checkin, $checkout ) ) {
				$price = $this->get_sale_price( $checkin, $checkout );
			} else {
				$price = $this->get_regular_price( $checkin, $checkout );
			}
		}

		return apply_filters( 'hotelier_get_price', $price, $checkin, $checkout, $this );
	}

	/**
	 * Returns the room's price in html format.
	 *
	 * @return string
	 */
	public function get_price_html( $checkin, $checkout, $use_this = false ) {
		if ( class_exists( 'HTL_APS_Room' ) && ! $use_this ) {
			$room = new HTL_APS_Room( $this );

			return $room->get_price_html( $checkin, $checkout );

		} else {
			if ( $this->is_variable_room() ) {
				$variations       = $this->get_room_variations();
				$count_variations = count( $variations );
				$prices           = array();

				for ( $i = 1; $i <= $count_variations; $i++ ) {
					$variation = $this->get_room_variation( $i );

					if ( $variation->get_price( $checkin, $checkout ) ) {
						$prices[] = $variation->get_price( $checkin, $checkout );
					}
				}

				$min_price = false;

				if ( ! empty( $prices ) ) {
					// Get min price of rates
					$min_price = min( $prices ) / 100; // (prices are stored as integers)
				}

				if ( $min_price && $min_price > 0 ) {

					$min_price = sprintf( _x( 'From %s', 'min_price', 'wp-hotelier' ), htl_price( $min_price ) );
					$price     = apply_filters( 'hotelier_get_rate_price_html', $min_price, $this );

				} else {

					$price = apply_filters( 'hotelier_empty_price_html', '', $this );

				}

			} elseif ( $get_price = $this->get_price( $checkin, $checkout ) ) {

				if ( $this->is_on_sale( $checkin, $checkout ) ) {

					$from  = $this->get_regular_price( $checkin, $checkout ) / 100; // (prices are stored as integers)
					$to    = $this->get_sale_price( $checkin, $checkout ) / 100; // (prices are stored as integers)
					$price = sprintf( _x( 'Price: %s', 'price', 'wp-hotelier' ), $this->get_price_html_from_to( $from, $to ) );

					$price = apply_filters( 'hotelier_sale_price_html', $price, $this );

				} else {

					$price = htl_price( $get_price / 100 ); // (prices are stored as integers)
					$price = sprintf( _x( 'Price: %s', 'price', 'wp-hotelier' ), $price );
					$price = apply_filters( 'hotelier_get_price_html', $price, $this );

				}

			} else {

				$price = apply_filters( 'hotelier_empty_price_html', '', $this );

			}

			return $price;
		}
	}

	/**
	 * Functions for getting parts of a price, in html, used by get_price_html.
	 *
	 * @param  string $from String or float to wrap with 'from' text
	 * @param  mixed $to String or float to wrap with 'to' text
	 * @return string
	 */
	public function get_price_html_from_to( $from, $to ) {
		$price = '<del>' . ( ( is_numeric( $from ) ) ? htl_price( $from ) : $from ) . '</del> <ins>' . ( ( is_numeric( $to ) ) ? htl_price( $to ) : $to ) . '</ins>';

		return apply_filters( 'hotelier_get_price_html_from_to', $price, $from, $to, $this );
	}

	/**
	 * Returns the low room's price in html format (variations included).
	 *
	 * @return string
	 */
	public function get_min_price_html( $use_this = false ) {
		if ( class_exists( 'HTL_APS_Room' ) && ! $use_this ) {
			$room = new HTL_APS_Room( $this );

			return $room->get_min_price_html();
		} else {
			$min_price = 0;

			if ( $this->is_variable_room() ) {
				$variations       = $this->get_room_variations();
				$count_variations = count( $variations );
				$prices           = array();

				for ( $i = 1; $i <= $count_variations; $i++ ) {
					$variation = $this->get_room_variation( $i );

					if ( $variation->has_seasonal_price() ) {
						// seasonal price schema
						$rules = htl_get_seasonal_prices_schema();

						// check only the rules stored in the schema
						// we don't allow 'orphan' rules
						foreach ( $rules as $key => $value ) {

							// check if this rule has a price
							if ( isset( $variation->variation[ 'seasonal_price' ][ $key ] ) && $variation->variation[ 'seasonal_price' ][ $key ] > 0 ) {
								$prices[] = $variation->variation[ 'seasonal_price' ][ $key ];
							}
						}

						// get also the default price
						$prices[] = $variation->variation[ 'seasonal_base_price' ];

					} else if  ( $variation->is_price_per_day() ) {
						if ( $variation->variation[ 'sale_price_day' ] ) {
							$variation_min_price = min( $variation->variation[ 'sale_price_day' ] );
							$prices[] = $variation_min_price;

						} elseif ( $variation->variation[ 'price_day' ] ) {
							$variation_min_price = min( $variation->variation[ 'price_day' ] );
							$prices[] = $variation_min_price;
						}

					} else {

						if ( $variation->variation[ 'sale_price' ] ) {
							$variation_min_price = $variation->variation[ 'sale_price' ];
							$prices[] = $variation_min_price;

						} elseif ( $variation->variation[ 'regular_price' ] ) {
							$variation_min_price = $variation->variation[ 'regular_price' ];
							$prices[] = $variation_min_price;
						}
					}
				}

				$min_price = $prices ? min( $prices ) / 100 : 0; // (prices are stored as integers)

				if ( $min_price > 0 ) {
					$min_price = sprintf( __( 'Rates from %s per night', 'wp-hotelier' ), htl_price( $min_price ) );
				} else {
					$min_price = apply_filters( 'hotelier_empty_price_html', '', $this );
				}

			} else {

				if ( $this->has_seasonal_price() ) {
					$prices = array();

					// seasonal price schema
					$rules = htl_get_seasonal_prices_schema();

					if ( is_array( $rules ) ) {
						// get room seasonal prices
						$seasonal_prices = $this->seasonal_price;

						// check only the rules stored in the schema
						// we don't allow 'orphan' rules
						foreach ( $rules as $key => $value ) {

							// check if this rule has a price
							if ( isset( $seasonal_prices[ $key ] ) && $seasonal_prices[ $key ] > 0 ) {
								$prices[] = $seasonal_prices[ $key ];
							}
						}

						// get also the default price
						$prices[] = $this->seasonal_base_price;
					}

					$min_price = $prices ? min( $prices ) / 100 : 0; // (prices are stored as integers)

					if ( $min_price > 0 ) {
						$min_price = sprintf( __( 'Rates from %s per night', 'wp-hotelier' ), htl_price( $min_price ) );
					} else {
						$min_price = apply_filters( 'hotelier_empty_price_html', '', $this );
					}

				} else if ( $this->is_price_per_day() ) {

					if ( $this->sale_price_day ) {
						$min_price = min( $this->sale_price_day ) / 100; // (prices are stored as integers)

					} elseif ( $this->regular_price_day ) {
						$min_price = min( $this->regular_price_day ) / 100; // (prices are stored as integers)
					}

					if ( $min_price > 0 ) {
						$min_price = sprintf( __( 'Rates from %s per night', 'wp-hotelier' ), htl_price( $min_price ) );
					} else {
						$min_price = apply_filters( 'hotelier_empty_price_html', '', $this );
					}

				} else {

					if ( $this->sale_price ) {
						$min_price = $this->sale_price / 100; // (prices are stored as integers)

					} elseif ( $this->regular_price ) {
						$min_price = $this->regular_price / 100; // (prices are stored as integers)
					}

					if ( $min_price > 0 ) {
						if ( $this->sale_price && $this->regular_price ) {
							$from  = $this->regular_price / 100; // (prices are stored as integers)
							$to    = $this->sale_price / 100; // (prices are stored as integers)
							$min_price = sprintf( __( '%s per night', 'wp-hotelier' ), $this->get_price_html_from_to( $from, $to ) );
						} else {
							$min_price = sprintf( __( '%s per night', 'wp-hotelier' ), htl_price( $min_price ) );
						}
					} else {
						$min_price = apply_filters( 'hotelier_empty_price_html', '', $this );
					}
				}

			}

			$min_price = apply_filters( 'hotelier_min_price_html', $min_price, $this );

			return $min_price;
		}
	}

	/**
	 * Returns the room's conditions.
	 *
	 * @return array conditions
	 */
	public function get_room_conditions() {
		$room_conditions = array();

		// we just need a flat array to output the conditions on the front-end
		foreach ( $this->room_conditions as $key => $value ) {
			if ( isset( $value[ 'name' ] ) ) {
				$room_conditions[] = $value[ 'name' ];
			}
		}

		$room_conditions = array_filter( $room_conditions );

		return apply_filters( 'hotelier_room_conditions', $room_conditions, $this );
	}

	/**
	 * Checks if the room has some conditions.
	 *
	 * @return bool
	 */
	public function has_conditions() {
		$room_conditions = $this->get_room_conditions();

		return empty( $room_conditions ) ? false : true;
	}

	/**
	 * Checks if the room requires a deposit.
	 *
	 * @return bool
	 */
	public function needs_deposit() {
		$require_deposit = $this->require_deposit;

		return $require_deposit ? true : false;
	}

	/**
	 * Returns the deposit amount
	 *
	 * @return int percentage of total price
	 */
	public function get_deposit() {
		$percentage = $this->needs_deposit() ? $this->deposit_amount : 0;

		return apply_filters( 'hotelier_room_deposit', $percentage, $this );
	}

	/**
	 * Returns the deposit amount with percentage symbol
	 *
	 * @return int percentage of total price
	 */
	public function get_formatted_deposit() {
		$percentage = $this->get_deposit() . '%';

		return apply_filters( 'hotelier_room_formatted_deposit', $percentage, $this );
	}

	/**
	 * Returns the deposit amount with a more descriptive text
	 *
	 * @return int percentage of total price
	 */
	public function get_long_formatted_deposit() {
		$text = $this->get_deposit() === '100' ? __( 'Requires an immediate payment', 'wp-hotelier' ) : sprintf( __( 'Requires an immediate payment (%s%% of the total)', 'wp-hotelier' ), $this->get_deposit() );

		return apply_filters( 'hotelier_room_long_formatted_deposit', $text, $this );
	}

	/**
	 * Returns the name of the rate
	 *
	 * @param int $rate_id
	 * @return string
	 */
	public function get_rate_name( $rate_id ) {
		$variations = $this->get_room_variations();

		return isset( $variations[ $rate_id ][ 'room_rate' ] ) ? $variations[ $rate_id ][ 'room_rate' ] : '';
	}

	/**
	 * Returns the main room image.
	 *
	 * @param string $size (default: 'room_thumbnail')
	 * @return string
	 */
	public function get_image( $size = 'room_thumbnail', $attr = array() ) {
		if ( has_post_thumbnail( $this->id ) ) {
			$image = get_the_post_thumbnail( $this->id, $size, $attr );
		} else {
			$image = htl_placeholder_img( $size );
		}

		return $image;
	}

	/**
	 * Returns the room categories.
	 *
	 * @param string $sep (default: ', ')
	 * @param string $before (default: '')
	 * @param string $after (default: '')
	 * @return string
	 */
	public function get_categories( $sep = ', ', $before = '', $after = '' ) {
		return get_the_term_list( $this->id, 'room_cat', $before, $sep, $after );
	}

	/**
	 * Returns the room's variations (rates).
	 *
	 * @return array room_variations
	 */
	public function get_room_variations() {
		return array_filter( (array) $this->room_variations );
	}

	/**
	 * Returns a variation from $rate_id.
	 *
	 * @param int $rate_id Rate ID
	 * @return HTL_Room_Variation on success, false on failure
	 */
	public function get_room_variation( $rate_id ) {
		$variations = $this->get_room_variations();
		$variation  = isset( $variations[ $rate_id ] ) ? $variations[ $rate_id ] : false;

		if ( ! $variation ) {
			return false;
		}

		return new HTL_Room_Variation( $variation, $this->id );
	}

	/**
	 * Get an array of category IDs associated to the room.
	 *
	 * @todo Perhaps we may include also the 'room_facilities' taxonomy?
	 * @return array Array of category IDs
	 */
	public function get_terms_related() {
		$cats = array();

		// Get room categories
		$terms = get_the_terms( $this->id, 'room_cat' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$cats[] = $term->term_id;
			}
		}

		return $cats;
	}

	/**
	 * Checks if a room is cancellable.
	 *
	 * @return bool
	 */
	public function is_cancellable() {
		$non_cancellable = $this->non_cancellable;

		return $non_cancellable ? false : true;
	}
}

endif;
