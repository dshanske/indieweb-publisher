<?php
/**
 * A clean one-column page template without the navigation bar or site info
 *
 * Template Name: One-Column, No Nav Bar
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

get_header(); ?>

	<style type="text/css">
		#masthead {
			display: none;
		}
	</style>

	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php
			while ( have_posts() ) :
				the_post();
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'indieweb_publisher_post_thumbnail' ); ?>
					<?php endif; ?>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>
					<!-- .entry-header -->

					<?php the_content(); ?>
					<footer class="entry-meta">>

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
									'before' => '<div class="page-links">' . __( 'Pages:', 'indieweb-publisher' ),
									'after'  => '</div>',
								)
							);
							?>

						<?php endif; ?>

					<?php edit_post_link( __( 'Edit', 'indieweb-publisher' ), '<span class="edit-link">', '</span>' ); ?>
					</footer> <!-- .entry-meta --!>
				</article><!-- #post-<?php the_ID(); ?> -->

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

		</div>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_footer(); ?>
