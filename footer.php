<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Indieweb Publisher
 * @since   Indieweb Publisher 1.0
 */
?>

</div><!-- #main .site-main -->

<footer id="colophon" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter" role="contentinfo">
			<?php if ( is_active_sidebar( 'sidebar-2' ) || is_active_sidebar( 'sidebar-3' ) || is_active_sidebar( 'sidebar-4' ) ) : ?>
				<div class="footer-widget">
					<?php if ( is_active_sidebar( 'sidebar-2' ) ) : ?>
						<div class="widget-area">
							<?php dynamic_sidebar( 'sidebar-2' ); ?>
						</div><!-- .widget-area -->
					<?php endif; // is_active_sidebar ?>
				</div><!-- .footer-widgets -->
<?php endif; ?>
	<div class="site-info">
		<?php echo indieweb_publisher_footer_credits(); ?>
		<?php
		if ( function_exists( 'the_privacy_policy_link' ) ) {
			the_privacy_policy_link( ' | ', '<span role="separator" aria-hidden="true"></span>' );
		}
		?>
	</div>
	<!-- .site-info -->
</footer><!-- #colophon .site-footer -->
</div><!-- #page .site -->

<?php wp_footer(); ?>

</body>
</html>
