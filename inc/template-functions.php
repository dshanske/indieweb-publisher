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
		printf( '<link rel="feed" type="text/html" href="%1$s" title="%2$s" />' . PHP_EOL, esc_url( get_post_type_archive_link( 'post' ) ), __( 'All Posts Feed', 'indieweb-publisher' ) );
	}
}
add_action( 'wp_head', 'indieweb_publisher_feed_header' );


/**
 * Returns Post Kind or Post Format
 */
function indieweb_publisher_get_post_kind() {
	if ( class_exists( 'Kind_Taxonomy' ) ) {
		return get_post_kind();
	}
	return get_post_format();
}

if ( ! function_exists( 'get_the_archive_thumbnail_url' ) ) {


	function get_the_archive_thumbnail_url() {
		$image_id = null;
		if ( is_tax() || is_category() || is_tag() ) {
			$term     = get_queried_object();
			$image_id = get_term_meta( $term->term_id, 'image', true );
		}
		if ( $image_id ) {
			return wp_get_attachment_image_url( $image_id, 'thumbnail', true );
		}
	}
}

if ( ! function_exists( 'get_the_archive_thumbnail' ) ) {

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
}

if ( ! function_exists( 'the_archive_thumbnail' ) ) {

	function the_archive_thumbnail() {
		echo get_the_archive_thumbnail();
	}
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

/**
 * Fix comment count so that it doesn't include pings/trackbacks
 */
add_filter( 'get_comments_number', 'indieweb_publisher_comment_count', 0 );
function indieweb_publisher_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments         = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $comments );

		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}

if ( ! function_exists( 'indieweb_publisher_show_full_name_comment_reply_to' ) ) :
	function indieweb_publisher_show_full_name_comment_reply_to() {
		$indieweb_publisher_general_options = get_option( 'indieweb_publisher_general_options' );
		if ( isset( $indieweb_publisher_general_options['show_full_name_comment_reply_to'] ) && $indieweb_publisher_general_options['show_full_name_comment_reply_to'] ) {
			return true;
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'indieweb_publisher_author_comment_reply_link' ) ) :
	/*
	 * Change the comment reply link to use 'Reply to [Author Name]'
	 */
	function indieweb_publisher_author_comment_reply_link( $args, $comment, $post ) {

		// If no comment author is blank, use 'Anonymous'
		if ( empty( $comment->comment_author ) ) {
			if ( ! empty( $comment->user_id ) ) {
				$user   = get_userdata( $comment->user_id );
				$author = $user->user_login;
			} else {
				$author = __( 'Anonymous', 'indieweb-publisher' );
			}
		} else {
			$author = $comment->comment_author;
		}

		// If the user provided more than a first name, use only first name if the theme is configured to do so
		if ( strpos( $author, ' ' ) && ! indieweb_publisher_show_full_name_comment_reply_to() ) {
			$author = substr( $author, 0, strpos( $author, ' ' ) );
		}

		// Replace Reply Text with "Reply to <Author Name>"
		$args['reply_text'] = __( 'Reply to', 'indieweb-publisher' ) . ' ' . $author;

		return $args;
	}
endif;

add_filter( 'comment_reply_link_args', 'indieweb_publisher_author_comment_reply_link', 420, 4 );

