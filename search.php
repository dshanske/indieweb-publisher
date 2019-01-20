<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

				<?php $search_stats = apply_filters( 'indieweb_publisher_search_stats', indieweb_publisher_search_stats() ); ?>

				<header class="page-header">
					<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'indieweb-publisher' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					<?php echo apply_filters( 'search_meta', '<div class="search-stats-description">' . $search_stats . '</div>' ); ?>
					<?php indieweb_publisher_the_posts_navigation(); ?>
				</header><!-- .page-header -->

				<?php /* Start the Loop */ ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>

					<?php get_template_part( 'template-parts/content', 'search' ); ?>

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
