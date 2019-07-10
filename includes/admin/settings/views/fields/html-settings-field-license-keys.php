<?php
/**
 * Field "License Keys"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$license = get_option( $args[ 'id' ] . '_active' );
$notice_type = 'error';

if ( ! empty( $license ) && is_object( $license ) ) {

	// handle errors
	if ( false === $license->success ) {

		switch( $license->error ) {

			case 'expired' :

				$messages[] = sprintf(
					__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'wp-hotelier' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
					'https://wphotelier.com/checkout/?edd_license_key=' . $value
				);

				break;

			case 'revoked' :

				$messages[] = sprintf(
					__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'wp-hotelier' ),
					'https://wphotelier.com/support/'
				);

				break;

			case 'missing' :

				$messages[] = sprintf(
					__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'wp-hotelier' ),
					'https://wphotelier.com/login/'
				);

				break;

			case 'invalid' :
			case 'site_inactive' :

				$messages[] = sprintf(
					__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'wp-hotelier' ),
					$args['name'],
					'https://wphotelier.com/login/'
				);

				break;

			case 'item_name_mismatch' :

				$messages[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-hotelier' ), $args['name'] );

				break;

			case 'no_activations_left':

				$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'wp-hotelier' ), 'https://wphotelier.com/login/' );

				break;

			default :

				$error = ! empty(  $license->error ) ?  $license->error : __( 'unknown_error', 'wp-hotelier' );
				$messages[] = sprintf( __( 'There was an error with this license key: %s. Please <a href="%s">contact our support team</a>.', 'wp-hotelier' ), $error, 'https://wphotelier.com/login/' );

				break;
		}

	} else {

		switch( $license->license ) {

			case 'valid' :
			default:

				$notice_type = 'success';
				$now         = current_time( 'timestamp' );
				$expiration  = strtotime( $license->expires, current_time( 'timestamp' ) );

				if ( 'lifetime' === $license->expires ) {

					$messages[] = __( 'License key never expires.', 'wp-hotelier' );

				} elseif ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

					$notice_type = 'info';

					$messages[] = sprintf(
						__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank">Renew your license key</a>.', 'wp-hotelier' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
						'https://wphotelier.com/checkout/?edd_license_key=' . $value
					);

				} else {

					$messages[] = sprintf(
						__( 'Your license key expires on %s.', 'wp-hotelier' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
					);
				}

				break;
		}
	}
}

?>

<div class="htl-ui-setting htl-ui-setting--license-keys htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<input type="text" class="htl-ui-input htl-ui-input--text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" />

	<?php if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) : ?>
		<input type="submit" class="htl-ui-button htl-ui-button--deactivate-license" name="<?php echo esc_attr( $args[ 'id' ] ); ?>'_deactivate" value="<?php esc_attr_e( 'Deactivate License',  'wp-hotelier' ); ?>" />
	<?php endif; ?>

	<?php if ( ! empty( $messages ) ) : ?>
		<?php foreach( $messages as $message ) : ?>
			<?php htl_ui_print_notice( $message, $notice_type, array( 'htl-ui-setting__description' ) ); ?>
		<?php endforeach; ?>
	<?php else : ?>
		<label class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--license-keys htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]">
			<?php echo wp_kses_post( sprintf(
				__( 'To receive updates, please enter your valid %s license key.', 'wp-hotelier' ),
				'<code>' . $args['name'] . '</code>'
			) ); ?>
		</label>

	<?php endif; ?>

	<?php wp_nonce_field( sanitize_text_field( $args[ 'id' ] ) . '-nonce', sanitize_text_field( $args[ 'id' ] ) . '-nonce' ); ?>
</div>
