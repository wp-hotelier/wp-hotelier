<?php
/**
 * Handle comments (reservation notes).
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes
 * @version  2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Comments' ) ) :

/**
 * HTL_Comments Class
 */
class HTL_Comments {

	/**
	 * Hook in methods.
	 */
	public static function init() {

		// Secure reservation notes
		add_filter( 'comments_clauses', array( __CLASS__, 'exclude_reservation_comments' ), 10, 1 );
		add_action( 'comment_feed_join', array( __CLASS__, 'exclude_reservation_comments_from_feed_join' ) );
		add_action( 'comment_feed_where', array( __CLASS__, 'exclude_reservation_comments_from_feed_where' ) );

		// Count comments
		add_filter( 'wp_count_comments', array( __CLASS__, 'wp_count_comments' ), 10, 2 );

		// Delete comments count cache when insert a new comment or when the status changes
		add_action( 'wp_insert_comment', array( __CLASS__, 'delete_comments_count_cache' ) );
		add_action( 'wp_set_comment_status', array( __CLASS__, 'delete_comments_count_cache' ) );
	}

	/**
	 * Exclude reservation notes from queries and RSS
	 *
	 * This code should exclude room_reservation notes from queries. Some queries (like the recent comments widget on the dashboard) are hardcoded
	 * and are not filtered, however, the code current_user_can( 'read_post', $comment->comment_post_ID ) should keep them safe since only admin and
	 * hotel managers can view reservations anyway.
	 *
	 * The frontend view reservation pages get around this filter by using remove_filter('comments_clauses', array( 'HTL_Comments' ,'exclude_reservation_comments'), 10, 1 );
	 * @param  array $clauses
	 * @return array
	 */
	public static function exclude_reservation_comments( $clauses ) {
		global $wpdb;

		if ( is_admin() ) {
			$screen = get_current_screen(); // Ensure we call this function only in admin pages

			if ( isset( $screen->id ) && in_array( $screen->id, array( 'room_reservation', 'edit-room_reservation' ) ) && current_user_can( 'manage_hotelier' ) ) {
				return $clauses; // Don't hide when viewing reservations in admin
			}
		}

		if ( ! $clauses[ 'join' ] ) {
			$clauses[ 'join' ] = '';
		}

		if ( ! strstr( $clauses[ 'join' ], "JOIN $wpdb->posts" ) ) {
			$clauses[ 'join' ] .= " LEFT JOIN $wpdb->posts ON comment_post_ID = $wpdb->posts.ID ";
		}

		if ( $clauses[ 'where' ] ) {
			$clauses[ 'where' ] .= ' AND ';
		}

		$clauses[ 'where' ] .= " $wpdb->posts.post_type <> 'room_reservation' ";

		return $clauses;
	}

	/**
	 * Exclude reservation notes from queries and RSS
	 * @param  string $join
	 * @return string
	 */
	public static function exclude_reservation_comments_from_feed_join( $join ) {
		global $wpdb;

		if ( ! strstr( $join, $wpdb->posts ) ) {
			$join = " LEFT JOIN $wpdb->posts ON $wpdb->comments.comment_post_ID = $wpdb->posts.ID ";
		}

		return $join;
	}

	/**
	 * Exclude reservation notes from queries and RSS
	 * @param  string $where
	 * @return string
	 */
	public static function exclude_reservation_comments_from_feed_where( $where ) {
		global $wpdb;

		if ( $where ) {
			$where .= ' AND ';
		}

		$where .= " $wpdb->posts.post_type <> 'room_reservation' ";

		return $where;
	}

	/**
	 * Delete comments count cache whenever there is a
	 * new comment or when the status of a comment changes.
	 */
	public static function delete_comments_count_cache() {
		delete_transient( 'hoteleir_count_comments' );
	}

	/**
	 * Remove reservation notes from wp_count_comments().
	 * @param  object $stats
	 * @param  int $post_id
	 * @return object
	 */
	public static function wp_count_comments( $stats, $post_id ) {
		global $wpdb;

		if ( 0 === $post_id ) {
			$stats = get_transient( 'hotelier_count_comments' );

			if ( ! $stats ) {
				$stats = array();

				$count = $wpdb->get_results( "
					SELECT comment_approved, COUNT(*) AS num_comments
					FROM {$wpdb->comments}
					WHERE comment_type NOT IN ('order_note', 'webhook_delivery')
					GROUP BY comment_approved
				", ARRAY_A );

				$total = 0;
				$approved = array(
					'0'            => 'moderated',
					'1'            => 'approved',
					'spam'         => 'spam',
					'trash'        => 'trash',
					'post-trashed' => 'post-trashed',
				);

				foreach ( (array) $count as $row ) {
					// Don't count post-trashed toward totals.
					if ( 'post-trashed' !== $row['comment_approved'] && 'trash' !== $row['comment_approved'] ) {
						$total += $row['num_comments'];
					}
					if ( isset( $approved[ $row['comment_approved'] ] ) ) {
						$stats[ $approved[ $row['comment_approved'] ] ] = $row['num_comments'];
					}
				}

				$stats['total_comments'] = $total;
				$stats['all'] = $total;
				foreach ( $approved as $key ) {
					if ( empty( $stats[ $key ] ) ) {
						$stats[ $key ] = 0;
					}
				}

				$stats = (object) $stats;
				set_transient( 'hotelier_count_comments', $stats );
			}
		}

		return $stats;
	}
}

endif;

HTL_Comments::init();
