<?php
/**
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">
	<?php if ( has_post_thumbnail() && ! indieweb_publisher_has_full_width_featured_image() ) : ?>
		<?php the_post_thumbnail( 'indieweb_publisher_post_thumbnail', array( 'itemprop' => 'image' ) ); ?>
	<?php endif; ?>
	<header>
		<?php if ( indieweb_publisher_post_has_post_cover_title() ) : ?>
			<h2 class="entry-title-meta">
	  <span class="entry-title-meta-author">
			<?php
			if ( ! indieweb_publisher_categorized_blog() ) {
				echo indieweb_publisher_entry_meta_author_prefix() . ' ';
			}
			indieweb_publisher_posted_author()
			?>
dddd
		</span>
				<?php
				if ( get_post_meta( get_the_ID(), 'indieweb_publisher_primary_category', true ) ) { // check for a custom field named 'indieweb_publisher_primary_category'
					echo indieweb_publisher_entry_meta_category_prefix() . ' ' . get_post_meta( get_the_ID(), 'indieweb_publisher_primary_category', true ); // show the primary category as set in ACF
				} elseif ( indieweb_publisher_categorized_blog() ) {
					echo indieweb_publisher_entry_meta_category_prefix() . ' ' . indieweb_publisher_post_categories();
				}
				?>
				<span class="entry-title-meta-post-date">
				<span class="sep"> <?php echo apply_filters( 'indieweb_publisher_entry_meta_separator', '|' ); ?> </span>
					<?php indieweb_publisher_posted_on_date(); ?>
			</span>
				<?php do_action( 'indieweb_publisher_entry_title_meta', $separator = ' | ' ); ?>
			</h2>
		<?php else : ?>
			<?php if ( ! indieweb_publisher_option( 'show_author_card' ) ) { ?>
			<h2 class="entry-title-meta">
			<span class="entry-title-meta-author">
				<?php
				if ( ! indieweb_publisher_categorized_blog() ) {
					echo indieweb_publisher_entry_meta_author_prefix() . ' ';
				}
				indieweb_publisher_posted_author()
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
			</h2>
			<?php 
			}
				$title = indieweb_publisher_get_the_title();
			if ( ! empty( $title ) ) {
				?>
				<h1 class="entry-title p-name" itemprop="name"><?php echo $title; ?></h1>
				<?php
			}
		endif;
?>
	</header>
	
	<?php the_content(); ?>

	<?php indieweb_publisher_posted_author_bottom_card(); ?>

	<footer class="entry-meta">

		<?php if ( function_exists( 'wp_pagenavi' ) ) : // WP-PageNavi support ?>

			<?php wp_pagenavi( array( 'type' => 'multipart' ) ); ?>

		<?php else : ?>

			<?php
			wp_link_pages(
				array(
					'before'           => '<div class="page-links-next-prev">',
					'after'            => '</div>',
					'nextpagelink'     => '<button class="next-page-nav">' . __( 'Next page &rarr;', 'indieweb-publisher' ) . '</button>',
					'previouspagelink' => '<button class="previous-page-nav">' . __( '&larr; Previous page', 'indieweb-publisher' ) . '</button>',
					'next_or_number'   => 'next',
				)
			);
			?>
			<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'independent-publisher' ),
					'after'  => '</div>',
				)
			);
			?>
		<?php endif; ?>
		<?php do_action( 'indieweb_publisher_entry_meta_top' ); ?>

		<?php if ( comments_open() && ! indieweb_publisher_hide_comments() ) : ?>
			<div id="share-comment-button">
				<button>
					<i class="share-comment-icon"></i><?php echo indieweb_publisher_comments_call_to_action_text(); ?>
				</button>
			</div>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'independent-publisher' ), '<span class="edit-link">', '</span>' ); ?>
	</footer>
	<!-- .entry-meta -->

</article><!-- #post-<?php the_ID(); ?> -->
