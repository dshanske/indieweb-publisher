<?php
/**
 * Indieweb Publisher functions and definitions
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Indieweb Publisher 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 700;
} /* pixels */

if ( ! function_exists( 'indieweb_publisher_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which runs
	 * before the init hook. The init hook is too late for some features, such as indicating
	 * support post thumbnails.
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_setup() {

		/**
		 * Custom template tags for this theme.
		 */
		require get_template_directory() . '/inc/template-tags.php';

		/**
		 * Customizer additions.
		 */
		require get_template_directory() . '/inc/customizer.php';

		/**
		 * Template Functions
		 */
		require get_template_directory() . '/inc/template-functions.php';

		/**
		 * MF2 Compatibility Functions
		 */
		require get_template_directory() . '/inc/mf2.php';

		/**
		 * Make theme available for translation
		 * Translations can be filed in the /languages/ directory
		 */
		load_theme_textdomain( 'indieweb-publisher', get_template_directory() . '/languages' );

		/**
		 * Add default posts and comments RSS feed links to head
		 */
		add_theme_support( 'automatic-feed-links' );

		/**
		 * Add title tag support
		 */
		add_theme_support( 'title-tag' );

		/**
		 * Enable Custom Backgrounds
		 */
		add_theme_support(
			'custom-background',
			apply_filters(
				'indieweb_publisher_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Enable support for HTML5 markup.
		add_theme_support(
			'html5',
			array(
				'comment-list',
				'search-form',
				'comment-form',
				'gallery',
			)
		);

		/**
		 * Enable Post Thumbnails
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Add custom thumbnail size for use with featured images
		 */

		add_image_size( 'indieweb_publisher_post_thumbnail', 700, 700 );

		/**
		 * Enable editor style
		 */
		add_editor_style();

		/**
		 * Set max width of full screen visual editor to match content width
		 */
		set_user_setting( 'dfw_width', 700 );

		/**
		 * Set default value for Show Post Word Count theme option
		 */
		add_option( 'indieweb_publisher_general_options', array( 'show_post_word_count' => true ) );

		/**
		 * Set default value for Show Author Card theme option
		 */
		add_option( 'indieweb_publisher_general_options', array( 'show_author_card' => true ) );

		/**
		 * Set default value for Show Post Thumbnails theme option
		 */
		add_option( 'indieweb_publisher_excerpt_options', array( 'show_post_thumbnails' => false ) );

		/**
		 * This theme uses wp_nav_menu() in two locations.
		 */
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'indieweb-publisher' ),
				'single'  => __( 'Single Posts Menu', 'indieweb-publisher' ),
			)
		);

		/**
		 * Add support for Post Formats if Post Kinds are not loaded
		 */
		if ( ! class_exists( 'Kind_Taxonomy' ) ) {
			add_theme_support(
				'post-formats',
				array(
					'aside',
					'link',
					'gallery',
					'status',
					'quote',
					'chat',
					'image',
					'video',
					'audio',
				)
			);
		}
	}
endif; // indieweb_publisher_setup
add_action( 'after_setup_theme', 'indieweb_publisher_setup' );

/*
 * Add WP 4.1+ support for Title Tags.
 * See https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
 */
function indieweb_publisher_theme_slug_setup() {
	add_theme_support( 'title-tag' );
}

add_action( 'after_setup_theme', 'indieweb_publisher_theme_slug_setup' );

/**
 * Include additional plugin support routines
 */
require get_template_directory() . '/inc/plugin-support.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Register widgetized areas and update sidebar with default widgets
 *
 * @since Indieweb Publisher 1.0
 */
function indieweb_publisher_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Sidebar', 'indieweb-publisher' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer', 'indieweb-publisher' ),
			'id'            => 'sidebar-2',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Archive Page', 'indieweb-publisher' ),
			'id'            => 'archive-page',
			'before_widget' => '<div class="widget">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

add_action( 'widgets_init', 'indieweb_publisher_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function indieweb_publisher_scripts() {
	global $post;

	wp_enqueue_style( 'genericons-neue', get_template_directory_uri() . '/fonts/genericons-neue/Genericons-Neue.min.css', array(), '4.0.5' );

	wp_enqueue_script( 'indieweb-publisher-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( indieweb_publisher_option( 'show_page_load_progress_bar' )  ) {
		wp_enqueue_style( 'nprogress', get_template_directory_uri() . '/css/nprogress.css', array(), '0.1.3' );
		wp_enqueue_script( 'nprogress', get_template_directory_uri() . '/js/nprogress.js', array(), '0.1.3' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) && ! indieweb_publisher_hide_comments() ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image( $post->ID ) ) {
		wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}

	if ( is_singular() ) {
		wp_enqueue_script( 'fade-post-title', get_template_directory_uri() . '/js/fade-post-title.js', array( 'jquery' ) );
	}

	/**
	 * Load Jetpack Infinite Scroll Dark Overlay Bug Fix
	 */
	indieweb_publisher_jetpack_dark_overlay_fix_css();

	/*
	 * Load WP-PageNavi CSS enhancements, if applicable.
	 */
	indieweb_publisher_wp_pagenavi_css();
}

