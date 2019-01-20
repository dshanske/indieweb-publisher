<?php
/**
 * A clean one-column page template without the navigation bar or site info
 *
 * Template Name: H-Card
 *
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

<?php
					if ( class_exists( 'HCard_User' ) ) {
						echo HCard_User::hcard(
						     get_the_author_meta( 'ID' ),
						     array(
							     'me' => true,
						     )
						);
					} else {
						indieweb_publisher_posted_author_card();
					}
				?>
				<article id="post-<?php the_ID(); ?>">
					<?php the_content(); ?>
					<footer class="entry-meta">>
					<?php edit_post_link( __( 'Edit', 'indieweb-publisher' ), '<span class="edit-link">', '</span>' ); ?>
					</footer> <!-- .entry-meta --!>
				</article><!-- #post-<?php the_ID(); ?> -->


			<?php endwhile; // end of the loop. ?>

		</div>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php 
get_sidebar();
get_footer(); 
?>
