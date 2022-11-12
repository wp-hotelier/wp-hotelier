<?php
/**
 * Room Variation Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Room_Variation' ) ) :

/**
 * HTL_Room_Variation Class
 */
class HTL_Room_Variation {

	/**
	 * The room (post) ID.
	 *
	 * @var int
	 */
	public $room_id = 0;

	/**
	 * $variation Stores variation data
	 *
	 * @var $variation
	 */
	public $variation = null;

	/**
	 * Get things going
	 */
	public function __construct( $variation, $room ) {
		$this->variation = $variation;

		if ( is_numeric( $room ) ) {
			$this->room_id = absint( $room );
		} elseif ( $room instanceof HTL_Room ) {
			$this->room_id = absint( $room->id );
		} elseif ( isset( $room->ID ) ) {
			$this->room_id = absint( $room->ID );
		}
	}

	/**
	 * Returns the variation's index.
	 *
	 * @return string room_index
	 */
	public function get_room_index() {
		$index = $this->variation[ 'index' ];

		return absint( $index );
	}

	/**
	 * Returns the variation's rate.
	 *
	 * @return string room_rate
	 */
	public function get_room_rate() {
		$room_rate = $this->variation[ 'room_rate' ];

		return $room_rate;
	}

	/**
	 * Returns the formatted variation's rate.
	 *
	 * @return string room_rate
	 */
	public function get_formatted_room_rate() {
		$room_rate = $this->get_room_rate();
		$room_rate = htl_get_formatted_room_rate( $room_rate );

		return $room_rate;
	}

	/**
	 * Returns the variation's description.
	 *
	 * @return string room_description
	 */
	public function get_room_description() {
		// We need to check if the term exists because the rate_name is stored
		// in a meta box (and we do not know if it still exists).
		$rate_name = $this->get_room_rate();

		$get_room_rates = get_terms( 'room_rate', 'hide_empty=0' );

		if ( empty( $get_room_rates ) || is_wp_error( $get_room_rates ) ) {
			// room_rate taxonomy empty
			$description = '';
		}

		$term = term_exists( $rate_name, 'room_rate' );

		if ( $term !== 0 && $term !== null ) {
			$description = term_description( $term[ 'term_id' ], 'room_rate' );
		} else {
			// room_rate taxonomy empty
			$description = '';
		}

		return $description;
	}

	/**
	 * Returns the variation's price type.
	 *
	 * @return string price_type
	 */
	public function get_price_type() {
		$price_type = $this->variation[ 'price_type' ];

		return $price_type;
	}

	/**
	 * Checks if the price is per day.
	 *
	 * @return bool
	 */
	public function is_price_per_day() {
		return $this->get_price_type() == 'per_day' ? true : false;
	}

	/**
	 * Checks if the variation has a seasonal price.
	 *
	 * @return bool
	 */
	public function has_seasonal_price() {
		return ( $this->get_price_type() == 'seasonal_price' ) ? true : false;
	}

