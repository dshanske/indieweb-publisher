<?php
/**
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
		<?php
		$title = indieweb_publisher_get_the_title();
		if ( ! empty( $title ) ) {
		?>
			<h1 class="entry-title p-name">
				<a class="u-url permalink" href="<?php the_permalink(); ?>" title="<?php echo indieweb_publisher_post_link_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
		<?php } ?>
	</header>
			<?php /* Only show featured image for Standard post and gallery post formats */ ?>
			<?php if ( has_post_thumbnail() && in_array( get_post_format(), array( 'gallery', false ) ) ) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php echo indieweb_publisher_post_thumbnail_link_title(); ?>"><?php the_post_thumbnail( 'indieweb_publisher_post_thumbnail' ); ?></a>
			<?php endif; ?>

			<?php the_content( indieweb_publisher_continue_reading_text() ); ?>
			<?php if ( function_exists( 'wp_pagenavi' ) ) : // WP-PageNavi support ?>

				<?php wp_pagenavi( array( 'type' => 'multipart' ) ); ?>

			<?php else : ?>

				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . __( 'Pages:', 'independent-publisher' ),
						'after'  => '</div>',
					)
				);
				?>

			<?php endif; ?>

	<footer class="entry-meta">

		<?php
		/*
		 Show author name and post categories only when post type == post */
		?>
		<?php if ( 'post' == get_post_type() ) : // post type == post conditional hides category text for Pages on Search ?>
			<?php indieweb_publisher_posted_author_cats(); ?>
		<?php endif; ?>

		<?php
		/* Show post date when show post date option enabled */
		?>
<?php 
		if ( indieweb_publisher_option( 'show_date_entry_meta' ) ) {
			echo indieweb_publisher_get_post_date(); 
		} else if ( empty( $title ) ) {
			printf( '<a class="u-url permalink" href="%1$s" title="%2$s" rel="bookmark"></a>', get_the_permalink(), indieweb_publisher_post_link_title(), $title );
		}
		?>

		<?php $separator = apply_filters( 'indieweb_publisher_entry_meta_separator', '|' ); ?>

		<?php /* Show webmentions link only when post is not password-protected AND pings open AND there are mentions on this post */ ?>
		<?php if ( ! post_password_required() && pings_open() && indieweb_publisher_comment_count_mentions() ) : ?>
			<?php $mention_count = indieweb_publisher_comment_count_mentions(); ?>
			<?php $mention_label = ( indieweb_publisher_comment_count_mentions() > 1 ? __( 'Webmentions', 'independent-publisher' ) : __( 'Webmention', 'independent-publisher' ) ); ?>
			<span class="mentions-link"><a href="<?php the_permalink(); ?>#webmentions"><?php echo $mention_count . ' ' . $mention_label; ?></a></span><span class="sep"><?php echo ( comments_open() && ! indieweb_publisher_hide_comments() ) ? ' ' . $separator : ''; ?></span>
		<?php endif; ?>

		<?php /* Show comments link only when post is not password-protected AND comments are enabled on this post */ ?>
		<?php if ( ! post_password_required() && comments_open() && ! indieweb_publisher_hide_comments() ) : ?>
			<span class="comments-link"><?php comments_popup_link( __( 'Comment', 'independent-publisher' ), __( '1 Comment', 'independent-publisher' ), __( '% Comments', 'independent-publisher' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'independent-publisher' ), '<span class="sep"> ' . $separator . ' </span> <span class="edit-link">', '</span>' ); ?>

	</footer>
	<!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