if ( ! function_exists( 'indieweb_publisher_comment_form_args' ) ) :
	/**
	 * Arguments for comment_form()
	 *
	 * @return array
	 */
	function indieweb_publisher_comment_form_args() {

		if ( ! is_user_logged_in() ) {
			$comment_notes_before = '';
			$comment_notes_after  = '';
		} else {
			$comment_notes_before = '';
			$comment_notes_after  = '';
		}

		$user      = wp_get_current_user();
		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$aria_req  = ( $req ? " aria-required='true'" : '' );

		$args = array(
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'title_reply'          => '',
			'title_reply_to'       => __( 'Leave a Reply for %s', 'indieweb-publisher' ),
			'cancel_reply_link'    => __( 'Cancel Reply', 'indieweb-publisher' ),
			'label_submit'         => __( 'Submit Comment', 'indieweb-publisher' ),
			'must_log_in'          => '<p class="must-log-in">' .
				sprintf(
					__( 'You must be <a href="%s">logged in</a> to post a comment.', 'indieweb-publisher' ),
					wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
				) . '</p>',
			'logged_in_as'         => '<p class="logged-in-as">' .
				sprintf(
					__( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'indieweb-publisher' ),
					admin_url( 'profile.php' ),
					$user->display_name,
					wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
				) . '</p>',
			'comment_notes_before' => $comment_notes_before,
			'comment_notes_after'  => $comment_notes_after,
			'fields'               => apply_filters(
				'comment_form_default_fields',
				array(
					'author' =>
						'<p class="comment-form-author"><label for="author">' . __( 'Name', 'indieweb-publisher' ) . '</label>' .
						( $req ? '' : '' ) .
						'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
						'"' . $aria_req . ' /></p>',
					'email'  =>
						'<p class="comment-form-email"><label for="email">' . __( 'Email', 'indieweb-publisher' ) . '</label>' .
						( $req ? '' : '' ) .
						'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) .
						'"' . $aria_req . ' /></p>',
					'url'    =>
						'<p class="comment-form-url"><label for="url">' . __( 'Website', 'indieweb-publisher' ) . '</label>' .
						'<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
						'" /></p>',
				)
			),
		);

		return $args;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_remove_textarea' ) ) :
	/**
	 * Move the comment form textarea above the comment fields
	 */
	function indieweb_publisher_remove_textarea( $defaults ) {
		$defaults['comment_field'] = '';

		return $defaults;
	}
endif;
add_filter( 'comment_form_defaults', 'indieweb_publisher_remove_textarea' );

if ( ! function_exists( 'indieweb_publisher_add_textarea' ) ) :
	/**
	 * Recreates the comment form textarea HTML for reinclusion in comment form
	 */
	function indieweb_publisher_add_textarea() {
		echo '<div id="main-reply-title"><h3>' . indieweb_publisher_comments_call_to_action_text() . '</h3></div>';
		echo '<div class="comment-form-reply-title"><p>' . __( 'Comment', 'indieweb-publisher' ) . '</p></div>';
		echo '<p class="comment-form-comment" id="comment-form-field"><textarea id="comment" name="comment" cols="60" rows="6" aria-required="true"></textarea></p>';
	}
endif;
add_action( 'comment_form_top', 'indieweb_publisher_add_textarea' );

if ( ! function_exists( 'indieweb_publisher_enhanced_comment_form' ) ) :
	/**
	 * Enqueue enhanced comment form JavaScript
	 */
	function indieweb_publisher_enhanced_comment_form() {
		wp_enqueue_script( 'enhanced-comment-form-js', get_template_directory_uri() . '/js/enhanced-comment-form.js', array( 'jquery' ), '1.0' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'indieweb_publisher_enhanced_comment_form' );

if ( ! function_exists( 'indieweb_publisher_option' ) ) :
	/*
	 * Returns true if enabled
	 *
	 */
	function indieweb_publisher_option( $option_name ) {
		$indieweb_publisher_general_options = get_option( 'indieweb_publisher_general_options' );

		if ( isset( $indieweb_publisher_general_options[ $option_name ] ) && $indieweb_publisher_general_options[ $option_name ] ) {
			return true;
		} else {
			return false;
		}
	}
endif;

/**
 * Returns Comments Call to Action text
 */
function indieweb_publisher_comments_call_to_action_text() {
	$comments_call_to_action = get_theme_mod( 'comments_call_to_action' );
	if ( isset( $comments_call_to_action ) && trim( $comments_call_to_action ) !== '' ) {
		return esc_attr( $comments_call_to_action );
	} else {
		return __( 'Write a Comment', 'indieweb-publisher' );
	}
}

/*
 * Return true if Auto-Set Featured Image as Post Cover is enabled and it hasn't
 * been disabled for this post.
 *
 * Returns true if the current post has Full Width Featured Image enabled.
 *
 * Returns false if not a Single post type or there is no Featured Image selected
 * or none of the above conditions are true.
 */
function indieweb_publisher_has_full_width_featured_image() {

	// If this isn't a Single post type or we don't have a Featured Image set
	if ( ! ( is_single() || is_page() ) || ! has_post_thumbnail() ) {
		return false;
	}

	$full_width_featured_image          = get_post_meta( get_the_ID(), 'full_width_featured_image' );
	$full_width_featured_image_disabled = get_post_meta( get_the_ID(), 'full_width_featured_image_disabled' );
	$indieweb_publisher_general_options = get_option( 'indieweb_publisher_general_options' );

	// If Auto-Set Featured Image as Post Cover is enabled and it hasn't been disabled for this post, return true.
	if ( isset( $indieweb_publisher_general_options['auto_featured_image_post_cover'] ) && $indieweb_publisher_general_options['auto_featured_image_post_cover'] && ! $full_width_featured_image_disabled ) {
		return true;
	}

	// If Use featured image as Post Cover has been checked in the Featured Image meta box, return true.
	if ( $full_width_featured_image ) {
		return true;
	}

	return false; // Default
}

/**
 * Return true if post has the custom field post_cover_overlay_post_title set to true
 */
function indieweb_publisher_post_has_post_cover_title() {
	$post_has_cover_title = get_post_meta( get_the_ID(), 'post_cover_overlay_post_title', true );

	$has_full_width_featured_image = indieweb_publisher_has_full_width_featured_image();

	$indieweb_publisher_general_options = get_option( 'indieweb_publisher_general_options' );

	// Allow site owner to set this option on a per-post basis using a Custom Field
	if ( ( $post_has_cover_title === '1' || $post_has_cover_title === 'true' ) && $has_full_width_featured_image ) {
		return true;
	} elseif ( ( $post_has_cover_title === '0' || $post_has_cover_title === 'false' ) && $has_full_width_featured_image ) {
		return false;
	}

	if ( isset( $indieweb_publisher_general_options['post_cover_overlay_post_title'] ) && $indieweb_publisher_general_options['post_cover_overlay_post_title'] && $has_full_width_featured_image ) {
		return true;
	}

	return false; // Default
}

/**
 * Add full-width-featured-image to body class when displaying a post with Full Width Featured Image enabled
 */
function indieweb_publisher_full_width_featured_image_body_class( $classes ) {
	if ( indieweb_publisher_has_full_width_featured_image() ) {
		$classes[] = 'full-width-featured-image';
	}

	return $classes;
}

add_filter( 'body_class', 'indieweb_publisher_full_width_featured_image_body_class' );

/**
 * Add post-cover-overlay-post-title to body class when displaying a post with Post Title Overlay on Post Cover enabled
 */
function indieweb_publisher_post_cover_title_body_class( $classes ) {
	if ( indieweb_publisher_post_has_post_cover_title() && indieweb_publisher_has_full_width_featured_image() ) {
		$classes[] = 'post-cover-overlay-post-title';
	}

	return $classes;
}

add_filter( 'body_class', 'indieweb_publisher_post_cover_title_body_class' );

/**
 * Add single-column-layout to body class when Use Single Column Layout option enabled
 */
function indieweb_publisher_single_column_layout_body_class( $classes ) {
	if ( indieweb_publisher_option( 'single_column_layout' ) ) {
		$classes[] = 'single-column-layout';
	}

	return $classes;
}

add_filter( 'body_class', 'indieweb_publisher_single_column_layout_body_class' );

/*
 * Add a checkbox for Post Covers to the featured image metabox
 */
function indieweb_publisher_featured_image_meta( $content ) {

	// If we don't have a featured image, nothing to do.
	if ( ! has_post_thumbnail() ) {
		return $content;
	}

	$post = get_post();

	// Meta key
	$meta_key = 'full_width_featured_image';

	// Text for checkbox
	$text = __( 'Use as post cover (full-width)', 'indieweb-publisher' );

	// Option type (for use when saving post data in indieweb_publisher_save_featured_image_meta()
	$option_type = 'enable';

	/*
	 If Auto-Set Featured Image as Post Cover enabled, this checkbox's functionality should reverse and
	 * allow for disabling Post Covers on a post-by-post basis.
	 */
	if ( indieweb_publisher_auto_featured_image_post_cover() ) {
		$meta_key    = 'full_width_featured_image_disabled';
		$text        = __( 'Disable post cover (full-width)', 'indieweb-publisher' );
		$option_type = 'disable';
	}

	// Get the current setting
	$value = esc_attr( get_post_meta( $post->ID, $meta_key, true ) );

	// Output the checkbox HTML
	$label  = '<label for="' . $meta_key . '" class="selectit"><input name="' . $meta_key . '" type="checkbox" id="' . $meta_key . '" value="1" ' . checked( $value, 1, false ) . '> ' . $text . '</label>';
	$label .= '<input type="hidden" name="full_width_featured_image_enable_disable" value="' . $option_type . '">';

	$label = wp_nonce_field( basename( __FILE__ ), 'indieweb_publisher_full_width_featured_image_meta_nonce' ) . $label;

	return $content .= $label;
}

add_filter( 'admin_post_thumbnail_html', 'indieweb_publisher_featured_image_meta' );

/*
 * Save the Featured Image meta box's post metadata for Post Cover options.
 */
function indieweb_publisher_save_featured_image_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if (
		! isset( $_POST['indieweb_publisher_full_width_featured_image_meta_nonce'] )
		|| ! wp_verify_nonce( $_POST['indieweb_publisher_full_width_featured_image_meta_nonce'], basename( __FILE__ ) )
	) {
		return $post_id;
	}

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	/* Get the posted data and sanitize it for use as an HTML class. */
	if ( isset( $_POST['full_width_featured_image'] ) ) {
		$new_meta_value = esc_attr( $_POST['full_width_featured_image'] );
		$meta_key       = 'full_width_featured_image';
	} elseif ( isset( $_POST['full_width_featured_image_disabled'] ) ) {
		$new_meta_value = esc_attr( $_POST['full_width_featured_image_disabled'] );
		$meta_key       = 'full_width_featured_image_disabled';
	} else {
		$new_meta_value = ''; // Empty value means we're unchecking this option
	}

	// Figure out which option was being unchecked (this routine handles two types)
	if ( isset( $_POST['full_width_featured_image_enable_disable'] ) ) {
		if ( $_POST['full_width_featured_image_enable_disable'] === 'enable' ) {
			$meta_key = 'full_width_featured_image';
		} elseif ( $_POST['full_width_featured_image_enable_disable'] === 'disable' ) {
			$meta_key = 'full_width_featured_image_disabled';
		}
	} else {
		$meta_key = 'full_width_featured_image'; // Default
	}

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value ) {
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	} /* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	} /* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value ) {
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'indieweb_publisher_save_featured_image_meta', 10, 2 );


if ( ! function_exists( 'indieweb_publisher_clean_content' ) ) :
	/**
	 * Cleans and returns the content for display as a Quote or Aside by stripping anything that might screw up formatting. This is necessary because we link Quotes and Asides to their own permalink. If the Quote or Aside contains a footnote with an anchor tag, or even just an anchor tag, then nesting anchor within anchor will break formatting.
	 */
	function indieweb_publisher_clean_content( $content ) {

		// Remove footnotes
		$content = preg_replace( '!<sup\s+.*?>.*?</sup>!is', '', $content );

		// Remove anchor tags
		$content = preg_replace( array( '"<a href(.*?)>"', '"</a>"' ), array( '', '' ), $content );

		return $content;
	}
endif;

/**
 * Handles Reply to Comment links properly when JavaScript is enabled
 */
function indieweb_publisher_replytocom() {
	if ( isset( $_GET['replytocom'] ) ) {
		$replytocom_comment_id = intval( $_GET['replytocom'] );
		$replytocom_post_id    = get_the_ID();
		?>
		<script type="text/javascript">
			addComment.moveForm("comment-<?php echo $replytocom_comment_id; ?>", "<?php echo $replytocom_comment_id; ?>", "respond", "<?php echo $replytocom_post_id; ?>");
			jQuery(function () {
				jQuery('#respond').show();
			});
			jQuery(function () {
				jQuery('.comment-form-reply-title').show();
			});
			jQuery(function () {
				jQuery('#main-reply-title').hide();
			});
		</script>
		<?php
	}
}

/*
 * Returns the number of approved webmentions, pings/trackbacks the current post has
 */
function indieweb_publisher_comment_count_mentions() {
	$args   = array(
		'post_id'  => get_the_ID(),
		'type__in' => array( 'pings', 'webmention' ),
		'status'   => 'approve',
	);
	$_query = new WP_Comment_Query();
	return count( $_query->query( $args ) );
}

/**
 * Returns the entry title meta category prefix (e.g., "<author name> in <category name>"; 'in' is the portion this function returns)
 */
function indieweb_publisher_entry_meta_category_prefix() {
	$prefix = __( 'in', 'indieweb-publisher' );

	return apply_filters( 'indieweb_publisher_entry_meta_category_prefix', $prefix );
}

/**
 * Returns the entry meta author prefix (e.g., "by <author name>"; 'by' is the portion this function returns)
 */
function indieweb_publisher_entry_meta_author_prefix() {
	$prefix = __( 'by', 'indieweb-publisher' );

	return apply_filters( 'indieweb_publisher_entry_meta_author_prefix', $prefix );
}

if ( ! function_exists( 'indieweb_publisher_cancel_comment_reply_link' ) ) :
	/**
	 * Returns the cancel comment reply link with #respond stripped out so it behaves with jQuery used to enhance comments
	 */
	function indieweb_publisher_cancel_comment_reply_link( $formatted_link ) {
		return str_ireplace( '#respond', '', $formatted_link );
	}
endif;

add_filter( 'cancel_comment_reply_link', 'indieweb_publisher_cancel_comment_reply_link', 10, 1 );

if ( ! function_exists( 'indieweb_publisher_html_tag_schema' ) ) :
	/**
	 * Returns the proper schema type
	 */
	function indieweb_publisher_html_tag_schema() {
		$schema = 'http://schema.org/';

		// Is single post
		if ( is_single() ) {
			$type = 'Article';
		} // Contact form page ID
		else {
			if ( is_page( 1 ) ) {
				$type = 'ContactPage';
			} // Is author page
			elseif ( is_author() ) {
				$type = 'ProfilePage';
			} // Is search results page
			elseif ( is_search() ) {
				$type = 'SearchResultsPage';
			} // Is of movie post type
			elseif ( is_singular( 'movies' ) ) {
				$type = 'Movie';
			} // Is of book post type
			elseif ( is_singular( 'books' ) ) {
				$type = 'Book';
			} else {
				$type = 'WebPage';
			}
		}

		echo 'itemscope="itemscope" itemtype="' . $schema . $type . '"';
	}
endif;

if ( ! function_exists( 'indieweb_publisher_show_page_load_progress_bar' ) ) :
	/**
	 * Echos the HTML and JavScript necessary to enable page load progress bar
	 */
	function indieweb_publisher_show_page_load_progress_bar() {
		?>
		<!-- Progress Bar - https://github.com/rstacruz/nprogress -->

		<div class="bar" role="bar"></div>
		<script type="text/javascript">
			NProgress.start();

			setTimeout(function () {

				NProgress.done();

				jQuery('.fade').removeClass('out');

			}, 1000);

			jQuery("#b-0").click(function () {
				NProgress.start();
			});
			jQuery("#b-40").click(function () {
				NProgress.set(0.4);
			});
			jQuery("#b-inc").click(function () {
				NProgress.inc();
			});
			jQuery("#b-100").click(function () {
				NProgress.done();
			});
		</script>

		<!-- End Progress Bar -->

		<?php
	}
endif;

if ( ! function_exists( 'indieweb_publisher_post_thumbnail_link_title' ) ) :
	function indieweb_publisher_post_thumbnail_link_title() {
		return indieweb_publisher_post_link_title_common();
	}
endif;

if ( ! function_exists( 'indieweb_publisher_post_link_title' ) ) :
	function indieweb_publisher_post_link_title() {
		return indieweb_publisher_post_link_title_common();
	}
endif;

if ( ! function_exists( 'indieweb_publisher_post_link_title_common' ) ) :
	function indieweb_publisher_post_link_title_common() {
		return esc_attr( sprintf( __( 'Permalink to %s', 'indieweb-publisher' ), the_title_attribute( 'echo=0' ) ) );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_get_the_title' ) ) :
	function indieweb_publisher_get_the_title() {
		if ( class_exists( 'Kind_Taxonomy' ) && Kind_Taxonomy::get_kind_info( get_post_kind_slug(), 'title' ) ) {
			return get_the_title();
		}
		return '';
	}
endif;


if ( ! function_exists( 'indieweb_publisher_is_multi_author' ) ) :
	function indieweb_publisher_is_multi_author() {
		if( class_exists( 'IndieWeb_Plugin' ) ) {
			return ( get_option( 'iw_single_author' ) ) ? false : true;
		}
		return is_multi_author();
	}
endif;

?>