	/**
	 * Returns the variation's regular price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_regular_price( $checkin, $checkout ) {
		$checkin   = new DateTime( $checkin );
		$checkout  = new DateTime( $checkout );
		// $checkout  = $checkout->modify( '+1 day' ); // include last day

		$interval  = new DateInterval( 'P1D' );
		$daterange = new DatePeriod( $checkin, $interval ,$checkout );

		$price = 0;

		if ( $this->is_price_per_day() ) {
			if ( $this->variation[ 'price_day' ] ) {
				// Different price for each day
				foreach( $daterange as $date ) {

					// 0 (for Sunday) through 6 (for Saturday)
					$day_index = $date->format( 'w' );

					// We need to sum the price of each day
					if ( $this->variation[ 'price_day' ][ $day_index ] ) {
						$price += $this->variation[ 'price_day' ][ $day_index ];
					}
				}

			} else {
				// The room has a price per day but empty price
				$price = 0;
			}

		} else {
			// Same price for all days
			foreach( $daterange as $date ) {
				if ( $this->variation[ 'regular_price' ] ) {
					$price += $this->variation[ 'regular_price' ];
				}
			}
		}

		$price = apply_filters( 'hotelier_get_variation_regular_price', $price, $checkin, $checkout, $this );

		if ( $price > 0 ) {

			return $price;

		} else {

			return false;

		}
	}

	/**
	 * Returns the variation's sale price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_sale_price( $checkin, $checkout ) {
		$checkin   = new DateTime( $checkin );
		$checkout  = new DateTime( $checkout );
		// $checkout  = $checkout->modify( '+1 day' ); // include last day

		$interval  = new DateInterval( 'P1D' );
		$daterange = new DatePeriod( $checkin, $interval ,$checkout );

		$price = 0;

		if ( $this->is_price_per_day() ) {
			if ( $this->variation[ 'sale_price_day' ] ) {
				// different price for each day
				foreach( $daterange as $date ) {

					// 0 (for Sunday) through 6 (for Saturday)
					$day_index = $date->format( 'w' );

					// We need to sum the price of each day
					if ( $this->variation[ 'sale_price_day' ][ $day_index ] ) {
						$price += $this->variation[ 'sale_price_day' ][ $day_index ]; // Use integers
					}
				}

			} else {
				// The room has a price per day but empty price
				$price = 0;
			}

		} else {
			// Same price for all days
			foreach( $daterange as $date ) {
				if ( $this->variation[ 'sale_price' ] ) {
					$price += $this->variation[ 'sale_price' ];
				}
			}
		}

		$price = apply_filters( 'hotelier_get_variation_sale_price', $price, $checkin, $checkout, $this );

		if ( $price > 0 ) {

			return $price;

		} else {

			return false;

		}
	}

	/**
	 * Returns the variatio's seasonal price.
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
					$end = new DateTime( $rule[ 'to' ] );

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

						if ( isset( $this->variation[ 'seasonal_price' ][ $rule[ 'index' ] ] ) && $this->variation[ 'seasonal_price' ][ $rule[ 'index' ] ] > 0 ) {
							// Rule found, use seasonal price
							$price += $this->variation[ 'seasonal_price' ][ $rule[ 'index' ] ];
							$has_seasonal_price = true;

							// Exit
							break;
						}
					}
				}

				if ( ! $has_seasonal_price ) {
					// Rule not found, use default price
					if ( $this->variation[ 'seasonal_base_price' ] ) {
						$price += $this->variation[ 'seasonal_base_price' ];
					}
				}
			}
		}

		if ( $price > 0 ) {
			return apply_filters( 'hotelier_get_variation_seasonal_price', $price, $this );

		} else {

			return false;

		}
	}

	/**
	 * Returns whether or not the variation is on sale.
	 *
	 * @return bool
	 */
	public function is_on_sale( $checkin, $checkout ) {
		$checkout   = $checkout ? $checkout : $checkin;
		$is_on_sale = ( ! $this->has_seasonal_price() && $this->get_sale_price( $checkin, $checkout ) && $this->get_regular_price( $checkin, $checkout ) && $this->get_sale_price( $checkin, $checkout ) < $this->get_regular_price( $checkin, $checkout ) ) ? true : false;

		return apply_filters( 'hotelier_variation_is_on_sale', $is_on_sale, $checkin, $checkout, $this );
	}

	/**
	 * Returns the variation's price.
	 *
	 * @return mixed int price or false if there are none
	 */
	public function get_price( $checkin, $checkout = false, $use_this = false ) {
		$checkout = $checkout ? $checkout : $checkin;

		if ( class_exists( 'HTL_APS_Room_Variation' ) && ! $use_this ) {
			$variation = new HTL_APS_Room_Variation( $this );
			$price     = $variation->get_price( $checkin, $checkout );

		} else {

			if ( $this->has_seasonal_price() ) {
				$price = $this->get_seasonal_price( $checkin, $checkout );
			} else if ( $this->is_on_sale( $checkin, $checkout ) ) {
				$price = $this->get_sale_price( $checkin, $checkout );
			} else {
				$price = $this->get_regular_price( $checkin, $checkout );
			}
		}

		return apply_filters( 'hotelier_variation_get_price', $price, $checkin, $checkout, $this );
	}

