<?php
/**
 * Code that improves theme support for various plugins
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

/*
 * Wrapper function for a possible custom display of Syndication Links output
 */
function indieweb_publisher_syndication_links( $separator ) {
	$args  = array(
		'text'             => false,
		'icons'            => true,
		'show_text_before' => false,
	);
	$links = get_syndication_links( get_the_ID(), $args );
	if ( empty( $links ) ) {
		return;
	}
	printf( '<h3 class="syn-text">%1$s</h3>', get_option( 'syndication-links_text_before' ) );
	echo $links;
}

/*
 * Wrapper function for a possible custom display of Simple Location output
  */
function indieweb_publisher_simple_location( $separator ) {
	$location = Loc_View::get_location(
		get_post(),
		array(
			'icon' => false,
		)
	);
	if ( empty( $location ) ) {
		return;
	}
	printf( '<h3 class="site-location">%1$s</h3>', __( 'Location', 'indieweb-publisher' ) );
	printf( '<h3 class="site-location-detail">%1$s</h3>', $location );
}

function indieweb_publisher_indieweb_plugin_support() {
	/*
	 * Adds support for Syndication Links
	 */
	if ( class_exists( 'Syn_Meta' ) ) {
		remove_filter( 'the_content', array( 'Syn_Config', 'the_content' ), 30 );
		add_action( 'indieweb_publisher_after_post_published_date', 'indieweb_publisher_syndication_links', 11 );
	}

	/*
	 * Adds support for Simple Location
						  */
	if ( class_exists( 'Loc_View' ) ) {
		remove_filter( 'the_content', array( 'Loc_View', 'location_content' ), 12 );
		add_action( 'indieweb_publisher_after_post_published_date', 'indieweb_publisher_simple_location' );
	}

	if ( class_exists( 'Semantic_Linkbacks_Plugin' ) ) {
		remove_action( 'comment_form_before', array( 'Linkbacks_Handler', 'show_mentions' ) );
	}

}
add_action( 'init', 'indieweb_publisher_indieweb_plugin_support', 11 );


/*
 * Adds support for showing Subscribe to Comments Reloaded options after comment form fields
 */
if ( function_exists( 'subscribe_reloaded_show' ) ) {
	if ( get_option( 'subscribe_reloaded_show_subscription_box', 'yes' ) !== 'yes' ) {
		add_action( 'comment_form_logged_in_after', 'subscribe_reloaded_show' );
		add_action( 'comment_form_after_fields', 'subscribe_reloaded_show' );
	}
}

if ( ! function_exists( 'indieweb_publisher_jetpack_dark_overlay_fix_css' ) ) :
	/**
	 * Fixes an issue with a dark overlay that appears < 800px when the Jetpack Infinite Scroll
	 * module is enabled. See https://github.com/raamdev/indieweb-publisher/issues/72
	 */
	function indieweb_publisher_jetpack_dark_overlay_fix_css() {
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			wp_enqueue_style( 'indieweb-publisher-jetpack-infinite-scroll-dark-overlay-fix', get_template_directory_uri() . '/css/jetpack-infinite-scroll-dark-overlay-fix.css', array(), '1.0' );
		}
	}
endif;

/**
 * When the Disqus Commenting System is active and enabled, don't load our comment form enhancements
 */
require_once ABSPATH . 'wp-admin/includes/plugin.php'; // Required to use is_plugin_active() here
if (
	is_plugin_active( 'disqus-comment-system/disqus.php' )
	&& ! function_exists( 'indieweb_publisher_enhanced_comment_form' )
) :
	if ( get_option( 'disqus_active' ) !== '0' ) {
		function indieweb_publisher_enhanced_comment_form() {
			return;
		}
	}
endif;

/*
 * When Jetpack Comments is enabled, don't load our comment form enhancements
 */
if (
	class_exists( 'Jetpack' )
	&& Jetpack::is_module_active( 'comments' )
	&& ! function_exists( 'indieweb_publisher_enhanced_comment_form' )
) {
	function indieweb_publisher_enhanced_comment_form() {
		return;
	}
}

if ( ! function_exists( 'indieweb_publisher_wp_pagenavi_css' ) ) :
	/**
	 * Improves the style of WP-PageNavi when used with this theme
	 */
	function indieweb_publisher_wp_pagenavi_css() {
		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_enqueue_style( 'indieweb-publisher-wp-pagenavi-css', get_template_directory_uri() . '/css/wp-pagenavi.css', array(), '1.7' );
		}
	}
endif;
