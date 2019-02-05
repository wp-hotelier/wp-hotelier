<?php
/**
 * Field "License Keys"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $options[ $args[ 'id' ] ] ) ) {
	$value = $options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$license = get_option( $args[ 'id' ] . '_active' );

if ( ! empty( $license ) && is_object( $license ) ) {

	// handle errors
	if ( false === $license->success ) {

		switch( $license->error ) {

			case 'expired' :

				$class = 'expired';
				$messages[] = sprintf(
					__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'wp-hotelier' ),
					date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
					'https://wphotelier.com/checkout/?edd_license_key=' . $value
				);

				$license_status = 'license-' . $class . '-notice';

				break;

			case 'revoked' :

				$class = 'error';
				$messages[] = sprintf(
					__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'wp-hotelier' ),
					'https://wphotelier.com/support/'
				);

				$license_status = 'license-' . $class . '-notice';

				break;

			case 'missing' :

				$class = 'error';
				$messages[] = sprintf(
					__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'wp-hotelier' ),
					'https://wphotelier.com/login/'
				);

				$license_status = 'license-' . $class . '-notice';

				break;

			case 'invalid' :
			case 'site_inactive' :

				$class = 'error';
				$messages[] = sprintf(
					__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'wp-hotelier' ),
					$args['name'],
					'https://wphotelier.com/login/'
				);

				$license_status = 'license-' . $class . '-notice';

				break;

			case 'item_name_mismatch' :

				$class = 'error';
				$messages[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-hotelier' ), $args['name'] );

				$license_status = 'license-' . $class . '-notice';

				break;

			case 'no_activations_left':

				$class = 'error';
				$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'wp-hotelier' ), 'https://wphotelier.com/login/' );

				$license_status = 'license-' . $class . '-notice';

				break;

			default :

				$class = 'error';
				$error = ! empty(  $license->error ) ?  $license->error : __( 'unknown_error', 'wp-hotelier' );
				$messages[] = sprintf( __( 'There was an error with this license key: %s. Please <a href="%s">contact our support team</a>.', 'wp-hotelier' ), $error, 'https://wphotelier.com/login/' );

				$license_status = 'license-' . $class . '-notice';
				break;
		}

	} else {

		switch( $license->license ) {

			case 'valid' :
			default:

				$class = 'valid';

				$now        = current_time( 'timestamp' );
				$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

				if ( 'lifetime' === $license->expires ) {

					$messages[] = __( 'License key never expires.', 'wp-hotelier' );

					$license_status = 'license-lifetime-notice';

				} elseif ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

					$messages[] = sprintf(
						__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank">Renew your license key</a>.', 'wp-hotelier' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
						'https://wphotelier.com/checkout/?edd_license_key=' . $value
					);

					$license_status = 'license-expires-soon-notice';

				} else {

					$messages[] = sprintf(
						__( 'Your license key expires on %s.', 'wp-hotelier' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
					);

					$license_status = 'license-expiration-date-notice';

				}

				break;

		}

	}

} else {
	$class = 'empty';

	$messages[] = sprintf(
		__( 'To receive updates, please enter your valid %s license key.', 'wp-hotelier' ),
		$args['name']
	);

	$license_status = null;
}

?>

<div class="htl-ui-setting htl-ui-setting--license-keys htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<label class="htl-ui-label htl-ui-label--text htl-ui-setting__description htl-ui-setting__description--license-keys htl-ui-setting__description--<?php echo esc_attr( $args[ 'id' ] ); ?>" for="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]"><?php esc_html_e( 'License Key', 'wp-hotelier' ); ?></label>

	<input type="text" class="htl-ui-input htl-ui-input--text" id="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>]" value="<?php echo esc_attr( $value ); ?>" />

	<?php if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) : ?>
		<input type="submit" class="htl-ui-button htl-ui-button--deactivate-license" name="<?php echo esc_attr( $args[ 'id' ] ); ?>'_deactivate" value="<?php esc_attr_e( 'Deactivate License',  'wp-hotelier' ); ?>" />
	<?php endif; ?>

	<?php if ( ! empty( $messages ) ) : ?>
		<?php foreach( $messages as $message ) : ?>
			<div class="htl-ui-license-key-notice htl-ui-license-key-notice--<?php echo esc_attr( $class ); ?> <?php echo esc_attr( $license_status ); ?>">
				<p class="htl-ui-license-key-notice__text"><?php echo wp_kses_post( $message ); ?></p>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php wp_nonce_field( sanitize_text_field( $args[ 'id' ] ) . '-nonce', sanitize_text_field( $args[ 'id' ] ) . '-nonce' ); ?>
</div>
