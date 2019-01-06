<?php

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function indieweb_publisher_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%1$s" />', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'indieweb_publisher_pingback_header' );

/**
 * Adds a rel-feed if the main page is not a list of posts
 */
function indieweb_publisher_feed_header() {
	if ( is_front_page() && 0 !== (int) get_option( 'page_for_posts', 0 ) ) {
		printf( '<link rel="feed" type="text/html" href="%1$s" title="%2$s" />' . PHP_EOL, esc_url( get_post_type_archive_link( 'post' ) ), __( 'All Posts Feed', 'iw26' ) );
	}
}
add_action( 'wp_head', 'indieweb_publisher_feed_header' );


function get_the_archive_thumbnail_url() {
	$image_id = null;
	if ( is_tax() || is_category() || is_tag() ) {
		$term     = get_queried_object();
		$image_id = get_term_meta( $term->term_id, 'image', true );
	}
	if ( $image_id ) {
		return wp_get_attachment_imagE_url( $image_id, 'thumbnail', true );
	}
}

function get_the_archive_thumbnail() {
	$image_id = null;
	if ( is_tax() || is_category() || is_tag() ) {
		$term     = get_queried_object();
		$image_id = get_term_meta( $term->term_id, 'image', true );
	}

	if ( $image_id ) {
		return wp_get_attachment_image( $image_id, 'thumbnail', true );
	}
	if ( is_tax( 'kind' ) ) {
		$term = get_queried_object();
		return Kind_Taxonomy::get_icon( $term->slug );
	}
}

function the_archive_thumbnail() {
	echo get_the_archive_thumbnail();
}

function indieweb_publisher_image_rss() {
	$url = get_the_archive_thumbnail_url();
	if ( ! $url ) {
		return;
	}
	echo '<image>' . PHP_EOL;
	echo '<url>' . $url . '</url>' . PHP_EOL;
	echo '<title>' . get_the_archive_title() . '</title>' . PHP_EOL;
	echo '<link>';
	self_link();
	echo '</link>' . PHP_EOL;
	echo '</image>' . PHP_EOL;
}

add_action( 'rss2_head', 'indieweb_publisher_image_rss' );
add_action( 'rss_head', 'indieweb_publisher_image_rss)' );
add_action( 'commentsrss2_head', 'indieweb_publisher_image_rss' );


if ( ! function_exists( 'has_content' ) ) {
	function has_content( $post = 0 ) {
		$post = get_post( $post );
		return ( ! empty( $post->post_content ) );
	}
}
