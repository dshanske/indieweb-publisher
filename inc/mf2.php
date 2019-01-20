<?php
/**
 * Code that Adds Support for Microformats 2
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.7
 */

function indieweb_publisher_mf2_body_class( $classes ) {
	if ( is_page_template() && 'hcard.php' === get_page_template_slug() ) {
		return $classes;
	} else if ( ! is_singular() ) {
		$classes[] = 'h-feed';
	} else {
		// Adds a class for microformats v2
		$classes[] = 'h-entry';
	}
	return $classes;
}

add_filter( 'body_class', 'indieweb_publisher_mf2_body_class' );

function indieweb_publisher_mf2_post_class( $classes ) {
	$classes = array_diff( $classes, array( 'hentry' ) );
	if ( ! is_singular() ) {
		// Adds a class for microformats v2
		$classes[] = 'h-entry';
		// add hentry to the same tag as h-entry
		$classes[] = 'hentry';
	}
	return $classes;
}

add_filter( 'post_class', 'indieweb_publisher_mf2_post_class' );


/**
 * Adds mf2 to avatar
 *
 * @param array             $args Arguments passed to get_avatar_data(), after processing.
 * @param int|string|object $id_or_email A user ID, email address, or comment object
 * @return array $args
 */
function indieweb_publisher_mf2_get_avatar_data( $args, $id_or_email ) {
	if ( ! isset( $args['class'] ) ) {
		$args['class'] = array( 'u-photo' );
	} else {
		if ( is_string( $args['class'] ) ) {
			$args['class'] = array( $args['class'] );
		}
		$args['class'][] = 'u-photo';
	}
		return $args;
}

add_filter( 'get_avatar_data', 'indieweb_publisher_mf2_get_avatar_data', 11, 2 );

/**
 * Adds custom classes to the array of comment classes.
 */
function indieweb_publisher_mf2_comment_class( $classes ) {
	$classes[] = 'u-comment';
	$classes[] = 'h-cite';
	return array_unique( $classes );
}

add_filter( 'comment_class', 'indieweb_publisher_mf2_comment_class', 11 );


/**
 * Wraps the_content in e-content
 */
function indieweb_publisher_the_content( $content ) {
	if ( is_feed() ) {
		return $content;
	}
	if ( empty( $content ) ) {
		return $content;
	}
	return sprintf( '<section class="e-content">%1$s</section>', $content );
}
add_filter( 'the_content', 'indieweb_publisher_the_content', 1 );

/**
 * Wraps the_excerpt in p-summary
 */
function indieweb_publisher_the_excerpt( $content ) {
	if ( is_feed() ) {
		return $content;
	}
	if ( empty( $content ) ) {
		return $content;
	}
	return sprintf( '<section class="e-summary">%1$s</section>', $content );
}

add_filter( 'the_excerpt', 'indieweb_publisher_the_excerpt', 1 );

