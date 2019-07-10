<?php
/**
 * Reservation Notes.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Admin
 * @package  Hotelier/Admin/Meta Boxes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Meta_Box_Reservation_Notes' ) ) :

/**
 * HTL_Meta_Box_Reservation_Notes Class
 */
class HTL_Meta_Box_Reservation_Notes {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post;

		$args = array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'reservation_note'
		);

		remove_filter( 'comments_clauses', array( 'HTL_Comments', 'exclude_reservation_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'HTL_Comments', 'exclude_reservation_comments' ), 10, 1 );
		?>

		<ul class="htl-ui-scope reservation-notes__list">

			<?php if ( $notes ) : ?>

				<?php foreach( $notes as $note ) : ?>
					<li class="reservation-notes__item">
						<p class="reservation-notes__text"><?php echo esc_html( $note->comment_content ); ?></p>

						<span class="reservation-notes__date"><?php printf( esc_html__( 'Added on %1$s at %2$s', 'wp-hotelier' ), date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) ), date_i18n( get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></span>
					</li>
				<?php endforeach; ?>

			<?php else: ?>

				<li class="reservation-notes__item reservation-notes__item--empty"><?php esc_html_e( 'There are no notes yet.', 'wp-hotelier' ) ?></li>

			<?php endif; ?>

		</ul>

		<?php
	}
}

endif;