	/**
	 * Returns the variation's price in html format.
	 *
	 * @return string
	 */
	public function get_price_html( $checkin, $checkout, $use_this = false ) {
		if ( class_exists( 'HTL_APS_Room_Variation' ) && ! $use_this ) {

			$variation = new HTL_APS_Room_Variation( $this );

			return $variation->get_price_html( $checkin, $checkout );

		} else {

			if ( $get_price = $this->get_price( $checkin, $checkout ) ) {

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
	 * Returns the low variation's price in html format.
	 *
	 * @return string
	 */
	public function get_min_price_html( $use_this = false ) {
		if ( class_exists( 'HTL_APS_Room_Variation' ) && ! $use_this ) {
			$variation = new HTL_APS_Room_Variation( $this );

			return $variation->get_min_price_html();
		} else {

			$min_price = 0;

			if ( $this->has_seasonal_price() ) {
				$prices = array();

				// seasonal price schema
				$rules = htl_get_seasonal_prices_schema();

				if ( is_array( $rules ) ) {
					// get variation seasonal prices
					$seasonal_prices = $this->variation[ 'seasonal_price' ];

					// check only the rules stored in the schema
					// we don't allow 'orphan' rules
					foreach ( $rules as $key => $value ) {

						// check if this rule has a price
						if ( isset( $this->variation[ 'seasonal_price' ][ $key ] ) && $this->variation[ 'seasonal_price' ][ $key ] > 0 ) {
							$prices[] = $this->variation[ 'seasonal_price' ][ $key ];
						}
					}

					// get also the default price
					$prices[] = $this->variation[ 'seasonal_base_price' ];
				}

				$min_price = min( $prices ) / 100; // (prices are stored as integers)

				if ( $min_price > 0 ) {
					$min_price = sprintf( __( 'Rates from %s per night', 'wp-hotelier' ), htl_price( $min_price ) );
				}

			} else if  ( $this->is_price_per_day() ) {
				if ( $this->variation[ 'sale_price_day' ] ) {
					$min_price = min( $this->variation[ 'sale_price_day' ] ) / 100; // (prices are stored as integers)

				} elseif ( $this->variation[ 'price_day' ] ) {
					$min_price = min( $this->variation[ 'price_day' ] ) / 100; // (prices are stored as integers)
				}

				if ( $min_price > 0 ) {
					$min_price = sprintf( __( 'Rates from %s per night', 'wp-hotelier' ), htl_price( $min_price ) );
				}

			} else {

				if ( $this->variation[ 'sale_price' ] ) {
					$min_price = $this->variation[ 'sale_price' ] / 100; // (prices are stored as integers)

				} elseif ( $this->variation[ 'regular_price' ] ) {
					$min_price = $this->variation[ 'regular_price' ] / 100; // (prices are stored as integers)
				}

				if ( $min_price > 0 ) {
					$min_price = sprintf( __( '%s per night', 'wp-hotelier' ), htl_price( $min_price ) );
				}
			}

			if ( $min_price === 0 ) {
				$min_price = apply_filters( 'hotelier_empty_price_html', '', $this );
			}

			$min_price = apply_filters( 'hotelier_variation_min_price_html', $min_price, $this );

			return $min_price;
		}
	}

	/**
	 * Returns the variation's conditions.
	 *
	 * @return array conditions
	 */
	public function get_room_conditions() {
		$room_conditions = array();

		// we just need a flat array to output the conditions on the front-end
		foreach ( $this->variation[ 'room_conditions' ] as $key => $value ) {
			if ( isset( $value[ 'name' ] ) ) {
				$room_conditions[] = $value[ 'name' ];
			}
		}

		$room_conditions = array_filter( $room_conditions );

		return apply_filters( 'hotelier_room_conditions', $room_conditions, $this );
	}

	/**
	 * Checks if the variation has some conditions.
	 *
	 * @return bool
	 */
	public function has_conditions() {
		$room_conditions = $this->get_room_conditions();

		return empty( $room_conditions ) ? false : true;
	}

	/**
	 * Checks if the variation requires a deposit.
	 *
	 * @return bool
	 */
	public function needs_deposit() {
		$require_deposit = isset( $this->variation[ 'require_deposit' ] ) ? $this->variation[ 'require_deposit' ] : false;

		return $require_deposit;
	}

	/**
	 * Returns the deposit amount
	 *
	 * @return int percentage of total price
	 */
	public function get_deposit() {
		$percentage = $this->needs_deposit() ? $this->variation[ 'deposit_amount' ] : 0;

		return apply_filters( 'hotelier_room_variation_deposit', $percentage, $this );
	}

	/**
	 * Returns the deposit amount with percentage symbol
	 *
	 * @return int percentage of total price
	 */
	public function get_formatted_deposit() {
		$percentage = $this->get_deposit() . '%';

		return apply_filters( 'hotelier_room_variation_formatted_deposit', $percentage, $this );
	}

	/**
	 * Returns the deposit amount with a more descriptive text
	 *
	 * @return int percentage of total price
	 */
	public function get_long_formatted_deposit() {
		$text = $this->get_deposit() === '100' ? __( 'Requires an immediate payment', 'wp-hotelier' ) : sprintf( __( 'Requires an immediate payment (%s%% of the total)', 'wp-hotelier' ), $this->get_deposit() );


		return apply_filters( 'hotelier_room_variation_long_formatted_deposit', $text, $this );
	}

	/**
	 * Checks if the variation is cancellable.
	 *
	 * @return bool
	 */
	public function is_cancellable() {
		$non_cancellable = isset( $this->variation[ 'non_cancellable' ] ) ? false : true;

		return $non_cancellable;
	}

	/**
	 * Get variation's required minimum nights
	 *
	 * @return int
	 */
	public function get_min_nights() {
		return apply_filters( 'hotelier_per_variation_minimum_nights', htl_get_option( 'booking_minimum_nights', 1 ), $this );
	}

	/**
	 * Get variation's required maximum nights
	 *
	 * @return int
	 */
	public function get_max_nights() {
		return apply_filters( 'hotelier_per_variation_maximum_nights', htl_get_option( 'booking_maximum_nights', 0 ), $this );
	}

	/**
	 * Returns the variation's advanced price settings.
	 *
	 * @return string array
	 */
	public function get_advanced_price_settings() {
		$settings = array(
			'type'                  => 'fixed',
			'modifier'              => 'decrease',
			'fixed_price'           => 0,
			'modifier_amount_price' => 0,
			'modifier_percentage'   => 0,
		);

		$price_settings = $this->variation[ 'advanced_variation_price' ];

		if ( isset( $price_settings[ 'type' ] ) && $price_settings[ 'type' ] ) {
			$settings[ 'type' ] = $price_settings[ 'type' ];
		}

		$allowed_types = apply_filters( 'hotelier_allowed_advanced_room_rate_price_types',
			array(
				'fixed',
				'amount',
				'percentage',
			)
		);

		if ( ! in_array( $settings[ 'type' ], $allowed_types ) ) {
			$settings[ 'type' ] = 'fixed';
		}

		if ( isset( $price_settings[ 'modifier' ] ) && $price_settings[ 'modifier' ] === 'increase' ) {
			$settings[ 'modifier' ] = 'increase';
		}

		if ( $settings[ 'type' ] === 'fixed' && isset( $price_settings[ 'fixed_price' ] ) ) {
			$settings[ 'fixed_price' ] = $price_settings[ 'fixed_price' ];
		}

		if ( $settings[ 'type' ] === 'amount' && isset( $price_settings[ 'modifier_amount_price' ] ) ) {
			$settings[ 'modifier_amount_price' ] = $price_settings[ 'modifier_amount_price' ];
		}

		if ( $settings[ 'type' ] === 'percentage' && isset( $price_settings[ 'modifier_percentage' ] ) ) {
			$settings[ 'modifier_percentage' ] = $price_settings[ 'modifier_percentage' ];
		}

		return $settings;
	}

	/**
	 * Check if variation has a custom price.
	 *
	 * @return bool
	 */
	public function has_advanced_custom_price() {
		$has_custom_price = false;

		if ( isset( $this->variation[ 'custom_price' ] ) && $this->variation[ 'custom_price' ] ) {
			$has_custom_price = true;
		}

		return $has_custom_price;
	}

	/**
	 * Calculate variation price.
	 *
	 * @return int
	 */
	public function calculate_advanced_custom_price( $price, $nights ) {
		if ( ! $this->has_advanced_custom_price() ) {
			return $price;
		}

		$price_settings = $this->get_advanced_price_settings();

		if ( $price_settings[ 'type' ] === 'fixed' ) {
			$fixed_price = $price_settings[ 'fixed_price' ];
			$price       = $nights * $fixed_price;
		} else if ( $price_settings[ 'type' ] === 'amount' ) {
			$modifier              = $price_settings[ 'modifier' ];
			$modifier_amount_price = $price_settings[ 'modifier_amount_price' ];
			$amount_to_sum         = $nights * $modifier_amount_price;

			// sum or reduce the amount
			$price = $modifier === 'increase' ? $price + $amount_to_sum : $price - $amount_to_sum;
		} else {
			$modifier          = $price_settings[ 'modifier' ];
			$percentage        = $price_settings[ 'modifier_percentage' ];
			$percentage_amount = ( $price * $percentage ) / 100;

			// sum or reduce the percentage amount
			$price = $modifier === 'increase' ? $price + $percentage_amount : $price - $percentage_amount;
		}

		return $price;
	}

	/**
	 * Get advanced restriction type.
	 *
	 * @return string
	 */
	public function get_advanced_restriction_type() {
		$restriction_type = isset( $this->variation[ 'variation_restrictions' ] ) ? $this->variation[ 'variation_restrictions' ] : false;

		return apply_filters( 'hotelier_room_variation_advanced_restriction_type', $restriction_type, $this );
	}

	/**
	 * Check if variation has restriction.
	 *
	 * @return bool
	 */
	public function has_advanced_restriction() {
		$has_restriction = false;

		if ( $this->get_advanced_restriction_type() && $this->get_advanced_restriction_type() !== 'none' ) {
			$has_restriction = true;
		}

		return apply_filters( 'hotelier_room_variation_has_advanced_restriction', $has_restriction, $this );
	}

	/**
	 * Get restriction value.
	 *
	 * @return int
	 */
	public function get_advanced_restriction_value() {
		$restriction_value = 0;

		if ( $this->has_advanced_restriction() ) {
			$type = $this->get_advanced_restriction_type();
			$key  = 'restriction_' . str_replace( '-', '_', $type );

			if ( isset( $this->variation[ $key ] ) ) {
				$restriction_value = $this->variation[ $key ];
			}
		}

		return apply_filters( 'hotelier_room_variation_get_advanced_restriction_value', $restriction_value, $this );
	}
}

endif;
