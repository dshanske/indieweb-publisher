<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Indieweb Publisher
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function indieweb_publisher_jetpack_setup() {
	add_theme_support(
		'infinite-scroll',
		array(
			'container' => 'main',
			'footer'    => 'page',
		)
	);
}

add_action( 'after_setup_theme', 'indieweb_publisher_jetpack_setup' );
add_filter( 'infinite_scroll_credit', 'indieweb_publisher_footer_credits' );
