<?php
/**
 * A clean one-column page template without the navigation bar or site info
 *
 * Template Name: Archive Page
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
				<!-- .entry-header -->

				<section>
			<?php the_content(); ?>

			<?php if ( ! dynamic_sidebar( 'archive-page' ) ) : ?>

				<?php the_widget( 'WP_Widget_Recent_Posts', array( 'number' => 15 ) ); ?>

				<?php if ( indieweb_publisher_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
						<div class="widget">
							<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'indieweb-publisher' ); ?></h2>
							<ul>
					<?php
					wp_list_categories(
						array(
							'orderby'    => 'count',
							'order'      => 'DESC',
							'show_count' => 1,
							'title_li'   => '',
							'number'     => 10,
						)
					);
					?>
							</ul>
						</div>
				<?php endif; ?>

						<div class="widget">
							<h2 class="widget-title"><?php esc_html_e( 'Yearly Archives', 'indieweb-publisher' ); ?></h2>
							<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
								<option value=""><?php esc_html_e( 'Select Year', 'indieweb-publisher' ); ?></option>
				<?php
				wp_get_archives(
					array(
						'type'   => 'yearly',
						'format' => 'option',
					)
				);
				?>
							</select>
						</div>

						<div class="widget">
							<h2 class="widget-title"><?php echo __( 'Monthly Archives', 'indieweb-publisher' ); ?></h2>
							<select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;">
								<option value=""><?php esc_html_e( 'Select Month', 'indieweb-publisher' ); ?></option>
				<?php
				wp_get_archives(
					array(
						'type'   => 'monthly',
						'format' => 'option',
					)
				);
				?>
							</select>
						</div>

						<div class="widget">
							<h2 class="widget-title"><?php esc_html_e( 'Search', 'indieweb-publisher' ); ?></h2>
				<?php get_search_form(); ?>
						</div>

				<?php the_widget( 'WP_Widget_Tag_Cloud', array( 'title' => __( 'Explore by Tag', 'indieweb-publisher' ) ) ); ?>

			<?php endif; ?>

				</section>

			<?php edit_post_link( __( 'Edit', 'indieweb-publisher' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; // end of the loop. ?>

	</div>
	<!-- #content .site-content -->
</div><!-- #primary .content-area -->

<?php get_footer(); ?>
