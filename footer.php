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
	<div class="site-info">
		<?php echo indieweb_publisher_footer_credits(); ?>
		<?php if ( function_exists( 'the_privacy_policy_link' ) ) {
			the_privacy_policy_link( ' | ', '<span role="separator" aria-hidden="true"></span>' );
		} ?>
	</div>
	<!-- .site-info -->
</footer><!-- #colophon .site-footer -->
</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>

</body>
</html>
