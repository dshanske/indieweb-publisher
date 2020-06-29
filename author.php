<?php
/**
 * The template for displaying Author archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

				<?php
				/*
				Queue the first post, that way we know
				* what author we're dealing with (if that is the case).
				*
				* We reset this later so we can run the loop
				* properly with a call to rewind_posts().
				*/
				the_post();
				?>

				<header class="archive-header">
					<h1 class="archive-title"><?php printf( '%s', '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
				</header><!-- .archive-header -->

				<?php
				/*
				Since we called the_post() above, we need to
				* rewind the loop back to the beginning that way
				* we can run the loop properly, in full.
				*/
				rewind_posts();
				?>

				<?php get_template_part( 'template-parts/author-bio' ); ?>

				<?php /* The loop */ ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php
					get_template_part( 'template-parts/content', indieweb_publisher_get_post_kind() );
					?>
				<?php endwhile; ?>

				<?php indieweb_publisher_the_posts_navigation(); ?>

			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>

		</div>
		<!-- #content -->
	</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
