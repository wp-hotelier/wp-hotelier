<?php
/**
 * Admin View: extensions & Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap hotelier">
	<h1><?php echo get_admin_page_title(); ?></h1>

	<?php if ( $addons ) : ?>

		<?php if ( isset( $addons->extensions ) ) : ?>
			<div class="hotelier-addons">
				<h2 class="hotelier-addons__title"><?php esc_html_e( 'Supercharge your hotel website with extensions built specifically for WP Hotelier.', 'hotelier' ); ?></h2>
				<p class="hotelier-addons__description"><?php esc_html_e( 'Premium extensions are special WordPress plugins that enhance, or extend, the core WP Hotelier functionalities.', 'hotelier' ); ?></p>

				<ul class="hotelier-addons__list">
					<?php foreach ( $addons->extensions as $extension ) :
						$title       = sanitize_text_field( $extension->title );
						$icon        = sanitize_text_field( $extension->icon );
						$description = sanitize_text_field( $extension->description );
						$link        = sanitize_text_field( $extension->link );
						?>
						<li class="hotelier-addons__extension">
							<a class="hotelier-addons__extension-thumbnail" href="<?php echo esc_url( $link ); ?>" style="background-image: url(<?php echo esc_url( $icon ); ?>);"></a>

							<div class="hotelier-addons__extension-content">
								<h4 class="hotelier-addons__extension-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h4>

								<div class="hotelier-addons__extension-description">
									<p><?php echo esc_html( $description ); ?></p>
								</div>

								<a class="hotelier-addons__extension-button" href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( 'View extension', 'hotelier' ); ?>"><?php esc_html_e( 'Get this extension', 'hotelier' ); ?></a>
							</div>
						</li>
					<?php endforeach; ?>
					<li></li>
				</ul>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<p><?php printf( __( 'Our catalog of WP Hotelier Extensions can be found on WP Hotelier here: <a href="%s">WP Hotelier Extensions</a>', 'hotelier' ), 'https://wphotelier.com/extensions/' ); ?></p>
	<?php endif; ?>