add_action( 'wp_enqueue_scripts', 'indieweb_publisher_scripts' );

if ( ! function_exists( 'indieweb_publisher_progress_bar_markup' ) ) :
	/**
	 * Insert Page Load Progress Bar markup
	 */
	function indieweb_publisher_progress_bar_markup() {
		if ( indieweb_publisher_option( 'show_page_load_progress_bar' ) ) {
			indieweb_publisher_show_page_load_progress_bar();
		}
	}
endif;
add_action( 'wp_footer', 'indieweb_publisher_progress_bar_markup' );

if ( ! function_exists( 'indieweb_publisher_remove_locale_stylesheet' ) ) :
	/**
	 * Remove locale_stylesheet() hook to prevent WordPress from
	 * automatically loading rtl.css. We need to load this manually
	 * so that we can load it before loading CSS from Customizer.
	 *
	 * @see https://github.com/raamdev/indieweb-publisher/issues/230
	 */
	function indieweb_publisher_remove_locale_stylesheet() {
		remove_action( 'wp_head', 'locale_stylesheet' );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_stylesheet_rtl' ) ) :
	/**
	 * Enqueue RTL stylesheet
	 */
	function indieweb_publisher_stylesheet_rtl() {
		wp_enqueue_style( 'indieweb-publisher-style', get_template_directory_uri() . '/css/rtl-style.css' );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_stylesheet' ) ) :
	/**
	 * Enqueue main stylesheet
	 */
	function indieweb_publisher_stylesheet() {
		wp_enqueue_style( 'indieweb-publisher-style', get_template_directory_uri() . '/css/default.min.css' );
	}
endif;

/*
 * Loads the PHP file that generates the Customizer CSS for the front-end
 */
function indieweb_publisher_customizer_css() {
	require get_template_directory() . '/css/customizer.css.php';
	wp_die();
}

/*
 * Enqueue the AJAX call to the dynamic Customizer CSS
 * See http://codex.wordpress.org/AJAX_in_Plugins
 */
function indieweb_publisher_customizer_stylesheet() {
	wp_enqueue_style( 'customizer', admin_url( 'admin-ajax.php' ) . '?action=indieweb_publisher_customizer_css', array(), '1.7' );

}

add_action( 'wp_ajax_indieweb_publisher_customizer_css', 'indieweb_publisher_customizer_css' );
add_action( 'wp_ajax_nopriv_indieweb_publisher_customizer_css', 'indieweb_publisher_customizer_css' );

/*
 * IMPORTANT: Customizer CSS *must* be called _after_ the main stylesheet,
 * to ensure that customizer-modified styles override the defaults.
 */
if ( is_rtl() ) {
	add_action( 'init', 'indieweb_publisher_remove_locale_stylesheet' );
	add_action( 'wp_enqueue_scripts', 'indieweb_publisher_stylesheet_rtl' );
} else {
	add_action( 'wp_enqueue_scripts', 'indieweb_publisher_stylesheet' );
}

add_action( 'wp_enqueue_scripts', 'indieweb_publisher_customizer_stylesheet' );

if ( ! function_exists( 'indieweb_publisher_wp_fullscreen_title_editor_style' ) ) :
	/**
	 * Enqueue the stylesheet for styling the full-screen visual editor post title
	 * so that it closely matches the front-end theme design. Hat tip to Helen:
	 * https://core.trac.wordpress.org/ticket/25783#comment:3
	 */
	function indieweb_publisher_wp_fullscreen_title_editor_style() {
		if ( 'post' === get_current_screen()->base ) {
			wp_enqueue_style( 'indieweb-publisher-wp-fullscreen-title', get_template_directory_uri() . '/css/wp-fullscreen-title.css', array(), '1.0' );
		}
	}
endif;

add_action( 'admin_enqueue_scripts', 'indieweb_publisher_wp_fullscreen_title_editor_style' );

if ( ! function_exists( 'indieweb_publisher_get_footer_credits' ) ) :
	/**
	 * Returns the theme's footer credits
	 *
	 * @return string
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_get_footer_credits() {
		return sprintf(
			'%1$s',
			sprintf( __( '%1$s empowered by %2$s', 'indieweb-publisher' ), '<a href="' . esc_url( 'http://independentpublisher.me' ) . '" rel="designer" title="Indieweb Publisher: A beautiful reader-focused WordPress theme, for you.">Indieweb Publisher</a>', '<a href="http://wordpress.org/" rel="generator" title="WordPress: A free open-source publishing platform">WordPress</a>' )
		);
	}
endif;

/**
 * Implement the Custom Logo feature
 */
require get_template_directory() . '/inc/custom-logo.php';


