<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

if ( ! function_exists( 'indieweb_publisher_the_posts_navigation' ) ) :
	/**
	 * Customized Post Navigation
	 */
	function indieweb_publisher_the_posts_navigation() {
		if ( function_exists( 'wp_pagenavi' ) ) { // WP-PageNavi Support
			wp_pagenavi();
		} else {
			the_posts_navigation( 
				array(
					'mid_size' => 2,
					'prev_text' => sprintf( '<button><span class="meta-nav">&larr;</span>%1$s</button>', __( 'Older Posts', 'indieweb-publisher' ) ),
					'next_text' => sprintf( '<button><span class="meta-nav">&rarr;</span>%1$s</button>', __( 'Newer Posts', 'indieweb-publisher' ) )
				)
			);
		}
	}
endif; // indieweb_publisher_the_posts_navigation

if ( ! function_exists( 'indieweb_publisher_pings' ) ) :
	/**
	 * Creates a custom query for pingbacks/trackbacks (i.e., 'pings')
	 * and displays them. Using this custom query instead of
	 * wp_list_comments() allows us to always show all pings,
	 * even when we're showing paginated comments.
	 *
	 * @since Indieweb Publisher 1.0
	 *
	 * @deprecated 1.7 No longer used in code; replaced by indieweb_publisher_mentions()
	 * @see indieweb_publisher_mentions()
	 */
	function indieweb_publisher_pings() {
		$args        = array(
			'post_id' => get_the_ID(),
			'type'    => 'pings',
			'status'  => 'approve',
		);
		$pings_query = new WP_Comment_Query();
		$pings       = $pings_query->query( $args );

		if ( $pings ) {
			foreach ( $pings as $ping ) {
				?>
				<li <?php comment_class( '', $ping->comment_ID ); ?> id="li-comment-<?php echo $ping->comment_ID; ?>">
					<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link( $ping->comment_ID ) ); ?>
					<span> <?php edit_comment_link( __( '(Edit)', 'indieweb-publisher' ), '  ', '' ); ?></span>
				</li>
				<?php
			}
		}
	}
endif; // ends check for indieweb_publisher_pings()

if ( ! function_exists( 'indieweb_publisher_mentions' ) ) :
	/**
	 * Creates a custom query for webmentions, pings, and trackbacks
	 * and displays them using this custom query instead of
	 * wp_list_comments() allows us to always show all webmentions,
	 * even when we're showing paginated comments.
	 *
	 * @since Indieweb Publisher 1.7
	 */
	function indieweb_publisher_mentions() {
		$args          = array(
			'post_id'  => get_the_ID(),
			'type__in' => array( 'pings', 'webmention' ),
			'status'   => 'approve',
		);
		$mention_query = new WP_Comment_Query();
		$mentions      = $mention_query->query( $args );

		if ( $mentions ) {
			foreach ( $mentions as $mention ) {
				?>
				<li <?php comment_class( '', $mention->comment_ID ); ?> id="li-comment-<?php echo $mention->comment_ID; ?>">
					<?php if ( $mention->comment_type !== 'webmention' ) : // Webmentions already include author in the comment text ?>
						<?php printf( '<cite class="fn">%s</cite>', get_comment_author_link( $mention->comment_ID ) ); ?>
						<small><?php printf( '%1$s', get_comment_date() ); ?></small>
					<?php endif; ?>
					<?php comment_text( $mention->comment_ID ); ?>
				</li>
				<?php
			}
		}
	}
endif; // ends check for indieweb_publisher_mentions()

