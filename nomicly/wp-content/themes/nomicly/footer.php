<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/* A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>

			<!--<div id="site-generator">
				<?php do_action( 'twentyeleven_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentyeleven' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twentyeleven' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'twentyeleven' ), 'WordPress' ); ?></a>
			</div>-->
			<!--<div id="footer-search"><?php get_search_form(); ?></div>-->
	</footer><!-- #colophon -->
	
	<!--social widget-->
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50e34b6d14e77c15"></script>
	<script type="text/javascript">
		var addthis_share = { url_transforms : { shorten: { twitter: 'bitly' } }, shorteners : { bitly : {} } }
	</script>

	<!--end social widget-->
	
	
</div><!-- #page -->

</div><!--end .body-wrap-->
<?php wp_footer(); ?>

</body>
</html>