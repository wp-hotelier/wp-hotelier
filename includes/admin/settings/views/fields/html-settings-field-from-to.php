<?php
/**
 * Field "Input From-To"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
	$value = $this->options[ $args[ 'id' ] ];
} else {
	$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
}

$from = $value[ 'from' ];
$to   = $value[ 'to' ];

$options = array();

for ( $i = 0; $i <= 24; $i++ ) {
	$options[ $i ] = sprintf( '%02d', $i ) . ':00';
	$options[ $i + 25 ] = sprintf( '%02d', $i ) . ':30';
}
?>

<div class="htl-ui-setting htl-ui-setting--from-to htl-ui-setting--<?php echo esc_attr( $args[ 'id' ] ); ?>">
	<label class="htl-ui-label htl-ui-label--from-to"><?php esc_html_e( 'From:', 'wp-hotelier' ); ?>
		<select class="htl-ui-input htl-ui-input--select htl-ui-input--from-to" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][from]">

			<?php foreach ( $options as $option => $name ) : ?>
				<?php $selected = selected( $option, $from, false ); ?>

				<option value="<?php echo esc_attr( $option ); ?>" <?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>

		</select>
	</label>

	<label class="htl-ui-label htl-ui-label--from-to"><?php esc_html_e( 'To:', 'wp-hotelier' ); ?>
		<select class="htl-ui-input htl-ui-input--select htl-ui-input--from-to" name="hotelier_settings[<?php echo esc_attr( $args[ 'id' ] ); ?>][to]">

			<?php foreach ( $options as $option => $name ) : ?>
				<?php $selected = selected( $option, $to, false ); ?>

				<option value="<?php echo esc_attr( $option ); ?>" <?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
			<?php endforeach; ?>

		</select>
	</label>
</div>
