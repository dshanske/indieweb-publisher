<?php
/**
 * Implements the Custom Logo feature
 * http://codex.wordpress.org/Custom_Logo
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

/**
 * Setup the WordPress core custom logo feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @uses indieweb_publisher_get_default_header_image()
 */
function indieweb_publisher_custom_logo_setup() {
	$args = array(
		'width'                  => 100,
		'height'                 => 100,
		'flex-width'             => true,
		'flex-height'            => true,
		'header-text'            => false,
	);

	$args = apply_filters( 'indieweb_publisher_custom_header_args', $args );

	add_theme_support( 'custom-logo', $args );

}

add_action( 'after_setup_theme', 'indieweb_publisher_custom_logo_setup' );

function indieweb_publisher_author_logo() { 
	$author_id = get_option( 'iw_default_author', get_option( 'admin_email' ) );
	printf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
			esc_url( home_url( '/' ) ),
			get_avatar( $author_id,
				100,
				'404',
				'',
				array(
					'class' => array( 'custom-logo' ),
					'extra-attr' => array(
						'itemprop' => 'logo'
					)
				)
		)
	);
}