if ( ! function_exists( 'indieweb_publisher_posted_author' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_posted_author() {
		/**
		 * This function gets called outside the loop (in header.php),
		 * so we need to figure out the post author ID and Nice Name manually.
		 */
		global $wp_query;
		$post_author_id        = $wp_query->post->post_author;
		$post_author_nice_name = get_the_author_meta( 'display_name', $post_author_id );
		if (  1 === (int) get_option( 'iw_author_url', 0 ) ) {
			$user_url = get_the_author_meta( 'user_url', $post_author_id );
			if ( empty( $user_url ) ) {
				$user_url = get_author_posts_url( $post_author_id );
			}
		} else {
			$user_url = get_author_posts_url( $post_author_id );
		}

		printf(
			'<span class="byline"><a href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( $user_url ),
			esc_attr( sprintf( __( 'View %s author page', 'indieweb-publisher' ), $post_author_nice_name ) ),
			esc_html( $post_author_nice_name )
		);
	}
endif;

if ( ! function_exists( 'indieweb_publisher_posted_author_cats' ) ) :
	/**
	 * Prints HTML with meta information for the current author and post categories.
	 *
	 * Only prints author name when Multi-Author Mode is enabled.
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_posted_author_cats() {

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'indieweb-publisher' ) );

		if ( ( ! post_password_required() && comments_open() && ! indieweb_publisher_hide_comments() ) || ( ! post_password_required() && ! get_post_format() ) || indieweb_publisher_option( 'show_date_entry_meta' ) ) {
			$separator = apply_filters( 'indieweb_publisher_entry_meta_separator', '|' );
		} else {
			$separator = '';
		}

		if ( indieweb_publisher_is_multi_author() ) :
			if ( $categories_list && indieweb_publisher_categorized_blog() ) :
				echo '<span class="cat-links">';
				printf(
					'<a href="%1$s" title="%2$s">%3$s</a> %4$s %5$s',
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_attr( sprintf( __( 'View all posts by %s', 'indieweb-publisher' ), get_the_author() ) ),
					esc_html( get_the_author() ),
					indieweb_publisher_entry_meta_category_prefix(),
					$categories_list
				);
				echo '</span> <span class="sep"> ' . $separator . '</span>';
			else :
				echo '<span class="cat-links">';
				printf(
					'%1$s <a href="%2$s" title="%3$s">%4$s</a>',
					indieweb_publisher_entry_meta_author_prefix(),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_attr( sprintf( __( 'View all posts by %s', 'indieweb-publisher' ), get_the_author() ) ),
					esc_html( get_the_author() )
				);
				echo '</span>';
			endif; // End if categories
		else : // not Multi-Author Site
			if ( $categories_list && indieweb_publisher_categorized_blog() ) :
				echo '<span class="cat-links">';
				printf(
					'%1$s %2$s',
					indieweb_publisher_entry_meta_category_prefix(),
					$categories_list
				);
				echo '</span> <span class="sep"> ' . $separator . '</span>';
			else :
				echo '<span class="cat-links">';
				echo '</span>';
			endif; // End if categories
		endif; // End if multi author
	}
endif;

if ( ! function_exists( 'indieweb_publisher_post_categories' ) ) :
	function indieweb_publisher_post_categories() {
		if ( ! indieweb_publisher_option( 'show_category_entry_meta' ) ) {
			return;
		}
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'indieweb-publisher' ) );
		if ( $categories_list && indieweb_publisher_categorized_blog() ) {
			printf( '<h3 class="post-category-title">%1$s</h3>', __( 'Categories', 'indieweb-publisher' ) );
			printf( '<span class="post-categories">%1$s</span>', $categories_list );
		}
	}
endif;

add_action( 'indieweb_publisher_after_post_published_date', 'indieweb_publisher_post_categories' );

if ( ! function_exists( 'indieweb_publisher_post_series' ) ) :
	function indieweb_publisher_post_series() {
		if ( taxonomy_exists( 'series' ) ) {
			$series_list = get_the_term_list( get_the_ID(), 'series', '', _x( ', ', 'Used between list items, there is a space after the comma.', 'indieweb-publisher' ) );
			if ( $series_list ) {
				printf( '<h3 class="post-series-title">%1$s</h3>', __( 'Series', 'indieweb-publisher' ) );
				printf( '<span class="post-series">%1$s</span>', $series_list );
			}
		}
	}
endif;

add_action( 'indieweb_publisher_after_post_published_date', 'indieweb_publisher_post_series' );



if ( ! function_exists( 'indieweb_publisher_posted_on_date' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_posted_on_date() {
		if ( indieweb_publisher_option( 'show_time_entry_meta' ) ) {
			$time_string = '<time class="entry-date dt-published" itemprop="datePublished" pubdate="pubdate" datetime="%1$s">%2$s %3$s</time>';
		} else {
			$time_string = '<time class="entry-date dt-published" itemprop="datePublished" pubdate="pubdate" datetime="%1$s">%2$s</time>';
		}
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			get_the_date(),
			get_the_time()
		);

		printf(
			'<a class="u-url" href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_title() ),
			$time_string
		);
	}
endif;

if ( ! function_exists( 'indieweb_publisher_post_updated_date' ) ) :
	/**
	 * Prints HTML with meta information for the current post's last updated date/time.
	 *
	 * @since Indieweb Publisher 1.4
	 */
	function indieweb_publisher_post_updated_date() {
		printf(
			'<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date-modified dt-updated" datetime="%3$s" moddate="moddate">%4$s</time></a>',
			esc_url( get_permalink() ),
			esc_attr( get_the_title() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
	}
endif;

if ( ! function_exists( 'indieweb_publisher_continue_reading_link' ) ) :
	/**
	 * Prints HTML with Continue Reading link
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_continue_reading_link() {
		$text = apply_filters( 'indieweb_publisher_continue_reading_link_text', ' ' . __( 'Continue Reading &rarr;', 'indieweb-publisher' ) );

		printf(
			'<div class="enhanced-excerpt-read-more"><a class="read-more" href="%1$s">%2$s</a></div>',
			esc_url( get_permalink() ),
			esc_html( $text )
		);
	}
endif;

if ( ! function_exists( 'indieweb_publisher_continue_reading_text' ) ) :
	/**
	 * Returns Continue Reading text for usage in the_content()
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_continue_reading_text() {
		return apply_filters( 'indieweb_publisher_continue_reading_text', __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'indieweb-publisher' ) );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_categorized_blog' ) ) :
	/**
	 * Returns true if a blog has more than 1 category
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_categorized_blog() {
		if ( ! indieweb_publisher_option( 'show_category_entry_meta' ) ) {
			return false;
		}
		if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
			// Create an array of all the categories that are attached to posts
			$all_the_cool_cats = get_categories(
				array(
					'hide_empty' => 1,
				)
			);

			// Count the number of categories that are attached to the posts
			$all_the_cool_cats = count( $all_the_cool_cats );

			set_transient( 'all_the_cool_cats', $all_the_cool_cats );
		}

		if ( '1' != $all_the_cool_cats ) {
			// This blog has more than 1 category so indieweb_publisher_categorized_blog should return true
			return true;
		} else {
			// This blog has only 1 category so indieweb_publisher_categorized_blog should return false
			return false;
		}
	}
endif;

/**
 * Flush out the transients used in indieweb_publisher_categorized_blog
 *
 * @since Indieweb Publisher 1.0
 */
function indieweb_publisher_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}

add_action( 'edit_category', 'indieweb_publisher_category_transient_flusher' );
add_action( 'save_post', 'indieweb_publisher_category_transient_flusher' );

if ( ! function_exists( 'indieweb_publisher_post_categories' ) ) :
	/**
	 * Returns categories for current post with separator.
	 * Optionally returns only a single category.
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_post_categories( $separator = ', ', $single = false ) {
		$separator = apply_filters( 'indieweb_publisher_post_categories_separator', $separator );
		$single    = apply_filters( 'indieweb_publisher_post_categories_single', $single );

		if ( $single === false ) {
			$categories = get_the_category_list( $separator );
			$output     = $categories;
		} else { // Only need one category
			$categories = get_the_category();
			$output     = '';
			if ( $categories ) {
				foreach ( $categories as $category ) {
					$output .= '<a class="p-category" href="' . get_category_link( $category->term_id ) . '" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'indieweb-publisher' ), $category->name ) ) . '">' . $category->cat_name . '</a>';
					if ( $single ) {
						break;
					}
				}
			}
		}

		return $output;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_site_info' ) ) :
	/**
	 * Outputs site info for display on non-single pages
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_site_info() {
		?>
		<?php 
		if ( has_custom_logo() ) {
			the_custom_logo(); 
		} else if ( ! indieweb_publisher_is_multi_author() ) {
			indieweb_publisher_author_logo();
		}
		?>
		
		<div class="site-title">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
		</div>
		<div class="site-description"><?php bloginfo( 'description' ); ?></div>
		<?php
	}
endif;

if ( ! function_exists( 'indieweb_publisher_posted_author_card' ) ) :
	/**
	 * Outputs post author info for display on single posts
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_posted_author_card() {
		/**
		 * This function gets called outside the loop (in header.php),
		 * so we need to figure out the post author ID and Nice Name manually.
		 */
		global $wp_query;
		$post_author_id = $wp_query->post->post_author;
		if (  1 === (int) get_option( 'iw_author_url', 0 ) ) {
			$user_url = get_the_author_meta( 'user_url', $post_author_id );
			if ( empty( $user_url ) ) {
				$user_url = get_author_posts_url( $post_author_id );
			}
		} else {
			$user_url = get_author_posts_url( $post_author_id );
		}
		

		$show_avatars   = get_option( 'show_avatars' );
		?>
		<aside class="p-author h-card">
		<?php if ( ( ! $show_avatars || $show_avatars === 0 ) && ! indieweb_publisher_is_multi_author_mode() && get_header_image() ) : ?>
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<img class="no-grav" src="<?php echo esc_url( get_header_image() ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
			</a>
		<?php else : ?>
			<a class="site-logo" href="<?php echo $user_url; ?>">
				<?php echo get_avatar( $post_author_id, 100 ); ?>
			</a>
		<?php endif; ?>

		<div class="site-title"><?php indieweb_publisher_posted_author(); ?></div>
		<div class="site-description"><?php the_author_meta( 'description', $post_author_id ); ?></div>

		</aside>
		<div class="site-published-separator"></div>
		<h2 class="site-published"><?php _e( 'Published', 'indieweb-publisher' ); ?></h2>
		<h2 class="site-published-date"><?php indieweb_publisher_posted_on_date(); ?></h2>
		<?php
		/*
		Show last updated date if the post was modified AND
					Show Updated Date on Single Posts option is enabled AND
						'indieweb_publisher_hide_updated_date' Custom Field is not present on this post */
		?>
		<?php
		if ( get_the_modified_date() !== get_the_date() &&
			indieweb_publisher_option( 'show_updated_date_on_single' ) &&
			! get_post_meta( get_the_ID(), 'indieweb_publisher_hide_updated_date', true )
		) :
			?>
			<h2 class="site-published"><?php _e( 'Updated', 'indieweb-publisher' ); ?></h2>
			<h2 class="site-published-date"><?php indieweb_publisher_post_updated_date(); ?></h2>
		<?php endif; ?>

		<?php do_action( 'indieweb_publisher_after_post_published_date' ); ?>
		<?php
	}
endif;

if ( ! function_exists( 'indieweb_publisher_posted_author_bottom_card' ) ) :
	/**
	 * Outputs post author info for display on bottom of single posts
	 *
	 * @since Indieweb Publisher 1.0
	 */
	function indieweb_publisher_posted_author_bottom_card() {
		if ( ! indieweb_publisher_option( 'show_author_card' ) ) {
			return; // This option has been disabled
		}

		do_action( 'indieweb_publisher_before_post_author_bottom_card' );
		?>
		<div class="post-author-bottom">
			<div class="post-author-card">
				<a class="site-logo" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 100 ); ?>
				</a>

				<div class="post-author-info">
					<div class="site-title">
						<?php indieweb_publisher_posted_author(); ?>
					</div>

					<div class="site-description"><?php the_author_meta( 'description' ); ?></div>
				</div>
				<div class="post-published-date">
					<h2 class="site-published"><?php _e( 'Published', 'indieweb-publisher' ); ?></h2>
					<h2 class="site-published-date"><?php indieweb_publisher_posted_on_date(); ?></h2>
					<?php
					/*
					Show last updated date if the post was modified AND
							Show Updated Date on Single Posts option is enabled AND
								'indieweb_publisher_hide_updated_date' Custom Field is not present on this post */
					?>
					<?php
					if ( get_the_modified_date() !== get_the_date() &&
						indieweb_publisher_option( 'show_updated_date_on_single' ) &&
						! get_post_meta( get_the_ID(), 'indieweb_publisher_hide_updated_date', true )
					) :
						?>
						<h2 class="site-published"><?php _e( 'Updated', 'indieweb-publisher' ); ?></h2>
						<h2 class="site-published-date"><?php indieweb_publisher_post_updated_date(); ?></h2>
					<?php endif; ?>

					<?php do_action( 'indieweb_publisher_after_post_published_date' ); ?>

				</div>
			</div>
		</div>
		<!-- .post-author-bottom -->
		<?php
		do_action( 'indieweb_publisher_after_post_author_bottom_card' );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_get_post_date' ) ) :
	/**
	 * Returns post date formatted for display in theme
	 *
	 * @return string
	 */
	function indieweb_publisher_get_post_date() {
		if ( ( comments_open() && ! indieweb_publisher_hide_comments() && ! get_post_format() ) ) {
			$separator = ' <span class="sep"> ' . apply_filters( 'indieweb_publisher_entry_meta_separator', '|' ) . ' </span>';
		} else {
			$separator = '';
		}

		return indieweb_publisher_posted_on_date() . $separator;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_full_width_featured_image' ) ) :
	/**
	 * Show Full Width Featured Image on single pages if post has full width featured image selected
	 * or if Auto-Set Featured Image as Post Cover option is enabled
	 */
	function indieweb_publisher_full_width_featured_image() {
		if ( indieweb_publisher_has_full_width_featured_image() ) {
			while ( have_posts() ) :
				the_post();
				if ( has_post_thumbnail() ) :
					if ( indieweb_publisher_post_has_post_cover_title() ) :
						$featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'indieweb_publisher_full_width_featured_image_size', 'indieweb_publisher_post_thumbnail' ) );
						$featured_image_url = $featured_image_url[0];
						?>
						<div class="post-cover-title-wrapper">
							<div class="post-cover-title-image" style="background-image:url('<?php echo $featured_image_url; ?>');"></div>
							<div class="post-cover-title-head">
								<header class="post-cover-title">
									<h1 class="entry-title p-name" itemprop="name">
										<?php echo get_the_title(); ?>
									</h1>
									<?php $subtitle = get_post_meta( get_the_id(), 'indieweb_publisher_post_cover_subtitle', true ); ?>
									<?php if ( $subtitle ) : ?>
										<h2 class="entry-subtitle">
											<?php echo $subtitle; ?>
										</h2>
									<?php endif; ?>
									<?php if ( ! is_page() ) : ?>
										<h3 class="entry-title-meta">
												<span class="entry-title-meta-author">
													<a class="author-avatar" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
														<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
													</a>
													<?php
													if ( ! indieweb_publisher_categorized_blog() ) {
														echo indieweb_publisher_entry_meta_author_prefix() . ' ';
													}
													indieweb_publisher_posted_author();
													?>
												</span>
											<?php
											if ( indieweb_publisher_categorized_blog() ) {
												echo indieweb_publisher_entry_meta_category_prefix() . ' ' . indieweb_publisher_post_categories();
											}
											?>
											<span class="entry-title-meta-post-date">
													<span class="sep"> <?php echo apply_filters( 'indieweb_publisher_entry_meta_separator', '|' ); ?> </span>
												<?php indieweb_publisher_posted_on_date(); ?>
												</span>
											<?php do_action( 'indieweb_publisher_entry_title_meta', $separator = ' | ' ); ?>
										</h3>
									<?php endif; ?>
								</header>
							</div>
						</div>
						<?php
					else :
						the_post_thumbnail( apply_filters( 'indieweb_publisher_full_width_featured_image_size', 'indieweb_publisher_post_thumbnail' ), array( 'class' => 'full-width-featured-image' ) );
					endif;
				endif;
			endwhile; // end of the loop.
		}
	}
endif;

if ( ! function_exists( 'indieweb_publisher_search_stats' ) ) :
	/**
	 * Returns stats for search results
	 */
	function indieweb_publisher_search_stats() {
		global $wp_query;
		$total            = $wp_query->found_posts;
		$total_pages      = $wp_query->max_num_pages; // The total number of pages
		$current_page_num = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$pagination_info  = '';

		$pagination_info = sprintf( __( 'this is page <strong>%1$s</strong> of <strong>%2$s</strong>', 'indieweb-publisher' ), number_format_i18n( $current_page_num ), number_format_i18n( $total_pages ) );
		$stats_text      = sprintf( _n( 'Found one search result for <strong>%2$s</strong>.', 'Found %1$s search results for <strong>%2$s</strong> (%3$s).', $total, 'indieweb-publisher' ), number_format_i18n( $total ), get_search_query(), $pagination_info );

		return wpautop( $stats_text );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_taxonomy_archive_stats' ) ) :
	/**
	 * Returns taxonomy archive stats and current page info for use in taxonomy archive descriptions
	 */
	function indieweb_publisher_taxonomy_archive_stats( $taxonomy = 'category' ) {

		// There's no point in showing page numbers of we're using Jetpack's Infinite Scroll module
		if ( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			return '';
		}

		global $wp_query;
		$total            = $wp_query->found_posts;
		$total_pages      = $wp_query->max_num_pages; // The total number of pages
		$current_page_num = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$pagination_info  = '';
		$stats_text       = '';

		$pagination_info = sprintf( __( 'this is page <strong>%1$s</strong> of <strong>%2$s</strong>', 'indieweb-publisher' ), number_format_i18n( $current_page_num ), number_format_i18n( $total_pages ) );

		if ( $taxonomy === 'category' ) {
			$stats_text = sprintf( _n( 'There is one post filed in <strong>%2$s</strong>.', 'There are %1$s posts filed in <strong>%2$s</strong> (%3$s).', $total, 'indieweb-publisher' ), number_format_i18n( $total ), single_term_title( '', false ), $pagination_info );
		} elseif ( $taxonomy === 'post_tag' ) {
			$stats_text = sprintf( _n( 'There is one post tagged <strong>%2$s</strong>.', 'There are %1$s posts tagged <strong>%2$s</strong> (%3$s).', $total, 'indieweb-publisher' ), number_format_i18n( $total ), single_term_title( '', false ), $pagination_info );
		}

		return wpautop( $stats_text );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_date_archive_description' ) ) :
	/**
	 * Returns the Date Archive description
	 */
	function indieweb_publisher_date_archive_description() {
		global $wp_query;
		$total             = $wp_query->found_posts; // The total number of posts found for this query
		$total_pages       = $wp_query->max_num_pages; // The total number of pages
		$current_page_num  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$pagination_info   = '';
		$date_archive_meta = '';

		$pagination_info = sprintf( __( 'this is page <strong>%1$s</strong> of <strong>%2$s</strong>', 'indieweb-publisher' ), number_format_i18n( $current_page_num ), number_format_i18n( $total_pages ) );

		/**
		 * Only proceed if we're on the first page and the description has not been overridden via indieweb_publisher_custom_date_archive_meta
		 */
		if ( trim( $date_archive_meta ) === '' ) {
			if ( is_year() && ( get_the_date( 'Y' ) != date( 'Y' ) ) ) {
				$date_archive_meta = sprintf( _n( 'There was one post published in %2$s.', 'There were %1$s posts published in %2$s (%3$s).', $total, 'indieweb-publisher' ), number_format_i18n( $total ), get_the_date( 'Y' ), $pagination_info );
			} elseif ( is_year() && ( get_the_date( 'Y' ) == date( 'Y' ) ) ) {
				$date_archive_meta = sprintf( _n( 'There is one post published in %2$s.', 'There are %1$s posts published in %2$s (%3$s).', $total, 'indieweb-publisher' ), number_format_i18n( $total ), get_the_date( 'Y' ), $pagination_info );
			} elseif ( is_day() ) {
				$date_archive_meta = sprintf(
					_n( 'There was one post published on %2$s.', 'There were %1$s posts published on %2$s (%3$s).', $total, 'indieweb-publisher' ),
					number_format_i18n( $total ),
					get_the_date(),
					$pagination_info
				);
			} elseif ( is_month() ) {
				$year = get_query_var( 'year' );
				if ( empty( $year ) ) {
					$date_archive_meta = sprintf(
						_n( 'There was one post published in the month of %2$s.', 'There were %1$s posts published in %2$s (%3$s).', $total, 'indieweb-publisher' ),
						number_format_i18n( $total ),
						get_the_date( 'F' ),
						$pagination_info
					);
				} else {
					$date_archive_meta = sprintf(
						_n( 'There was one post published in %2$s %3$s.', 'There were %1$s posts published in %2$s %3$s (%4$s).', $total, 'indieweb-publisher' ),
						number_format_i18n( $total ),
						get_the_date( 'F' ),
						get_the_date( 'Y' ),
						$pagination_info
					);
				}
			}
		}
		$date_archive_meta = wpautop( $date_archive_meta );

		return apply_filters( 'date_archive_meta', '<div class="intro-meta">' . $date_archive_meta . '</div>' );
	}
endif;

if ( ! function_exists( 'indieweb_publisher_min_comments_bottom_comment_button' ) ) :
	/**
	 * Returns the minimum number of comments that must exist for the bottom 'Write a Comment' button to appear
	 */
	function indieweb_publisher_min_comments_bottom_comment_button() {
		return 4;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_min_comments_comment_title' ) ) :
	/**
	 * Returns the minimum number of comments that must exist for the comments title to appear
	 */
	function indieweb_publisher_min_comments_comment_title() {
		return 10;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_hide_comments' ) ) :
	/**
	 * Determines if the comments and comment form should be hidden altogether.
	 * This differs from disabling the comments by also hiding the
	 * "Comments are closed." message and allows for easily overriding this
	 * function in a Child Theme.
	 */
	function indieweb_publisher_hide_comments() {
		return false;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_footer_credits' ) ) :
	/**
	 * Echoes the theme footer credits. Overriding this function in a Child Theme also
	 * applies the changes to Jetpack's Infinite Scroll footer.
	 */
	function indieweb_publisher_footer_credits() {
		return indieweb_publisher_get_footer_credits();
	}
endif;

if ( ! function_exists( '_wp_render_title_tag' ) ) :
	/*
	 * Backwards compatibility for <= WP v4.0.
	 * See https://make.wordpress.org/core/2015/10/20/document-title-in-4-4/
	 */
	function indieweb_publisher_render_title() {
		?>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
		<?php
	}

	add_action( 'wp_head', 'indieweb_publisher_render_title' );
endif;

if ( ! function_exists( 'indieweb_publisher_show_related_tags' ) ) :
	/**
	 * Display a list of all other tags at the bottom of each post.
	 */
	function indieweb_publisher_show_related_tags() {
		if ( get_the_tag_list() ) :

			$tag_list_title = apply_filters( 'indieweb_publisher_tag_list_title', __( 'Related Content by Tag', 'indieweb-publisher' ) );
			$tag_list       = (string) get_the_tag_list( '<ul class="taglist"><li class="taglist-title">' . $tag_list_title . '</li><li class="p-category">', '</li><li class="p-category">', '</li></ul>' );

			printf( '<div id="taglist">%s</div>', $tag_list );

		endif;
	}
endif;

if ( ! function_exists( 'indieweb_publisher_archive_description' ) ) :
	/**
	 * Filters the archive description.
	 */
	function indieweb_publisher_archive_description( $description ) {
		if ( is_category() ) {
			$taxonomy_stats = apply_filters( 'indieweb_publisher_taxonomy_category_stats', indieweb_publisher_taxonomy_archive_stats( 'category' ) );
			if ( ! empty( $description ) ) { // show the description + the taxonomy stats
				return apply_filters( 'category_archive_meta', '<div class="taxonomy-description">' . $description . $taxonomy_stats . '</div>' );
			} else { // there was no description set, so let's just show some stats
				return apply_filters( 'category_archive_meta', '<div class="taxonomy-description">' . $taxonomy_stats . '</div>' );
			}
		} elseif ( is_tag() ) {
			// Get some stats about this taxonomy to include in the description
			$taxonomy_stats = apply_filters( 'indieweb_publisher_taxonomy_tag_stats', indieweb_publisher_taxonomy_archive_stats( 'post_tag' ) );
			if ( ! empty( $description ) ) { // show the description + the taxonomy stats
				return apply_filters( 'tag_archive_meta', '<div class="taxonomy-description">' . $description . $taxonomy_stats . '</div>' );
			} else { // there was description set, so let's just show some stats
				return apply_filters( 'tag_archive_meta', '<div class="taxonomy-description">' . $taxonomy_stats . '</div>' );
			}
		} elseif ( is_day() || is_month() || is_year() ) {
			return indieweb_publisher_date_archive_description();
		}
		return $description;
	}
endif;

add_filter( 'get_the_archive_description', 'indieweb_publisher_archive_description' );

if ( ! function_exists( 'indieweb_publisher_archive_title' ) ) :
	/**
	 * Filters the archive title.
	 */
	function indieweb_publisher_archive_title( $title ) {
		if ( is_category() ) {
			return sprintf( '%s', '<span>' . single_cat_title( '', false ) . '</span>' );
		} elseif ( is_tag() ) {
			return sprintf( '%s', '<span>' . single_tag_title( '', false ) . '</span>' );
		} elseif ( is_author() ) {
			/*
			 Queue the first post, that way we know
			* what author we're dealing with (if that is the case).
			*/
			the_post();
			$title = sprintf( '%s', '<span class="vcard h-card"><a class="url fn n u-url" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
			/*
			 Since we called the_post() above, we need to
			* rewind the loop back to the beginning that way
			* we can run the loop properly, in full.
			*/
			rewind_posts();
			return $title;
		} elseif ( is_day() ) {
			return sprintf( '%s', '<span>' . get_the_date() . '</span>' );
		} elseif ( is_month() ) {
			return sprintf( '%s', '<span>' . get_the_date( 'F Y' ) . '</span>' );
		} elseif ( is_year() ) {
			return sprintf( '%s', '<span>' . get_the_date( 'Y' ) . '</span>' );
		} elseif ( is_tax( 'post_format', 'post-format-aside' ) ) {
			return __( 'Asides', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			return __( 'Galleries', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			return __( 'Images', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			return __( 'Videos', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			return __( 'Quotes', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			return __( 'Links', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			return __( 'Statuses', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			return __( 'Audios', 'indieweb-publisher' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			return __( 'Chats', 'indieweb-publisher' );
		} elseif( is_tax() ) {
			return single_term_title( '', false );
		}
		return $title;
	}
endif;

add_filter( 'get_the_archive_title', 'indieweb_publisher_archive_title' );

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
