<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="content" class="site-content">

			<?php if ( have_posts() ) : ?>

				<header class="page-header">
					<?php the_archive_thumbnail(); ?>
					<?php the_archive_title( '<h1 class="page-title p-name">', '</h1>' ); ?>
					<?php the_archive_description( '<div class="archive-description p-summary">', '</div>' ); ?>
					<?php indieweb_publisher_the_posts_navigation(); ?>
				</header><!-- .page-header -->

				<?php /* Start the Loop */ ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>

					<?php
					/*
					 Include the Post-Format-specific template for the content.
					 * If you want to overload this in a child theme then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content', indieweb_publisher_get_post_kind() );
					?>

				<?php endwhile; ?>

				<?php indieweb_publisher_the_posts_navigation(); ?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</main>
		<!-- #content .site-content -->
	</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
